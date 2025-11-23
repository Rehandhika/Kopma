<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class SetupProductsForPublicCatalog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:setup-public-catalog';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate slugs and set default image URLs for products';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Setting up products for public catalog...');
        
        // Generate slugs for products without slugs
        $this->info('Generating slugs for products...');
        $productsWithoutSlugs = Product::whereNull('slug')->orWhere('slug', '')->get();
        
        if ($productsWithoutSlugs->isEmpty()) {
            $this->info('All products already have slugs.');
        } else {
            $slugCount = 0;
            foreach ($productsWithoutSlugs as $product) {
                $slug = Str::slug($product->name);
                $originalSlug = $slug;
                $count = 1;
                
                // Ensure unique slug
                while (Product::where('slug', $slug)->where('id', '!=', $product->id)->exists()) {
                    $slug = $originalSlug . '-' . $count;
                    $count++;
                }
                
                $product->slug = $slug;
                $product->save();
                $slugCount++;
                
                $this->line("  - Generated slug for '{$product->name}': {$slug}");
            }
            $this->info("Generated {$slugCount} slugs.");
        }
        
        // Set default image URLs for products without images
        $this->info('Setting default image URLs for products...');
        $productsWithoutImages = Product::whereNull('image_url')->orWhere('image_url', '')->get();
        
        if ($productsWithoutImages->isEmpty()) {
            $this->info('All products already have image URLs.');
        } else {
            $imageCount = 0;
            $defaultImageUrl = 'https://via.placeholder.com/400x400.png?text=No+Image';
            
            foreach ($productsWithoutImages as $product) {
                $product->image_url = $defaultImageUrl;
                $product->save();
                $imageCount++;
                
                $this->line("  - Set default image for '{$product->name}'");
            }
            $this->info("Set default images for {$imageCount} products.");
        }
        
        $this->newLine();
        $this->info('✓ Products setup completed successfully!');
        
        return Command::SUCCESS;
    }
}
