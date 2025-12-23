<?php

namespace App\Livewire\Public;

use App\Models\Product;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

class ProductDetail extends Component
{
    public Product $product;

    /**
     * Mount the component with product slug
     * 
     * @param string $slug
     * @return void
     */
    public function mount(string $slug): void
    {
        // Query product by slug with public visibility check, optimized with caching and select
        $cacheKey = "product:detail:slug:{$slug}";

        $this->product = \Illuminate\Support\Facades\Cache::remember($cacheKey, 300, function () use ($slug) {
            return Product::select([
                    'id', 'name', 'slug', 'sku', 'price', 'stock', 'min_stock', 
                    'category', 'description', 'image', 'is_featured', 'status', 'is_public'
                ])
                ->where('slug', $slug)
                ->where('is_public', true)
                ->firstOrFail();
        });
    }

    #[Layout('layouts.public')]
    #[Title('Detail Produk')]
    public function render()
    {
        return view('livewire.public.product-detail');
    }
}
