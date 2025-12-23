<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\BannerService;
use App\Models\Banner;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class BannerServiceTest extends TestCase
{
    use RefreshDatabase;

    protected BannerService $bannerService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->bannerService = new BannerService();
        
        // Fake the public storage disk for testing
        Storage::fake('public');
    }

    /** @test */
    public function it_can_be_instantiated()
    {
        $this->assertInstanceOf(BannerService::class, $this->bannerService);
    }

    /** @test */
    public function it_can_get_active_banners()
    {
        // Create a user for the created_by field
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create some test banners
        Banner::factory()->create(['is_active' => true, 'priority' => 2]);
        Banner::factory()->create(['is_active' => false, 'priority' => 1]);
        Banner::factory()->create(['is_active' => true, 'priority' => 1]);

        $activeBanners = $this->bannerService->getActiveBanners();

        $this->assertCount(2, $activeBanners);
        $this->assertEquals(1, $activeBanners->first()->priority);
        $this->assertEquals(2, $activeBanners->last()->priority);
    }

    /** @test */
    public function it_can_toggle_banner_status()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $banner = Banner::factory()->create(['is_active' => true]);

        $updatedBanner = $this->bannerService->toggleStatus($banner);

        $this->assertFalse($updatedBanner->is_active);

        $updatedBanner = $this->bannerService->toggleStatus($updatedBanner);

        $this->assertTrue($updatedBanner->is_active);
    }

    /** @test */
    public function it_can_process_image_dimensions()
    {
        // Create a simple test image using GD
        $image = imagecreatetruecolor(2000, 1000);
        $tempPath = tempnam(sys_get_temp_dir(), 'test_image') . '.jpg';
        imagejpeg($image, $tempPath, 100);
        imagedestroy($image);

        // Create UploadedFile instance
        $uploadedFile = new UploadedFile(
            $tempPath,
            'test-image.jpg',
            'image/jpeg',
            null,
            true
        );

        $result = $this->bannerService->processImage($uploadedFile);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('main', $result);
        $this->assertArrayHasKey('tablet', $result);
        $this->assertArrayHasKey('mobile', $result);

        // Verify files were created in storage
        Storage::disk('public')->assertExists($result['main']);
        Storage::disk('public')->assertExists($result['tablet']);
        Storage::disk('public')->assertExists($result['mobile']);

        // Clean up
        unlink($tempPath);
    }
}