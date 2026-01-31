<?php

namespace Tests\Feature\ShuPoint;

use App\Livewire\ShuPoint\Students;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class ShuStudentsModalTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_opens_modal_state(): void
    {
        Permission::create(['name' => 'manage.shu_students']);

        $user = User::factory()->create();
        $user->givePermissionTo(['manage.shu_students']);
        $this->actingAs($user);

        Livewire::actingAs($user)
            ->test(Students::class)
            ->call('create')
            ->assertSet('showModal', true);
    }
}
