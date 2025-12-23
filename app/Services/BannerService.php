<?php

namespace App\Services;

use App\Models\Banner;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;

class BannerService
{
    /**
     * Store a new banner
     *
     * @param array $data
     * @param UploadedFile $image
     * @return Banner
     */
    public function store(array $data, UploadedFile $image): Banner
    {
        return DB::transaction(function () use ($data, $image) {
            // Process the image and get paths
            $imagePaths = $this->processImage($image);
            
            // Create banner record
            $banner = Banner::create([
                'title' => $data['title'] ?? null,
                'image_path' => $imagePaths['main'],
                'priority' => $data['priority'] ?? 0,
                'is_active' => $data['is_active'] ?? true,
                'created_by' => auth()->id(),
            ]);

            return $banner;
        });
    }

    /**
     * Update an existing banner
     *
     * @param Banner $banner
     * @param array $data
     * @param UploadedFile|null $image
     * @return Banner
     */
    public function update(Banner $banner, array $data, ?UploadedFile $image = null): Banner
    {
        return DB::transaction(function () use ($banner, $data, $image) {
            $updateData = [
                'title' => $data['title'] ?? $banner->title,
                'priority' => $data['priority'] ?? $banner->priority,
                'is_active' => $data['is_active'] ?? $banner->is_active,
            ];

            // If new image is provided, process it and delete old images
            if ($image) {
                // Delete old image files
                $this->deleteImageFiles($banner->image_path);
                
                // Process new image
                $imagePaths = $this->processImage($image);
                $updateData['image_path'] = $imagePaths['main'];
            }

            $banner->update($updateData);

            return $banner->fresh();
        });
    }

    /**
     * Delete a banner and its associated images
     *
     * @param Banner $banner
     * @return bool
     */
    public function delete(Banner $banner): bool
    {
        return DB::transaction(function () use ($banner) {
            // Delete image files
            $this->deleteImageFiles($banner->image_path);
            
            // Delete banner record
            return $banner->delete();
        });
    }

    /**
     * Toggle banner active status
     *
     * @param Banner $banner
     * @return Banner
     */
    public function toggleStatus(Banner $banner): Banner
    {
        $banner->update([
            'is_active' => !$banner->is_active,
        ]);

        return $banner->fresh();
    }

    /**
     * Process uploaded image - resize, compress, and create responsive variants
     *
     * @param UploadedFile $image
     * @return array Array with paths to different image sizes
     */
    public function processImage(UploadedFile $image): array
    {
        // Generate unique filename
        $uuid = Str::uuid();
        $extension = 'jpg'; // Always convert to JPG for consistency
        
        // Create directory if it doesn't exist
        $directory = 'banners';
        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
        }

        // Get image info
        $imageInfo = getimagesize($image->getPathname());
        $originalWidth = $imageInfo[0];
        $originalHeight = $imageInfo[1];
        $mimeType = $imageInfo['mime'];

        // Create image resource from uploaded file
        $sourceImage = $this->createImageResource($image->getPathname(), $mimeType);
        
        if (!$sourceImage) {
            throw new \Exception('Failed to process image. Invalid image format.');
        }

        // Define responsive sizes
        $sizes = [
            'main' => 1920,
            'tablet' => 768,
            'mobile' => 480,
        ];

        $paths = [];

        foreach ($sizes as $key => $maxWidth) {
            // Calculate new dimensions maintaining aspect ratio
            $newDimensions = $this->calculateDimensions($originalWidth, $originalHeight, $maxWidth);
            
            // Create resized image
            $resizedImage = imagecreatetruecolor($newDimensions['width'], $newDimensions['height']);
            
            // Preserve transparency for PNG sources
            if ($mimeType === 'image/png') {
                imagealphablending($resizedImage, false);
                imagesavealpha($resizedImage, true);
                $transparent = imagecolorallocatealpha($resizedImage, 255, 255, 255, 127);
                imagefill($resizedImage, 0, 0, $transparent);
            }

            // Resize the image
            imagecopyresampled(
                $resizedImage,
                $sourceImage,
                0, 0, 0, 0,
                $newDimensions['width'],
                $newDimensions['height'],
                $originalWidth,
                $originalHeight
            );

            // Generate filename
            $filename = "{$uuid}_{$maxWidth}.{$extension}";
            $filePath = "{$directory}/{$filename}";
            $fullPath = Storage::disk('public')->path($filePath);

            // Save as JPEG with 80% quality
            imagejpeg($resizedImage, $fullPath, 80);
            
            // Clean up memory
            imagedestroy($resizedImage);

            // Store path (use main size as the primary path)
            if ($key === 'main') {
                $paths['main'] = $filePath;
            }
            $paths[$key] = $filePath;
        }

        // Clean up source image
        imagedestroy($sourceImage);

        return $paths;
    }

    /**
     * Get active banners ordered by priority
     *
     * @return Collection
     */
    public function getActiveBanners(): Collection
    {
        return Banner::active()->ordered()->get();
    }

    /**
     * Create image resource from file path based on MIME type
     *
     * @param string $filePath
     * @param string $mimeType
     * @return resource|false
     */
    protected function createImageResource(string $filePath, string $mimeType)
    {
        switch ($mimeType) {
            case 'image/jpeg':
                return imagecreatefromjpeg($filePath);
            case 'image/png':
                return imagecreatefrompng($filePath);
            case 'image/gif':
                return imagecreatefromgif($filePath);
            default:
                return false;
        }
    }

    /**
     * Calculate new dimensions maintaining aspect ratio
     *
     * @param int $originalWidth
     * @param int $originalHeight
     * @param int $maxWidth
     * @return array
     */
    protected function calculateDimensions(int $originalWidth, int $originalHeight, int $maxWidth): array
    {
        // If original width is smaller than max, keep original dimensions
        if ($originalWidth <= $maxWidth) {
            return [
                'width' => $originalWidth,
                'height' => $originalHeight,
            ];
        }

        // Calculate new height maintaining aspect ratio
        $aspectRatio = $originalHeight / $originalWidth;
        $newWidth = $maxWidth;
        $newHeight = (int) round($maxWidth * $aspectRatio);

        return [
            'width' => $newWidth,
            'height' => $newHeight,
        ];
    }

    /**
     * Delete image files for a given main image path
     *
     * @param string $mainImagePath
     * @return void
     */
    protected function deleteImageFiles(string $mainImagePath): void
    {
        // Extract UUID from main image path
        $pathInfo = pathinfo($mainImagePath);
        $filename = $pathInfo['filename'];
        
        // Extract UUID (everything before the last underscore)
        $lastUnderscorePos = strrpos($filename, '_');
        if ($lastUnderscorePos !== false) {
            $uuid = substr($filename, 0, $lastUnderscorePos);
            
            // Delete all variants
            $sizes = [1920, 768, 480];
            foreach ($sizes as $size) {
                $filePath = "banners/{$uuid}_{$size}.jpg";
                if (Storage::disk('public')->exists($filePath)) {
                    Storage::disk('public')->delete($filePath);
                }
            }
        }
    }
}