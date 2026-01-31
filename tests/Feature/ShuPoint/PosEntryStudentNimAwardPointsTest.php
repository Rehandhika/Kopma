<?php

namespace Tests\Feature\ShuPoint;

use App\Livewire\Cashier\PosEntry;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PosEntryStudentNimAwardPointsTest extends TestCase
{
    use RefreshDatabase;

    public function test_submit_all_with_student_nim_assigns_student_and_awards_points(): void
    {
        Role::create(['name' => 'Super Admin']);

        $user = User::factory()->create();
        $user->assignRole('Super Admin');
        $this->actingAs($user);

        Setting::set('shu_point_percentage_bps', '100'); // 1%

        $student = Student::factory()->create(['nim' => '123456789', 'points_balance' => 0]);

        $product = Product::create([
            'name' => 'Produk Test',
            'sku' => 'SKU-TEST',
            'price' => 10000,
            'stock' => 100,
            'min_stock' => 0,
            'category' => 'Test',
            'description' => null,
            'status' => 'active',
            'has_variants' => false,
            'is_featured' => false,
            'is_public' => true,
        ]);

        Livewire::actingAs($user)
            ->test(PosEntry::class)
            ->call('submitAll', [
                ['product_id' => $product->id, 'student_nim' => $student->nim, 'qty' => 1, 'payment_method' => 'cash'],
            ])
            ->assertDispatched('toast', type: 'success');

        $student->refresh();
        $this->assertSame(100, $student->points_balance);

        $this->assertDatabaseHas('sales', [
            'student_id' => $student->id,
            'shu_points_earned' => 100,
            'shu_percentage_bps' => 100,
        ]);

        $this->assertDatabaseHas('shu_point_transactions', [
            'student_id' => $student->id,
            'type' => 'earn',
            'points' => 100,
            'percentage_bps' => 100,
        ]);
    }
}
