<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use App\Services\Responses\ServiceResponse;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\Contracts\ResetPasswordServiceInterface;

class PasswordResetTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

    protected $resetPasswordService;

    public function setUp(): void
    {
        parent::setUp();
        $this->resetPasswordService = app(ResetPasswordServiceInterface::class);
    }

    /**
     * Teste deve enviar link com token com sucesso
     */
    public function testShouldSendResetTokenWithSuccess()
    {
        $user = User::factory()->create();
        $data = [
            'email' => $user->email
        ];

        $resetPasswordServiceResponse = $this->resetPasswordService->sendResetLink($data);

        $this->assertTrue($resetPasswordServiceResponse->success);
        $this->assertEmpty($resetPasswordServiceResponse->internalErrors);
        $this->assertInstanceOf(ServiceResponse::class, $resetPasswordServiceResponse);
    }

    /**
     * Teste não deve enviar token se email não existir
     */
    public function testShouldNotSendTokenIfEmailDoesntExists()
    {
        $data = [
            'email' => $this->faker->email
        ];

        $resetPasswordServiceResponse = $this->resetPasswordService->sendResetLink($data);

        $this->assertFalse($resetPasswordServiceResponse->success);
        $this->assertEmpty($resetPasswordServiceResponse->internalErrors);
        $this->assertInstanceOf(ServiceResponse::class, $resetPasswordServiceResponse);
    }

    /**
     * Teste deve atualizar uma senha com sucesso
     */
    public function testShouldUpdatePasswordWithSuccess()
    {

        $user = User::factory()->create();
        $data = [
            'email' => $user->email,
            'token' => Password::createToken($user),
            'password' => $this->faker->password(6)
        ];

        $resetPasswordServiceResponse = $this->resetPasswordService->resetPassword($data);

        $this->assertTrue($resetPasswordServiceResponse->success);
        $this->assertEmpty($resetPasswordServiceResponse->internalErrors);
        $this->assertInstanceOf(ServiceResponse::class, $resetPasswordServiceResponse);
    }

    /**
     * Teste deve não deve atualizar senha se token for inválido
     */
    public function testShouldUpdatePasswordIfTokenIsInvalid()
    {

        $user = User::factory()->create();
        $data = [
            'email' => $user->email,
            'token' => Str::uuid(),
            'password' => $this->faker->password(6)
        ];

        $resetPasswordServiceResponse = $this->resetPasswordService->resetPassword($data);

        $this->assertFalse($resetPasswordServiceResponse->success);
        $this->assertEmpty($resetPasswordServiceResponse->internalErrors);
        $this->assertInstanceOf(ServiceResponse::class, $resetPasswordServiceResponse);
    }

    /**
     * Teste deve não deve atualizar se senha for igual a anterior
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

        $resetPasswordServiceResponse = $this->resetPasswordService->resetPassword($data);

        $this->assertFalse($resetPasswordServiceResponse->success);
        $this->assertEmpty($resetPasswordServiceResponse->internalErrors);
        $this->assertInstanceOf(ServiceResponse::class, $resetPasswordServiceResponse);
    }

    /**
     * Teste deve não deve atualizar se email não existir no banco de dados
     */
    public function testShouldNotUpdatePasswordIfEmailDoesntExistsInDatabase()
    {

        $user = User::factory()->create();
        $data = [
            'email' => $this->faker->email,
            'token' => Password::createToken($user),
            'password' => $this->faker->password(6)
        ];

        $resetPasswordServiceResponse = $this->resetPasswordService->resetPassword($data);

        $this->assertFalse($resetPasswordServiceResponse->success);
        $this->assertEmpty($resetPasswordServiceResponse->internalErrors);
        $this->assertInstanceOf(ServiceResponse::class, $resetPasswordServiceResponse);
    }

}
