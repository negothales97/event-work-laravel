<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Password;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PasswordResetTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;


    /**
     * Teste deve enviar token com sucesso
     */
    public function testShouldSendTokenWithSuccess()
    {
        $user = User::factory()->create();
        $data = [
            'email' => $user->email
        ];

        $response = $this->postJson(route('api.forgot-password'), $data);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true
            ]);
    }

    /**
     * Teste não deve enviar token se email não existir
     */
    public function testShouldNotSendTokenIfEmailDoesntExists()
    {
        $data = [
            'email' => $this->faker->email
        ];

        $response = $this->postJson(route('api.forgot-password'), $data);

        $response->assertStatus(500)
            ->assertJson([
                'success' => false
            ]);
    }

    /**
     * Teste deve atualizar senha com sucesso
     */
    public function testShouldUpdatePasswordWithSuccess()
    {
        $user = User::factory()->create();
        $data = [
            'email' => $user->email,
            'token' => Password::createToken($user),
            'password' => $this->faker->password(6)
        ];

        $response = $this->postJson(route('api.reset-password'), $data);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'code' => 200
            ]);
    }

    /**
     * Teste não deve atualizar senha se token for de outro email
     */
    public function testShouldNotUpdatePasswordIfTokenBelongsToAnotherEmail()
    {
        $users = User::factory()->count(2)->create();
        $data = [
            'email' => $users[1]->email,
            'token' => Password::createToken($users[0]),
            'password' => $this->faker->password(6)
        ];

        $response = $this->postJson(route('api.reset-password'), $data);

        $response->assertStatus(500)
            ->assertJson([
                'success' => false,
                'code' => 500
            ]);
    }

    /**
     * Teste não deve atualizar senha se senha for igual a antiga
     */
    public function testShouldNotUpdatePasswordIfPasswordIsEqualsOldPassword()
    {
        $user = User::factory()->create([
            'password' => 'teste'
        ]);
        $data = [
            'email' => $user->email,
            'token' => Password::createToken($user),
            'password' => 'teste'
        ];

        $response = $this->postJson(route('api.reset-password'), $data);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);
    }

    /**
     * Teste não deve atualizar senha se token for inválido
     */
    public function testShouldNotUpdatePasswordIfTokenIsInvalid()
    {
        $user = User::factory()->create([
            'password' => 'teste'
        ]);
        $data = [
            'email' => $user->email,
            'token' => Str::uuid(),
            'password' => $this->faker->password(6)
        ];

        $response = $this->postJson(route('api.reset-password'), $data);

        $response->assertStatus(500)
            ->assertJson([
                'success' => false,
            ]);
    }

    /**
     * Teste não deve atualizar senha se email não existir no banco de dados
     */
    public function testShouldNotUpdatePasswordIfEmailDoesntExistsInDatabase()
    {
        User::factory()->create([
            'password' => 'teste'
        ]);
        $data = [
            'email' => $this->faker->email,
            'token' => Str::uuid(),
            'password' => $this->faker->password(6)
        ];

        $response = $this->postJson(route('api.reset-password'), $data);

        $response->assertStatus(500)
            ->assertJson([
                'success' => false,
            ]);
    }
}
