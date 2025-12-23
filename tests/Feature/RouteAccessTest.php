<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

/**
 * Route Access Tests
 * 
 * Tests basic route accessibility for different user roles.
 * Note: The application uses view-level permission checks rather than
 * route-level middleware for most admin routes.
 */
class RouteAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            'view.users', 'create.users', 'edit.users', 'delete.users',
            'view.products', 'create.products', 'edit.products', 'delete.products',
            'view.reports', 'manage.settings',
            'view.sales.all', 'view.sales.own', 'create.sales',
            'view.attendance.all', 'view.attendance.own',
            'view.penalty.all', 'view.penalty.own',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create Ketua role with admin permissions
        $ketuaRole = Role::firstOrCreate(['name' => 'Ketua']);
        $ketuaRole->givePermissionTo([
            'view.users', 'create.users', 'edit.users',
            'view.products', 'create.products', 'edit.products',
            'view.reports', 'manage.settings',
            'view.sales.all', 'view.attendance.all', 'view.penalty.all',
        ]);

        // Create Anggota role with member permissions
        $anggotaRole = Role::firstOrCreate(['name' => 'Anggota']);
        $anggotaRole->givePermissionTo([
            'view.sales.own', 'create.sales',
            'view.attendance.own', 'view.penalty.own',
        ]);
    }

    private function makeUserWithRole(string $role): User
    {
        $user = User::create([
            'name' => ucfirst($role) . ' User',
            'nim' => 'NIM' . strtoupper(substr($role, 0, 3)) . rand(1000, 9999),
            'email' => strtolower($role) . rand(1000, 9999) . '@test.com',
            'password' => Hash::make('password'),
            'status' => 'active',
        ]);
        $user->assignRole($role);
        return $user;
    }

    public function test_admin_can_access_admin_routes(): void
    {
        $admin = $this->makeUserWithRole('Ketua');

        // Test core admin routes
        $this->actingAs($admin)
            ->get('/admin/dashboard')->assertOk();
        $this->actingAs($admin)
            ->get('/admin/users')->assertOk();
        $this->actingAs($admin)
            ->get('/admin/reports/attendance')->assertOk();
        $this->actingAs($admin)
            ->get('/admin/reports/sales')->assertOk();
    }

    public function test_member_can_access_member_routes(): void
    {
        $member = $this->makeUserWithRole('Anggota');

        // Members can access dashboard and their own data
        $this->actingAs($member)
            ->get('/admin/dashboard')->assertOk();
        $this->actingAs($member)
            ->get('/admin/attendance')->assertOk();
        $this->actingAs($member)
            ->get('/admin/schedule/my-schedule')->assertOk();
        $this->actingAs($member)
            ->get('/admin/penalties/my-penalties')->assertOk();
    }

    public function test_guest_cannot_access_admin_routes(): void
    {
        // Guest should be redirected to login
        $this->get('/admin/dashboard')->assertRedirect('/admin/login');
        $this->get('/admin/users')->assertRedirect('/admin/login');
        $this->get('/admin/products')->assertRedirect('/admin/login');
    }

    public function test_store_settings_requires_specific_roles(): void
    {
        // Store settings has explicit role middleware
        $admin = $this->makeUserWithRole('Ketua');
        $member = $this->makeUserWithRole('Anggota');

        // Admin can access store settings
        $this->actingAs($admin)
            ->get('/admin/settings/store')->assertOk();

        // Member cannot access store settings (has role middleware)
        $this->actingAs($member)
            ->get('/admin/settings/store')->assertForbidden();
    }
}
