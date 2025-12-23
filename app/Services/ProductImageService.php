<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ProductImageService
{
    // Image sizes for different use cases
    private const SIZES = [
        'thumbnail' => ['width' => 150, 'height' => 150, 'quality' => 70],
        'medium' => ['width' => 400, 'height' => 400, 'quality' => 80],
        'large' => ['width' => 800, 'height' => 800, 'quality' => 85],
    ];

    private const DISK = 'public';
    private const BASE_PATH = 'products';
    private const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB
    private const ALLOWED_MIMES = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

    /**
     * Upload and process product image
     * Returns the stored image path (without size variants)
     */
    public function upload(UploadedFile $file, ?string $oldImagePath = null): string
    {
        $this->validateFile($file);

        // Generate unique filename
        $filename = $this->generateFilename($file);
        $basePath = self::BASE_PATH . '/' . date('Y/m');

        // Delete old image if exists
        if ($oldImagePath) {
            $this->delete($oldImagePath);
        }

        // Store original (optimized)
        $originalPath = $this->storeOptimizedOriginal($file, $basePath, $filename);

        // Generate all size variants in background-friendly way
        $this->generateVariants($originalPath);

        return $originalPath;
    }

    /**
     * Get image URL with specific size
     * Uses cache for performance
     */
    public function getUrl(?string $path, string $size = 'medium'): ?string
    {
        if (empty($path)) {
            return null;
        }

        // Check if variant exists, generate if not
        $variantPath = $this->getVariantPath($path, $size);
        
        $cacheKey = 'product_img_' . md5($variantPath);
        
        return Cache::remember($cacheKey, 3600, function () use ($path, $variantPath, $size) {
            if (Storage::disk(self::DISK)->exists($variantPath)) {
                return Storage::disk(self::DISK)->url($variantPath);
            }

            // Generate variant on-demand if missing
            if (Storage::disk(self::DISK)->exists($path)) {
                $this->generateSingleVariant($path, $size);
                if (Storage::disk(self::DISK)->exists($variantPath)) {
                    return Storage::disk(self::DISK)->url($variantPath);
                }
            }

            // Fallback to original
            if (Storage::disk(self::DISK)->exists($path)) {
                return Storage::disk(self::DISK)->url($path);
            }

            return null;
        });
    }

    /**
     * Get thumbnail URL (shortcut)
     */
    public function getThumbnailUrl(?string $path): ?string
    {
        return $this->getUrl($path, 'thumbnail');
    }

    /**
     * Delete image and all variants
     */
    public function delete(?string $path): bool
    {
        if (empty($path)) {
            return false;
        }

        $deleted = false;

        // Delete original
        if (Storage::disk(self::DISK)->exists($path)) {
            Storage::disk(self::DISK)->delete($path);
            $deleted = true;
        }

        // Delete all variants
        foreach (array_keys(self::SIZES) as $size) {
            $variantPath = $this->getVariantPath($path, $size);
            if (Storage::disk(self::DISK)->exists($variantPath)) {
                Storage::disk(self::DISK)->delete($variantPath);
            }
            // Clear cache
            Cache::forget('product_img_' . md5($variantPath));
        }

        return $deleted;
    }

    /**
     * Validate uploaded file
     */
    private function validateFile(UploadedFile $file): void
    {
        if ($file->getSize() > self::MAX_FILE_SIZE) {
            throw new \InvalidArgumentException('File terlalu besar. Maksimal 5MB.');
        }

        if (!in_array($file->getMimeType(), self::ALLOWED_MIMES)) {
            throw new \InvalidArgumentException('Format file tidak didukung. Gunakan JPG, PNG, WebP, atau GIF.');
        }
    }

    /**
     * Generate unique filename
     */
    private function generateFilename(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension() ?: 'jpg';
        return Str::uuid() . '.' . strtolower($extension);
    }

    /**
     * Store optimized original image
     */
    private function storeOptimizedOriginal(UploadedFile $file, string $basePath, string $filename): string
    {
        $fullPath = $basePath . '/' . $filename;
        $tempPath = $file->getRealPath();

        // Get image info
        $imageInfo = getimagesize($tempPath);
        if ($imageInfo === false) {
            throw new \InvalidArgumentException('File bukan gambar yang valid.');
        }

        // Create source image
        $sourceImage = $this->createImageFromFile($tempPath, $imageInfo[2]);
        if (!$sourceImage) {
            // Fallback: store as-is if can't process
            Storage::disk(self::DISK)->putFileAs($basePath, $file, $filename);
            return $fullPath;
        }

        $origWidth = imagesx($sourceImage);
        $origHeight = imagesy($sourceImage);

        // Resize if too large (max 1200px)
        $maxDimension = 1200;
        if ($origWidth > $maxDimension || $origHeight > $maxDimension) {
            $ratio = min($maxDimension / $origWidth, $maxDimension / $origHeight);
            $newWidth = (int) ($origWidth * $ratio);
            $newHeight = (int) ($origHeight * $ratio);

            $resized = imagecreatetruecolor($newWidth, $newHeight);
            $this->preserveTransparency($resized);
            imagecopyresampled($resized, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);
            imagedestroy($sourceImage);
            $sourceImage = $resized;
        }

        // Ensure directory exists
        $storagePath = Storage::disk(self::DISK)->path($basePath);
        if (!is_dir($storagePath)) {
            mkdir($storagePath, 0755, true);
        }

        // Save as WebP for better compression (or original format if WebP not supported)
        $savePath = Storage::disk(self::DISK)->path($fullPath);
        
        if (function_exists('imagewebp')) {
            // Change extension to webp
            $webpPath = preg_replace('/\.[^.]+$/', '.webp', $fullPath);
            $webpSavePath = Storage::disk(self::DISK)->path($webpPath);
            imagewebp($sourceImage, $webpSavePath, 85);
            $fullPath = $webpPath;
        } else {
            imagejpeg($sourceImage, $savePath, 85);
        }

        imagedestroy($sourceImage);

        return $fullPath;
    }

    /**
     * Generate all size variants
     */
    private function generateVariants(string $originalPath): void
    {
        foreach (array_keys(self::SIZES) as $size) {
            $this->generateSingleVariant($originalPath, $size);
        }
    }

    /**
     * Generate single size variant
     */
    private function generateSingleVariant(string $originalPath, string $size): bool
    {
        if (!isset(self::SIZES[$size])) {
            return false;
        }

        $config = self::SIZES[$size];
        $variantPath = $this->getVariantPath($originalPath, $size);

        $fullOriginalPath = Storage::disk(self::DISK)->path($originalPath);
        if (!file_exists($fullOriginalPath)) {
            return false;
        }

        $imageInfo = getimagesize($fullOriginalPath);
        if ($imageInfo === false) {
            return false;
        }

        $sourceImage = $this->createImageFromFile($fullOriginalPath, $imageInfo[2]);
        if (!$sourceImage) {
            return false;
        }

        $origWidth = imagesx($sourceImage);
        $origHeight = imagesy($sourceImage);
        $targetWidth = $config['width'];
        $targetHeight = $config['height'];

        // Calculate dimensions maintaining aspect ratio (contain mode)
        $ratio = min($targetWidth / $origWidth, $targetHeight / $origHeight);
        $newWidth = (int) ($origWidth * $ratio);
        $newHeight = (int) ($origHeight * $ratio);

        // Create variant
        $variant = imagecreatetruecolor($newWidth, $newHeight);
        $this->preserveTransparency($variant);
        imagecopyresampled($variant, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);

        // Ensure directory exists
        $variantDir = dirname(Storage::disk(self::DISK)->path($variantPath));
        if (!is_dir($variantDir)) {
            mkdir($variantDir, 0755, true);
        }

        // Save as WebP
        $savePath = Storage::disk(self::DISK)->path($variantPath);
        imagewebp($variant, $savePath, $config['quality']);

        imagedestroy($sourceImage);
        imagedestroy($variant);

        return true;
    }

    /**
     * Get variant path for a size
     */
    private function getVariantPath(string $originalPath, string $size): string
    {
        $pathInfo = pathinfo($originalPath);
        $directory = $pathInfo['dirname'];
        $filename = $pathInfo['filename'];

        return "{$directory}/{$size}/{$filename}.webp";
    }

    /**
     * Create GD image from file
     */
    private function createImageFromFile(string $path, int $type)
    {
        return match ($type) {
            IMAGETYPE_JPEG => imagecreatefromjpeg($path),
            IMAGETYPE_PNG => imagecreatefrompng($path),
            IMAGETYPE_GIF => imagecreatefromgif($path),
            IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? imagecreatefromwebp($path) : false,
            default => false,
        };
    }

    /**
     * Preserve transparency for PNG/GIF
     */
    private function preserveTransparency($image): void
    {
        imagealphablending($image, false);
        imagesavealpha($image, true);
        $transparent = imagecolorallocatealpha($image, 255, 255, 255, 127);
        imagefilledrectangle($image, 0, 0, imagesx($image), imagesy($image), $transparent);
    }
}
