<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Services\ProductImageService;

class GenerateProductThumbnails extends Command
{
    protected $signature = 'products:generate-thumbnails {--force : Regenerate all thumbnails}';
    protected $description = 'Generate thumbnails for all product images';

    public function handle(): int
    {
        $this->info('Starting thumbnail generation...');

        $products = Product::whereNotNull('image')->get();
        $imageService = app(ProductImageService::class);

        $bar = $this->output->createProgressBar($products->count());
        $bar->start();

        $success = 0;
        $failed = 0;

        foreach ($products as $product) {
            try {
                // This will generate all variants if they don't exist
                $imageService->getUrl($product->image, 'thumbnail');
                $imageService->getUrl($product->image, 'medium');
                $imageService->getUrl($product->image, 'large');
                $success++;
            } catch (\Exception $e) {
                $failed++;
                $this->newLine();
                $this->error("Failed for product #{$product->id}: {$e->getMessage()}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("Completed! Success: {$success}, Failed: {$failed}");

        return Command::SUCCESS;
    }
}
