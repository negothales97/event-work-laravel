<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Role;
use App\Models\User;
use App\Services\Responses\ServiceResponse;
use Illuminate\Foundation\Testing\WithFaker;
use App\Services\Contracts\UserServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

    protected $userService;

    public function setUp(): void
    {
        parent::setUp();
        $this->userService = app(UserServiceInterface::class);
    }

    /**
     * Deve criar um usuário com sucesso
     */
    public function testShouldCreateUserWithSuccess()
    {
        $role = Role::factory()->admin()->create();

        $data = [
            'name' => $this->faker->name,
            'phone' => $this->faker->phoneNumber,
            'email' => $this->faker->email,
            'role_id' => $role->id,
            'password' => $this->faker->password(6),
        ];

        $userServiceResponse = $this->userService->store($data);
        unset($data['password']);

        $this->assertDatabaseHas('users', $data);
        $this->assertTrue($userServiceResponse->success);
        $this->assertNotEmpty($userServiceResponse->data);
        $this->assertEmpty($userServiceResponse->internalErrors);
        $this->assertInstanceOf(User::class, $userServiceResponse->data);
        $this->assertInstanceOf(ServiceResponse::class, $userServiceResponse);
    }

    /**
     * Teste não deve criar usuário se não passar telefone
     */
    public function testShouldNotCreateUserWithSuccessIfRequestIsMissingPhone()
    {
        $role = Role::factory()->admin()->create();

        $data = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'role_id' => $role->id,
            'password' => $this->faker->password(6)
        ];

        $userServiceResponse = $this->userService->store($data);

        $this->assertDatabaseMissing('users', $data);
        $this->assertFalse($userServiceResponse->success);
        $this->assertIsArray($userServiceResponse->internalErrors);
        $this->assertInstanceOf(ServiceResponse::class, $userServiceResponse);
    }

    /**
     * Teste não deve criar usuário se não passar role
     */
    public function testShouldNotCreateUserWithSuccessIfRequestIsMissingRole()
    {
        $data = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'phone' => $this->faker->phoneNumber,
            'password' => $this->faker->password(6)
        ];

        $userServiceResponse = $this->userService->store($data);

        $this->assertDatabaseMissing('users', $data);
        $this->assertFalse($userServiceResponse->success);
        $this->assertIsArray($userServiceResponse->internalErrors);
        $this->assertInstanceOf(ServiceResponse::class, $userServiceResponse);
    }

    /**
     * Teste não deve criar se email já existe no banco
     */
    public function testShouldNotCreateIfEmailAlreadyExistsInDatabase()
    {
        $user = User::factory()->create();
        $role = Role::factory()->admin()->create();

        $data = [
            'name' => $this->faker->name,
            'email' => $user->email,
            'role_id' => $role->id,
            'phone' => $this->faker->phoneNumber,
            'password' => $this->faker->password(6)
        ];

        $userServiceResponse = $this->userService->store($data);

        $this->assertDatabaseMissing('users', $data);
        $this->assertFalse($userServiceResponse->success);
        $this->assertIsArray($userServiceResponse->internalErrors);
        $this->assertInstanceOf(ServiceResponse::class, $userServiceResponse);
    }

    /**
     * Teste não deve criar usuário se role não existir no banco
     */
    public function testShouldNotCreateUserIfRoleDoesntExist()
    {
        $data = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'phone' => $this->faker->phoneNumber,
            'password' => $this->faker->password(6),
            'role_id' => 2
        ];

        $userServiceResponse = $this->userService->store($data);

        $this->assertDatabaseMissing('users', $data);
        $this->assertFalse($userServiceResponse->success);
        $this->assertIsArray($userServiceResponse->internalErrors);
        $this->assertInstanceOf(ServiceResponse::class, $userServiceResponse);
    }

    /**
     * Teste deve retornar um usuário existente do banco
     */
    public function testShouldReturnExistingUserInDatabase()
    {
        $user = User::factory()->create();
        $userServiceResponse = $this->userService->find($user->uuid);

        $this->assertTrue($userServiceResponse->success);
        $this->assertEmpty($userServiceResponse->internalErrors);
        $this->assertInstanceOf(User::class, $userServiceResponse->data);
        $this->assertInstanceOf(ServiceResponse::class, $userServiceResponse);
    }

    /**
     * Teste deve retornar não localizado quando usuário não existir
     */

    public function testShouldReturnNotFoundWhenUserDoesntExist()
    {
        $userServiceResponse = $this->userService->find('teste');

        $this->assertEmpty($userServiceResponse->data);
        $this->assertFalse($userServiceResponse->success);
        $this->assertEmpty($userServiceResponse->internalErrors);
        $this->assertInstanceOf(ServiceResponse::class, $userServiceResponse);
    }

    /**
     * Teste deve atualizar um usuário com sucesso
     */
    public function testShouldUpdateUserWithSuccess()
    {
        $role = Role::factory()->admin()->create();

        $attributes = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => $this->faker->password(6),
            'phone' => $this->faker->phoneNumber,
            'role_id' => $role->id
        ];

        $user = User::factory()->create();

        $userServiceResponse = $this->userService->update($user->uuid, $attributes);
        unset($attributes['password']);

        $this->assertDatabaseHas('users', $attributes);
        $this->assertTrue($userServiceResponse->success);
        $this->assertEmpty($userServiceResponse->internalErrors);
        $this->assertInstanceOf(User::class, $userServiceResponse->data);
        $this->assertInstanceOf(ServiceResponse::class, $userServiceResponse);
    }

    /**
     * Teste não deve atualizar usuário se email já existir no banco
     */
    public function testShouldNotUpdateUserIfEmailAlreadyExistsInDatabase()
    {
        $role = Role::factory()->admin()->create();

        $users = User::factory()->count(2)->create();

        $attributes = [
            'name' => $this->faker->name,
            'email' => $users[1]->email,
            'password' => $this->faker->password(6),
            'phone' => $this->faker->phoneNumber,
            'role_id' => $role->id
        ];

        $userServiceResponse = $this->userService->update($users[0]->uuid, $attributes);

        $this->assertFalse($userServiceResponse->success);
        $this->assertDatabaseMissing('users', $attributes);
        $this->assertNotEmpty($userServiceResponse->internalErrors);
        $this->assertInstanceOf(ServiceResponse::class, $userServiceResponse);
    }

    /**
     * Teste não deve atualizar usuário se role não existir no banco de dados
     */
    public function testShouldNotUpdateUserIfRoleIdDoesntExistsInDatabase()
    {
        $attributes = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => $this->faker->password(6),
            'phone' => $this->faker->phoneNumber,
            'role_id' => 10
        ];

        $user = User::factory()->create();

        $userServiceResponse = $this->userService->update($user->uuid, $attributes);


        $this->assertDatabaseMissing('users', $attributes);
        $this->assertFalse($userServiceResponse->success);
        $this->assertNotEmpty($userServiceResponse->internalErrors);
        $this->assertInstanceOf(ServiceResponse::class, $userServiceResponse);
    }

    /**
     * Teste deve retornar os usuários paginados
     */
    public function testShouldReturnPaginatedUsers()
    {
        User::factory()->count(3)->create();

        $userServiceResponse = $this->userService->getAllPaginated();

        $this->assertTrue($userServiceResponse->success);
        $this->assertCount(3, $userServiceResponse->data);
        $this->assertEmpty($userServiceResponse->internalErrors);
        $this->assertInstanceOf(ServiceResponse::class, $userServiceResponse);
    }

    /**
     * Teste deve excluir um usuário com sucesso
     */
    public function testShouldDeleteUserWithSuccess()
    {
        $user = User::factory()->create();

        $userServiceResponse = $this->userService->delete($user->uuid);

        $this->assertTrue($userServiceResponse->success);
        $this->assertEmpty($userServiceResponse->internalErrors);
        $this->assertInstanceOf(ServiceResponse::class, $userServiceResponse);
        $this->assertSoftDeleted('users', [
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'uuid' => $user->uuid,
        ]);
    }

    /**
     * Teste não deve excluir usuário se não encontrar
     */
    public function testShouldNotDeleteNonExistingUser()
    {
        $user = User::factory()->create();
        $user->delete();

        $userServiceResponse = $this->userService->delete($user->uuid);

        $this->assertNull($userServiceResponse->data);
        $this->assertFalse($userServiceResponse->success);
        $this->assertEmpty($userServiceResponse->internalErrors);
        $this->assertInstanceOf(ServiceResponse::class, $userServiceResponse);
    }
    /**
     *Teste deve retornar total de páginas conforme parametro
     *
     */
    public function testShouldReturnPaginatedUsersByPerPageParameter()
    {
        User::factory()->count(10)->create();
        $per_page = 5;

        $userServiceResponse = $this->userService->getAllPaginated($per_page);

        $this->assertTrue($userServiceResponse->success);
        $this->assertCount(5, $userServiceResponse->data);
        $this->assertEmpty($userServiceResponse->internalErrors);
        $this->assertInstanceOf(ServiceResponse::class, $userServiceResponse);
    }

}
