<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_should_login_user()
    {
        $user = factory(User::class)->create(['email' => 'abachinasser@gmail.com']);
        $response = $this->json('POST', route('auth.login'), [
          'email' => 'abachinasser@gmail.com',
          'password' => 'password',
          ]);
        $response
          ->assertStatus(200)
          ->assertJsonStructure([
            'token',
          ]);

        $this->assertAuthenticated('api');
    }

    public function test_should_register_and_authenticate_a_valid_user()
    {
        $this->assertNull(User::where('email', 'abachinasser@gmail.com')->first());

        $response = $this->json('POST', route('auth.register'), [
            'name' => 'Nasser Abachi',
            'email' => 'abachinasser@gmail.com',
            'password' => 'password',
        ]);

        $this->assertNotNull(User::where('email', 'abachinasser@gmail.com')->first());

        $response
            ->assertStatus(201)
            ->assertJsonStructure(['token', 'email']);
    }

    public function test_user_should_logout()
    {
        $user = factory(User::class)->create();
        $token = \JWTAuth::fromUser($user);
        $response = $this->json('POST', route('auth.logout'), ['token' => $token]);
        $response->assertStatus(200);
        $this->assertGuest('api');
    }
}
