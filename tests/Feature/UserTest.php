<?php

namespace Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Laravel\Passport\Passport;
use Tests\TestCase;

class UserTest extends TestCase
{

    public function testGetCurrentUserDataSuccess()
    {
        $this->post('api/users/login', [
            'email' => 'achmadrifqi12@gmail.com',
            'password' => 'password'
        ]);

        Passport::actingAs(
            Auth::user()
        );
        $this->get('/api/users/current')
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id', 'name', 'username', 'email', 'phone', 'avatar_id'
                ],
            ]);
    }

    public function testGetCurrentUserDataFailed()
    {
        $this->get('/api/users/current')
            ->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'unauthorized'
                    ]
                ]
            ]);
    }

    public function testUserUpdatePasswordSuccess()
    {
        $this->post('api/users/login', [
            'email' => 'achmadrifqi12@gmail.com',
            'password' => 'password'
        ]);

        Passport::actingAs(
            Auth::user()
        );

        $this->put('api/users/current/password', [
            'old_password' => 'password',
            'password' => 'passwordupdate',
            'confirmation_password' => 'passwordupdate'
        ])->assertStatus(200)
            ->assertJson([
                'data' => true
            ]);
    }

    public function testUserUpdatePasswordFailed()
    {
        $this->post('api/users/login', [
            'email' => 'achmadrifqi12@gmail.com',
            'password' => 'passwordupdate'
        ]);

        Passport::actingAs(
            Auth::user()
        );

        $this->put('api/users/current/password', [
            'old_password' => 'passwordupdate',
            'password' => 'passwordupdate',
            'confirmation_password' => 'passwordtesting'
        ])->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'confirmation_password' => [
                        "The confirmation password field must match password."
                    ]
                ]
            ]);
    }

    public function testUserUpdateAvatarSuccess()
    {
        $this->post('api/users/login', [
            'email' => 'achmadrifqi12@gmail.com',
            'password' => 'passwordupdate'
        ]);

        Passport::actingAs(
            Auth::user()
        );

        Storage::fake('avatar');
        $this->post('api/users/current/avatar', [
            'image' => UploadedFile::fake()->image('avatar.jpg')
        ])
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id', 'name', 'username', 'email', 'phone', 'avatar_id'
                ]
            ]);
    }

    public function testUserUpdateAvatarFailed()
    {
        $this->post('api/users/login', [
            'email' => 'achmadrifqi12@gmail.com',
            'password' => 'passwordupdate'
        ]);

        Passport::actingAs(
            Auth::user()
        );

        Storage::fake('avatar');
        $this->post('api/users/current/avatar', [
            'image' => ''
        ])
            ->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'image' => [
                        "The image field is required."
                    ]
                ]
            ]);
    }

    public function testUpdateUserSuccess()
    {
        $this->post('api/users/login', [
            'email' => 'achmadrifqi12@gmail.com',
            'password' => 'passwordupdate'
        ]);

        Passport::actingAs(
            Auth::user()
        );

        $this->put('api/users/current', [
            'name' => 'Update Name',
            'email' => 'user@mail.com',
            'phone' => '12345678'
        ])
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id', 'name', 'username', 'email', 'phone', 'avatar_id'
                ]
            ]);
    }

    public function testUpdateUserFailed()
    {
        $this->post('api/users/login', [
            'email' => 'user@mail.com',
            'password' => 'passwordupdate'
        ]);

        Passport::actingAs(
            Auth::user()
        );

        $this->put('api/users/current', [
            'name' => 'Update Name',
            'email' => 'user@mail.com',
            'phone' => '123'
        ])
            ->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'phone' => [
                        'The phone field must be at least 8 characters.'
                    ]
                ]
            ]);
    }
}
