<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Passport;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testUserRegisterSuccess()
    {
        $this->post('/api/users', [
            'name' => 'Achmad Rifqi',
            'username' => 'achmad12',
            'email' => 'achmadrifqi12@gmail.com',
            'phone' => '081554112334',
            'password' => 'password'
        ])->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id', 'name', 'username', 'email', 'phone', 'avatar_id', 'token'
                ]
            ]);
    }

    public function testRegisterFailed()
    {
        $this->post('/api/users', [
            'name' => '',
            'username' => '',
            'email' => '',
            'phone' => '',
            'password' => ''
        ])->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'name' => [
                        'The name field is required.'
                    ],
                    'username' => [
                        'The username field is required.'
                    ],
                    'email' => [
                        'The email field is required.'
                    ],
                    'phone' => [
                        'The phone field is required.'
                    ],
                    'password' => [
                        'The password field is required.'
                    ],
                ]
            ]);
    }

    public function testLoginSuccess()
    {
        $this->post('api/users/login', [
            'email' => 'achmadrifqi12@gmail.com',
            'password' => 'password'
        ])->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id', 'email', 'token'
                ]
            ]);
    }

    public function testLoginFailedPasswordWrong()
    {
        $this->post('api/users/login', [
            'email' => 'achmadrifqi12@gmail.com',
            'password' => 'wrongpassword'
        ])->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'Email or password wrong'
                    ]
                ]
            ]);
    }

    public function testLoginFailedEmailWrong()
    {
        $this->post('api/users/login', [
            'email' => 'testing@domain.com',
            'password' => 'password'
        ])->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'Email or password wrong'
                    ]
                ]
            ]);
    }


    public function testLogoutFailed()
    {

        $this->get('/api/users/logout')
            ->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'unauthorized'
                    ]
                ]
            ]);
    }
}
