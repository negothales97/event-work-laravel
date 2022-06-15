<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Services\Responses\ServiceResponse;
use Illuminate\Foundation\Testing\WithFaker;
use App\Services\Contracts\AuthServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

    protected $authService;

    public function setUp(): void
    {
        parent::setUp();
        $this->authService = app(AuthServiceInterface::class);
    }
    /**
     * Deve retornar os dados autenticados quando enviado dados corretos
     */
    public function testShouldReturnAuthDataWithCorrectData()
    {
        $user = User::factory()->create();

        $authResponse = $this->authService->login($user->email, 'password');

        $this->assertInstanceOf(ServiceResponse::class, $authResponse);
        $this->assertInstanceOf(User::class, $authResponse->data);
        $this->assertTrue($authResponse->success);
        $this->assertIsArray($authResponse->internalErrors);
        $this->assertEmpty($authResponse->internalErrors);
    }

    /**
     * Deve retornar não localizado quando o usuário não existir
     */
    public function testShouldReturnNotFoundWhenUserDoesntExist()
    {
        $authResponse = $this->authService->login($this->faker->email, 'password');

        $this->assertInstanceOf(ServiceResponse::class, $authResponse);
        $this->assertFalse($authResponse->success);
        $this->assertIsArray($authResponse->data);
        $this->assertIsArray($authResponse->internalErrors);
        $this->assertEmpty($authResponse->internalErrors);
    }

    /**
     * Deve retornar usuário ou senha inválidos quando o algum dado estiver incorreto
     */
    public function testShouldReturnUserOrPasswordIncorrectWhenDataIsWrong()
    {
        $user = User::factory()->create();

        $authResponse = $this->authService->login($user->email, 'wrong_pass');

        $this->assertInstanceOf(ServiceResponse::class, $authResponse);
        $this->assertFalse($authResponse->success);
        $this->assertIsArray($authResponse->data);
        $this->assertIsArray($authResponse->internalErrors);
        $this->assertEmpty($authResponse->internalErrors);
    }

    /**
     * Deve retornar o token atualizado
     */
    public function testShouldReturnGenerateToken()
    {
        $user = User::factory()->create();

        $authResponse = $this->authService->generateToken($user);

        $this->assertInstanceOf(ServiceResponse::class, $authResponse);
        $this->assertTrue($authResponse->success);
        $this->assertIsArray($authResponse->data);
        $this->assertArrayHasKey('token', $authResponse->data);
        $this->assertArrayHasKey('user', $authResponse->data);
        $this->assertIsArray($authResponse->internalErrors);
        $this->assertEmpty($authResponse->internalErrors);
    }
}
