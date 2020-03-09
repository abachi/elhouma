<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;
    
    private function user($data= [])
    {
        return array_merge([
            'name' => 'Nasser Abachi',
            'email' => 'abachinasser@gmail.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ], $data);
    }
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
            'user',
            'token',
          ]);

        $this->assertAuthenticated('api');
    }

    public function test_should_register_and_authenticate_a_valid_user()
    {
        $this->assertNull(User::where('email', 'abachinasser@gmail.com')->first());
        $response = $this->json('POST', route('auth.register'), $this->user());
        $this->assertNotNull(User::where('email', 'abachinasser@gmail.com')->first());
        $response
            ->assertStatus(201)
            ->assertJsonStructure(['token', 'user']);
        $this->assertAuthenticated('api');
    }

    public function test_user_should_logout()
    {
        $user = factory(User::class)->create();
        $token = \JWTAuth::fromUser($user);
        $response = $this->json('POST', route('auth.logout'), ['token' => $token]);
        $response->assertStatus(200);
        $this->assertGuest('api');
    }

    public function test_should_return_the_user_by_token()
    {
        $user = factory(User::class)->create();
        $token = \JWTAuth::fromUser($user);
        $response = $this->json('GET', route('auth.attempt'), ['token' => $token]);
        $response
            ->assertStatus(200)
            ->assertJsonStructure(['user']);
    }

    public function test_should_return_invalid_token_response()
    {
        $token = 'foo.baz.boo';
        $response = $this->json('GET', route('auth.attempt'), ['token' => $token]);
        $response
            ->assertStatus(401)
            ->assertJsonStructure(['error']);
    }

    public function test_should_return_password_or_email_validation_error()
    {
        $user = factory(User::class)->create(['email' => 'abachinasser@gmail.com']);
        $response = $this->json('POST', route('auth.login'), [
          'email' => 'doesnotexist@example.com',
          'password' => 'notvalid',
          ]);
        $response
          ->assertStatus(401)
          ->assertJsonStructure([
            'error',
          ]);

        $this->assertGuest('api');
    }

    public function test_user_should_not_register_when_the_email_is_missing()
    {
        $response = $this->json('POST', route('auth.register'), $this->user([
            'email' => '',
        ]));
        $response->assertStatus(422);
    }
    public function test_user_should_not_register_when_the_email_is_invalid_format()
    {
        $response = $this->json('POST', route('auth.register'), $this->user([
            'email' => 'invalid_email_format',
        ]));
        $response->assertStatus(422);
    }
    public function test_user_should_not_register_when_the_email_is_already_exists()
    {
        $user = factory(User::class)->create(['email' => 'user@example.com']);
        $response = $this->json('POST', route('auth.register'), $this->user([
            'email' => 'user@example.com',
        ]));
        $response->assertStatus(422);
    }

    public function test_user_should_not_register_when_name_is_missing()
    {
        $response = $this->json('POST', route('auth.register'), $this->user([
            'name' => '',
        ]));
        $response->assertStatus(422);
    }

    public function test_user_should_not_register_when_name_is_too_short()
    {
        $response = $this->json('POST', route('auth.register'), $this->user([
            'name' => 'nas',
        ]));
        $response->assertStatus(422);
    }

    public function test_user_should_not_register_when_password_is_missing()
    {
        $response = $this->json('POST', route('auth.register'), $this->user([
            'password' => '',
        ]));
        $response->assertStatus(422);
    }

    public function test_user_should_not_register_when_password_is_too_short()
    {
        $response = $this->json('POST', route('auth.register'), $this->user([
            'password' => 'ab1',
        ]));
        $response->assertStatus(422);
    }

    public function test_user_should_not_register_when_password_is_not_confirmed()
    {
        $response = $this->json('POST', route('auth.register'), $this->user([
            'password' => 'password',
            'password_confirmation' => '',
        ]));
        $response->assertStatus(422);
    }
}
