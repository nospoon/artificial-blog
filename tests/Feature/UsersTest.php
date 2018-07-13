<?php

namespace Tests\Feature;

use App\User;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UsersTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function anAuthenticatedUserCanSeeHisOwnDetails()
    {
        $user = factory(User::class)->create();

        Passport::actingAs($user);

        $this->json('GET', route('me'))
            ->assertJsonFragment([
                'name' => $user->name,
                'email' => $user->email,
            ]);
    }

    /** @test */
    public function anAdminCanSeeViewAllUsers()
    {
        $users = factory(User::class, 4)->create();
        $admin = factory(User::class)->states('admin')->create();

        Passport::actingAs($admin);

        $this->json('GET', route('user.index'))
            ->assertJsonFragment([
                'name' => $admin->name,
                'email' => $admin->email,
            ]);
    }

    /** @test */
    public function regularUserCanNotViewOtherUsers()
    {
        $users = factory(User::class, 4)->create();
        $actingUser = factory(User::class)->create();

        Passport::actingAs($actingUser);

        $this->json('GET', route('user.index'))
            ->assertForbidden();
    }
}
