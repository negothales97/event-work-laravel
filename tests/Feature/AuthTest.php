<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

    /**
     * O usuário pode realizar login e receber um token válido
     */
    public function testUserCantRequestLoginAndReceiveAValidToken()
    {
        $user = User::factory()->create();

        $attributes = [
            'email' => $user->email,
            'password' => 'password'
        ];

        $response = $this
            ->postJson(
                route(
                    'api.login'
                ),
                $attributes
            );

        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonPath('data.name', $user->name)
            ->assertJsonPath('data.email', $user->email)
            ->assertJsonStructure([
                'data' => [
                    'uuid',
                    'name',
                    'email',
                    'token',
                ]
            ])
            ->assertJsonMissing([
                'errors'
            ]);
    }

    /**
     * O usuário deve receber um 422 quando passa os dados errados para o login
     */
    public function testUserMustReceive422WhenPassWrongDataToLogin()
    {
        $user = User::factory()->create();

        $attributes = [
            'email' => $user->email
        ];

        $response = $this
            ->postJson(
                route(
                    'api.login'
                ),
                $attributes
            );

        $response
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
            ])
            ->assertJsonStructure([
                'data' => ['errors']
            ]);
    }

    /**
     * O usuário deve receber um 500 com a mensagem respectiva quando passar um email errado para o login
     */
    public function testUserMustReceive500WhenPassWrongEmailToLogin()
    {
        User::factory()->create();

        $attributes = [
            'email' => $this->faker->email,
            'password' => 'password'
        ];

        $response = $this
            ->postJson(
                route(
                    'api.login'
                ),
                $attributes
            );


        $response
            ->assertStatus(500)
            ->assertJson([
                'success' => false,
            ])
            ->assertJsonStructure([
                'data' => ['errors']
            ]);
    }

    /**
     * O usuário deve receber um 500 com a mensagem respectiva quando passar uma senha errada para o login
     */
    public function testUserMustReceive500WhenPassWrongPasswordToLogin()
    {
        $user = User::factory()->create();

        $attributes = [
            'email' => $user->email,
            'password' => 'wrong_pass'
        ];

        $response = $this
            ->postJson(
                route(
                    'api.login'
                ),
                $attributes
            );


        $response
            ->assertStatus(500)
            ->assertJson([
                'success' => false,
            ])
            ->assertJsonStructure([
                'data' => ['errors']
            ]);
    }
}
