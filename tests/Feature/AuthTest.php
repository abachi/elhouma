<?php

namespace Tests\Feature;

use App\User;
use App\FacebooInterface;
use \Facebook\Facebook;
use Tests\Feature\FakeFacebook;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_user_should_login_with_facebook_account()
    {
        $data = [
            'name' => 'Nasser Abachi',
            'email' => 'abachinasser@gmail.com',
            'provider' => 'facebook',
            'provider_token' => 'valid_facebook_access_token',
            'provider_user_id' => '1000010000123456',
        ];

        $response = $this->json('POST', route('sociallogin'), $data);
        $response->assertStatus(201)
            ->assertJsonStructure([
                'status',
                'data' => [
                'email',
                'provider',
                'name',
                'provider_user_id',
                'token',
                ]
            ]);
    }

    public function test_return_bad_request_for_invalid_facebook_access_token()
    {
        $data = [
            'name' => 'Nasser Abachi',
            'email' => 'abachinasser@gmail.com',
            'provider' => 'facebook',
            'provider_token' => 'THIS_TOKEN_IS_NOT_VALID',
            'provider_user_id' => '1000010000123456',
        ];
        $response = $this->json('POST', route('sociallogin'), $data);
        $response->assertStatus(400)
            ->assertJsonStructure([
                'error',
            ]);
    }

}
