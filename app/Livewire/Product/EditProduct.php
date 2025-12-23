<?php

namespace App\Livewire\Product;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Product;
use App\Services\ProductImageService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Validation\Rule;

#[Layout('layouts.app')]
#[Title('Edit Produk')]
class EditProduct extends Component
{
    use WithFileUploads;

    public Product $product;

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
    public $existingImage = null;

    public function mount(Product $product)
    {
        $this->product = $product;
        $this->name = $product->name;
        $this->sku = $product->sku;
        $this->price = $product->price;
        $this->cost_price = $product->cost_price;
        $this->stock = $product->stock;
        $this->min_stock = $product->min_stock;
        $this->category = $product->category;
        $this->description = $product->description;
        $this->status = $product->status;
        $this->existingImage = $product->image_medium_url;
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'sku' => ['nullable', 'string', 'max:50', Rule::unique('products', 'sku')->ignore($this->product->id)],
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

    public function deleteExistingImage()
    {
        if ($this->product->image) {
            $imageService = app(ProductImageService::class);
            $imageService->delete($this->product->image);
            
            $this->product->update(['image' => null]);
            $this->existingImage = null;
            
            $this->dispatch('alert', type: 'success', message: 'Gambar berhasil dihapus.');
        }
    }

    public function save()
    {
        $this->validate();

        $imagePath = $this->product->image;

        // Process new image upload
        if ($this->image) {
            try {
                $imageService = app(ProductImageService::class);
                $imagePath = $imageService->upload($this->image, $this->product->image);
            } catch (\Exception $e) {
                $this->dispatch('alert', type: 'error', message: 'Gagal upload gambar: ' . $e->getMessage());
                return;
            }
        }

        $this->product->update([
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

        session()->flash('message', 'Produk berhasil diperbarui.');

        return redirect()->route('admin.products.index');
    }

    public function render()
    {
        return view('livewire.product.edit-product');
    }
}
