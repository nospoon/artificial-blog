<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Http\Response;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function testAuthenticationRequired()
    {
        $user = factory(User::class)->create();

        $this->json('GET', route('me'))
            ->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJsonMissing(['id' => $user->id]);
    }

    public function testPersonalAccessTokenAuthentication()
    {
        $user = factory(User::class)->create();
        $this->artisan('passport:client', [
            '--personal' => null,
            '--no-interaction' => null
        ]);
        $token = $user->createToken($this->faker->word)->accessToken;

        $this->json('GET', route('me'), [], [
            'Authorization' => 'Bearer ' . $token
        ])
            ->assertSuccessful()
            ->assertJson([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ]);
        $this->assertAuthenticatedAs($user);
    }
}
