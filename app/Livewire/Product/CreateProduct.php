<?php

namespace App\Livewire\Product;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Product;
use App\Services\ProductImageService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Tambah Produk')]
class CreateProduct extends Component
{
    use WithFileUploads;

    public $name = '';
    public $sku = '';
    public $price = '';
    public $cost_price = '';
    public $stock = 0;
    public $min_stock = 5;
    public $category = '';
    public $description = '';
    public $status = 'active';
    public $image;
    public $imagePreview = null;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:50|unique:products,sku',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'category' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'image' => 'nullable|image|max:5120|mimes:jpg,jpeg,png,webp,gif',
        ];
    }

    protected $messages = [
        'image.max' => 'Ukuran gambar maksimal 5MB.',
        'image.mimes' => 'Format gambar harus JPG, PNG, WebP, atau GIF.',
        'image.image' => 'File harus berupa gambar.',
        'cost_price.required' => 'Harga beli wajib diisi.',
        'cost_price.min' => 'Harga beli tidak boleh negatif.',
    ];

    public function updatedImage()
    {
        $this->validateOnly('image');
        
        if ($this->image) {
            $this->imagePreview = $this->image->temporaryUrl();
        }
    }

    public function removeImage()
    {
        $this->image = null;
        $this->imagePreview = null;
    }

    public function save()
    {
        $this->validate();

        $imagePath = null;

        // Process image upload
        if ($this->image) {
            try {
                $imageService = app(ProductImageService::class);
                $imagePath = $imageService->upload($this->image);
            } catch (\Exception $e) {
                $this->dispatch('alert', type: 'error', message: 'Gagal upload gambar: ' . $e->getMessage());
                return;
            }
        }

        Product::create([
            'name' => $this->name,
            'sku' => $this->sku ?: null,
            'price' => $this->price,
            'cost_price' => $this->cost_price,
            'stock' => $this->stock,
            'min_stock' => $this->min_stock,
            'category' => $this->category,
            'description' => $this->description,
            'status' => $this->status,
            'image' => $imagePath,
        ]);

        session()->flash('message', 'Produk berhasil ditambahkan.');

        return redirect()->route('admin.products.index');
    }

    public function render()
    {
        return view('livewire.product.create-product');
    }
}
