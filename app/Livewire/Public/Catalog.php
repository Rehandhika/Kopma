<?php

namespace App\Livewire\Public;

use App\Models\Product;
use App\Models\Banner;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Cache;

class Catalog extends Component
{
    use WithPagination;

    public string $search = '';
    public string $category = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'category' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCategory()
    {
        $this->resetPage();
    }

    public function render()
    {
        $page = $this->getPage();
        $cacheKey = "products:public:page:{$page}:search:{$this->search}:category:{$this->category}";
        
        $products = Cache::remember($cacheKey, 300, function () {
            return Product::query()
                ->public()
                ->active()
                ->when($this->search, function ($query) {
                    $query->where(function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%')
                          ->orWhere('description', 'like', '%' . $this->search . '%')
                          ->orWhere('sku', 'like', '%' . $this->search . '%');
                    });
                })
                ->when($this->category, function ($query) {
                    $query->where('category', $this->category);
                })
                ->ordered()
                ->paginate(12);
        });

        $categories = Cache::remember('products:categories', 300, function () {
            return Product::query()
                ->public()
                ->active()
                ->whereNotNull('category')
                ->distinct()
                ->pluck('category')
                ->sort()
                ->values();
        });

        // Cache banner query for performance (5 minutes)
        $banners = Cache::remember('banners:active', 300, function () {
            return Banner::query()
                ->active()
                ->ordered()
                ->get();
        });

        return view('livewire.public.catalog', [
            'products' => $products,
            'categories' => $categories,
            'banners' => $banners,
        ])->layout('layouts.public');
    }
}
