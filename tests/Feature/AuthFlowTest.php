<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_when_accessing_protected_route(): void
    {
        $this->get('/admin/beranda')
            ->assertRedirect('/admin/masuk');
    }

    public function test_login_page_loads(): void
    {
        $this->get('/admin/masuk')->assertOk();
    }

    public function test_authenticated_user_can_access_dashboard(): void
    {
        $user = User::create([
            'name' => 'Member User',
            'nim' => 'NIM0001',
            'email' => 'member@test.com',
            'password' => Hash::make('password'),
            'status' => 'active',
        ]);

        $this->actingAs($user)
            ->get('/admin/beranda')
            ->assertOk();
    }
}
