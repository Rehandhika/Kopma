<?php

namespace Tests\Feature\Audit;

/**
 * Test to verify the AuditTestCase base class works correctly.
 */
class AuditTestCaseTest extends AuditTestCase
{
    public function test_audit_test_case_creates_all_roles(): void
    {
        $this->assertDatabaseHas('roles', ['name' => 'Super Admin']);
        $this->assertDatabaseHas('roles', ['name' => 'Ketua']);
        $this->assertDatabaseHas('roles', ['name' => 'Wakil Ketua']);
        $this->assertDatabaseHas('roles', ['name' => 'BPH']);
        $this->assertDatabaseHas('roles', ['name' => 'Anggota']);
    }

    public function test_audit_test_case_creates_all_test_users(): void
    {
        $this->assertNotNull($this->superAdmin);
        $this->assertNotNull($this->ketua);
        $this->assertNotNull($this->wakilKetua);
        $this->assertNotNull($this->bph);
        $this->assertNotNull($this->anggota);

        $this->assertTrue($this->superAdmin->hasRole('Super Admin'));
        $this->assertTrue($this->ketua->hasRole('Ketua'));
        $this->assertTrue($this->wakilKetua->hasRole('Wakil Ketua'));
        $this->assertTrue($this->bph->hasRole('BPH'));
        $this->assertTrue($this->anggota->hasRole('Anggota'));
    }

    public function test_create_user_with_role_helper(): void
    {
        $user = $this->createUserWithRole('Anggota', [
            'name' => 'Test User',
        ]);

        $this->assertNotNull($user);
        $this->assertEquals('Test User', $user->name);
        $this->assertTrue($user->hasRole('Anggota'));
        $this->assertEquals('active', $user->status);
    }

    public function test_create_inactive_user_helper(): void
    {
        $user = $this->createInactiveUser();

        $this->assertNotNull($user);
        $this->assertEquals('inactive', $user->status);
        $this->assertTrue($user->hasRole('Anggota'));
    }

    public function test_seed_products_helper(): void
    {
        $products = $this->seedProducts(3);

        $this->assertCount(3, $products);
        foreach ($products as $product) {
            $this->assertDatabaseHas('products', ['id' => $product->id]);
        }
    }

    public function test_get_all_test_users_helper(): void
    {
        $users = $this->getAllTestUsers();

        $this->assertCount(5, $users);
        $this->assertArrayHasKey('Super Admin', $users);
        $this->assertArrayHasKey('Ketua', $users);
        $this->assertArrayHasKey('Wakil Ketua', $users);
        $this->assertArrayHasKey('BPH', $users);
        $this->assertArrayHasKey('Anggota', $users);
    }

    public function test_get_admin_users_helper(): void
    {
        $adminUsers = $this->getAdminUsers();

        $this->assertCount(4, $adminUsers);
        $this->assertArrayHasKey('Super Admin', $adminUsers);
        $this->assertArrayHasKey('Ketua', $adminUsers);
        $this->assertArrayHasKey('Wakil Ketua', $adminUsers);
        $this->assertArrayHasKey('BPH', $adminUsers);
    }

    public function test_get_member_users_helper(): void
    {
        $memberUsers = $this->getMemberUsers();

        $this->assertCount(1, $memberUsers);
        $this->assertArrayHasKey('Anggota', $memberUsers);
    }
}
