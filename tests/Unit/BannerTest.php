<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Banner;
use App\Services\BannerService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class BannerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Create storage disk for testing
        Storage::fake('public');
    }

    /** @test */
    public function banner_model_exists_and_has_correct_fillable_fields()
    {
        $banner = new Banner();
        
        $expectedFillable = [
            'title',
            'image_path',
            'priority',
            'is_active',
            'created_by',
        ];
        
        $this->assertEquals($expectedFillable, $banner->getFillable());
    }

    /** @test */
    public function banner_model_has_correct_casts()
    {
        $banner = new Banner();
        
        $expectedCasts = [
            'id' => 'int',
            'priority' => 'integer',
            'is_active' => 'boolean',
            'created_by' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
        
        $this->assertEquals($expectedCasts, $banner->getCasts());
    }

    /** @test */
    public function banner_service_exists_and_has_required_methods()
    {
        $service = new BannerService();
        
        $this->assertTrue(method_exists($service, 'store'));
        $this->assertTrue(method_exists($service, 'update'));
        $this->assertTrue(method_exists($service, 'delete'));
        $this->assertTrue(method_exists($service, 'toggleStatus'));
        $this->assertTrue(method_exists($service, 'processImage'));
        $this->assertTrue(method_exists($service, 'getActiveBanners'));
    }

    /** @test */
    public function banner_service_calculate_dimensions_maintains_aspect_ratio()
    {
        $service = new BannerService();
        
        // Use reflection to access protected method
        $reflection = new \ReflectionClass($service);
        $method = $reflection->getMethod('calculateDimensions');
        $method->setAccessible(true);
        
        // Test case: 1920x1080 image should maintain aspect ratio when resized to 768px width
        $result = $method->invoke($service, 1920, 1080, 768);
        
        $this->assertEquals(768, $result['width']);
        $this->assertEquals(432, $result['height']); // 768 * (1080/1920) = 432
        
        // Test case: smaller image should not be upscaled
        $result = $method->invoke($service, 400, 300, 768);
        
        $this->assertEquals(400, $result['width']);
        $this->assertEquals(300, $result['height']);
    }

    /** @test */
    public function banner_service_create_image_resource_handles_different_formats()
    {
        $service = new BannerService();
        
        // Use reflection to access protected method
        $reflection = new \ReflectionClass($service);
        $method = $reflection->getMethod('createImageResource');
        $method->setAccessible(true);
        
        // Test unsupported format returns false
        $result = $method->invoke($service, '/fake/path', 'image/webp');
        $this->assertFalse($result);
        
        // Note: We can't test actual image creation without real image files
        // but we can verify the method exists and handles unsupported formats
    }

    /** @test */
    public function image_url_accessor_returns_correct_format()
    {
        $banner = new Banner();
        $banner->image_path = 'banners/test-image.jpg';
        
        $expectedUrl = asset('storage/banners/test-image.jpg');
        $this->assertEquals($expectedUrl, $banner->image_url);
    }

    /** @test */
    public function image_url_accessor_returns_placeholder_when_no_path()
    {
        $banner = new Banner();
        $banner->image_path = null;
        
        $expectedUrl = asset('images/placeholder-banner.jpg');
        $this->assertEquals($expectedUrl, $banner->image_url);
    }

    /** @test */
    public function thumbnail_url_accessor_returns_correct_format()
    {
        $banner = new Banner();
        $banner->image_path = 'banners/test-image_1920.jpg';
        
        $expectedUrl = asset('storage/banners/test-image_1920_480.jpg');
        $this->assertEquals($expectedUrl, $banner->thumbnail_url);
    }

    /** @test */
    public function thumbnail_url_accessor_returns_placeholder_when_no_path()
    {
        $banner = new Banner();
        $banner->image_path = null;
        
        $expectedUrl = asset('images/placeholder-banner-thumb.jpg');
        $this->assertEquals($expectedUrl, $banner->thumbnail_url);
    }
}