<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Str;
use Tests\Feature\Traits\UtilsTrait;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use WithFaker;
    use UtilsTrait;
    use RefreshDatabase;

    /**
     * Teste não deve criar usuário se não estiver autenticado
     */
    public function testShouldNotCreateUserIfNotAuthenticated()
    {
        $role = Role::factory()->admin()->create();

        $attributes = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'phone' => $this->faker->phoneNumber,
            'password' => 'teste123',
            'password_confirmation' => 'teste123',
            'role_id' => $role->id
        ];

        $response = $this->postJson(route('api.users.store'), $attributes);

        $response->assertStatus(401);
    }

    /**
     * O usuário deve se cadastrar sem nenhum erro
     */
    public function testShouldCreateUserWithoutErrors()
    {
        $user = $this->createUser();
        $role = Role::factory()->admin()->create();

        $attributes = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'phone' => $this->faker->phoneNumber,
            'password' => 'teste123',
            'password_confirmation' => 'teste123',
            'role_id' => $role->id
        ];

        $response = $this->actingAs($user)->postJson(
            route('api.users.store'),
            $attributes,
        );

        $response
            ->assertStatus(200)
            ->assertJsonMissing(['errors'])
            ->assertJsonStructure([
                'data' => [
                    'name',
                    'email',
                    'phone',
                    'role_name'
                ]
            ]);
    }

    /**
     * Teste não deve criar usuário se não passar telefone
     */
    public function testShouldNotCreateUserIfRequestParametersIsMissingPhone()
    {
        $user = $this->createUser();
        $role = Role::factory()->admin()->create();

        $attributes = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => 'teste123',
            'password_confirmation' => 'teste123',
            'role_id' => $role->id
        ];

        $response = $this->actingAs($user)->postJson(
            route('api.users.store'),
            $attributes,
        );

        $response
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
            ])
            ->assertJsonStructure([
                'message',
                'data' => ['errors' => ['phone']]
            ]);
    }

    /**
     * Teste não deve criar usuário se não passar email
     */
    public function testShouldNotCreateUserIfRequestParametersIsMissingEmail()
    {
        $user = $this->createUser();
        $role = Role::factory()->admin()->create();

        $attributes = [
            'name' => $this->faker->name,
            'phone' => $this->faker->phoneNumber,
            'password' => 'teste123',
            'password_confirmation' => 'teste123',
            'role_id' => $role->id
        ];

        $response = $this->actingAs($user)->postJson(
            route('api.users.store'),
            $attributes,
        );

        $response
            ->assertStatus(422)
            ->assertJson([
                'success' => false
            ])
            ->assertJsonStructure([
                'message',
                'data' => ['errors' => ['email']]
            ]);
    }

    /**
     * Teste não deve criar usuário se não passar role
     */
    public function testShouldNotCreateUserIfRequestParametersIsMissingRole()
    {
        $user = $this->createUser();
        $attributes = [
            'name' => $this->faker->name,
            'phone' => $this->faker->phoneNumber,
            'password' => 'teste123',
            'password_confirmation' => 'teste123',
        ];

        $response = $this->actingAs($user)->postJson(
            route('api.users.store'),
            $attributes,
        );

        $response
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
            ])
            ->assertJsonStructure([
                'message',
                'data' => ['errors' => ['role_id']]
            ]);
    }

    /**
     * Teste não deve criar usuário se email já existir
     */
    public function testShouldNotCreateUserIfEmailAlreadyExists()
    {
        $user = $this->createUser();
        $role = Role::factory()->admin()->create();

        $attributes = [
            'name' => $this->faker->name,
            'email' => $user->email,
            'phone' => $this->faker->phoneNumber,
            'role_id' => $role->id,
            'password' => 'teste123',
            'password_confirmation' => 'teste123',
        ];

        $response = $this->actingAs($user)->postJson(
            route('api.users.store'),
            $attributes,
        );

        $response
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
            ])
            ->assertJsonStructure([
                'message',
                'data' => ['errors' => ['email']]
            ]);
    }

    /**
     * Teste não deve criar usuário se a senha não for igual a senha de confirmação
     */
    public function testShouldNotCreateUserIfPasswordDoesntMatch()
    {
        $user = $this->createUser();
        $role = Role::factory()->admin()->create();

        $attributes = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'phone' => $this->faker->phoneNumber,
            'role_id' => $role->id,
            'password' => 'teste123',
            'password_confirmation' => 'teste1234',
        ];

        $response = $this->actingAs($user)->postJson(
            route('api.users.store'),
            $attributes,
        );

        $response
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
            ])
            ->assertJsonStructure([
                'message',
                'data' => ['errors' => ['password']]
            ]);
    }

    /**
     * Teste não deve criar usuário se a role não existir
     */
    public function testShouldNotCreateUserIfRoleDoesntExists()
    {
        $user = $this->createUser();
        $attributes = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'phone' => $this->faker->phoneNumber,
            'role_id' => 44,
            'password' => 'teste123',
            'password_confirmation' => 'teste1234',
        ];

        $response = $this->actingAs($user)->postJson(
            route('api.users.store'),
            $attributes,
        );

        $response
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
            ])
            ->assertJsonStructure([
                'message',
                'data' => ['errors' => ['role_id']]
            ]);
    }

    /**
     * Teste deve encontar um usuario com sucesso
     */
    public function testShouldReturnUserWithSuccess()
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->getJson(
            route('api.users.show', $user->uuid),
        );

        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true
            ])
            ->assertJsonStructure([
                'data' => [
                    'name',
                    'email',
                    'phone',
                    'role_name'
                ]
            ]);
    }

    /**
     * Test deve retornar status 500 se usuário não existir
     */
    public function testShouldReturnStatus500IfUserDoesntExists()
    {
        $user = $this->createUser();
        $uuid = Str::uuid();

        $response = $this->actingAs($user)->getJson(
            route('api.users.show', $uuid),
        );

        $response
            ->assertStatus(500)
            ->assertJson([
                'success' => false,
                'data' => null
            ])
            ->assertJsonStructure([
                'errors' => [
                    ['message']
                ]
            ]);
    }

    /**
     * Teste deve atualizar um usuário com sucesso
     */
    public function testShouldUpdateUserWithSuccess()
    {
        $role = Role::factory()->admin()->create();
        $user = $this->createUser();

        $attributes = [
            'name' => $this->faker->name,
            'email' => $user->email,
            'phone' => $this->faker->phoneNumber,
            'password' => 'testenovo',
            'password_confirmation' => 'testenovo',
            'role_id' => $role->id
        ];

        $response = $this->actingAs($user)->putJson(
            route('api.users.update', $user->uuid),
            $attributes,
        );

        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true
            ])
            ->assertJsonStructure([
                'data' => [
                    'name',
                    'email',
                    'phone',
                    'role_name'
                ]
            ]);
    }

    /**
     * Teste não deve atualizar contato se email ja existir no banco de dados
     */
    public function testShouldNotUpdateUserIfEmailAlreadyExistsInDatabase()
    {
        $role = Role::factory()->admin()->create();
        $users = User::factory()->count(2)->create();

        $attributes = [
            'name' => $this->faker->name,
            'email' => $users[0]->email,
            'phone' => $this->faker->phoneNumber,
            'password' => 'testenovo',
            'password_confirmation' => 'testenovo',
            'role_id' => $role->id
        ];

        $response = $this->actingAs($users[0])->putJson(
            route('api.users.update', $users[1]->uuid),
            $attributes,
        );

        $response
            ->assertStatus(422)
            ->assertJson([
                'success' => false
            ])
            ->assertJsonStructure([
                'data' => [
                    'errors' => [
                        'email'
                    ]
                ]
            ]);
    }

    /**
     * Teste não deve atualizar usuario se não passar nome
     */
    public function testShouldNotUpdateUserIfRequestParametersIsMissingName()
    {
        $user = $this->createUser();
        $role = Role::factory()->admin()->create();

        $attributes = [
            'email' => $user->email,
            'phone' => $this->faker->phoneNumber,
            'password' => 'testenovo',
            'password_confirmation' => 'testenovo',
            'role_id' => $role->id
        ];

        $response = $this->actingAs($user)->putJson(
            route('api.users.update', $user->uuid),
            $attributes,
        );

        $response
            ->assertStatus(422)
            ->assertJson([
                'success' => false
            ])
            ->assertJsonStructure([
                'data' => [
                    'errors' => [
                        'name'
                    ]
                ]
            ]);
    }

    /**
     * Teste não deve atualizar usuario se não passar telefone
     */
    public function testShouldNotUpdateUserIfRequestParametersIsMissingPhone()
    {
        $user = $this->createUser();
        $role = Role::factory()->admin()->create();

        $attributes = [
            'name' => $this->faker->name,
            'email' => $user->email,
            'password' => 'testenovo',
            'password_confirmation' => 'testenovo',
            'role_id' => $role->id
        ];

        $response = $this->actingAs($user)->putJson(
            route('api.users.update', $user->uuid),
            $attributes,
        );

        $response
            ->assertStatus(422)
            ->assertJson([
                'success' => false
            ])
            ->assertJsonStructure([
                'data' => [
                    'errors' => [
                        'phone'
                    ]
                ]
            ]);
    }

    /**
     * Teste não deve atualizar usuario se não passar email
     */
    public function testShouldNotUpdateUserIfRequestParametersIsMissingEmail()
    {
        $user = $this->createUser();
        $role = Role::factory()->admin()->create();

        $attributes = [
            'name' => $this->faker->name,
            'password' => 'testenovo',
            'password_confirmation' => 'testenovo',
            'role_id' => $role->id
        ];

        $response = $this->actingAs($user)->putJson(
            route('api.users.update', $user->uuid),
            $attributes,
        );

        $response
            ->assertStatus(422)
            ->assertJson([
                'success' => false
            ])
            ->assertJsonStructure([
                'data' => [
                    'errors' => [
                        'email'
                    ]
                ]
            ]);
    }

    /**
     * Teste não deve atualizar usuario se não passar role
     */
    public function testShouldNotUpdateUserIfRequestParametersIsMissingRole()
    {
        $user = $this->createUser();

        $attributes = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => 'testenovo',
            'password_confirmation' => 'testenovo',
        ];

        $response = $this->actingAs($user)->putJson(
            route('api.users.update', $user->uuid),
            $attributes,
        );

        $response
            ->assertStatus(422)
            ->assertJson([
                'success' => false
            ])
            ->assertJsonStructure([
                'data' => [
                    'errors' => [
                        'role_id'
                    ]
                ]
            ]);
    }

    /**
     * Teste não deve atualizar usuario se senha não coincidir
     */
    public function testShouldNotUpdateUserIfPasswordDoesntMatch()
    {
        $user = $this->createUser();
        $role = Role::factory()->admin()->create();

        $attributes = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => 'testenovo',
            'password_confirmation' => 'testenovo1',
            'role_id' => $role->id,
        ];

        $response = $this->actingAs($user)->putJson(
            route('api.users.update', $user->uuid),
            $attributes,
        );

        $response
            ->assertStatus(422)
            ->assertJson([
                'success' => false
            ])
            ->assertJsonStructure([
                'data' => [
                    'errors' => [
                        'password'
                    ]
                ]
            ]);
    }

    /**
     * Teste não deve atualizar usuario role não existir
     */
    public function testShouldNotUpdateUserIfRoleDoesntExist()
    {
        $user = $this->createUser();

        $attributes = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => 'testenovo',
            'password_confirmation' => 'testenovo1',
            'role_id' => 9,
        ];

        $response = $this->actingAs($user)->putJson(
            route('api.users.update', $user->uuid),
            $attributes,
        );

        $response
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
            ])
            ->assertJsonStructure([
                'data' => [
                    'errors' => [
                        'role_id'
                    ]
                ]
            ]);
    }

    /**
     * Teste deve retornar 500 ao tentar atualizar usuário que não existe
     */
    public function testShouldReturn500IfTryingToUpdateUserThatDoesntExists()
    {
        $user = $this->createUser();
        $role = Role::factory()->admin()->create();
        $uuid = Str::uuid();

        $attributes = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'phone' => $this->faker->phoneNumber,
            'password' => 'testenovo',
            'password_confirmation' => 'testenovo',
            'role_id' => $role->id,
        ];

        $response = $this->actingAs($user)->putJson(
            route('api.users.update', $uuid),
            $attributes,
        );

        $response
            ->assertStatus(500)
            ->assertJson([
                'success' => false,
                'data' => null
            ])
            ->assertJsonStructure([
                'errors'
            ]);
    }

    /**
     * Teste deve retornar todos os usuários paginados
     */
    public function testShouldReturnAllUsersPaginated()
    {
        $users = User::factory()->count(3)->create();
        $token = $users[0]->createToken('teste')->plainTextToken;

        $headers = [
            'Authorization' => "Bearer {$token}"
        ];

        $response = $this->getJson(
            route('api.users.index'),
            $headers
        );

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'total' => 3
            ])
            ->assertJsonCount(3, 'data');
    }

    /**
     * Teste deve retornar usuarios filtrados por nome
     */
    public function testShouldReturnUsersFilteredByName()
    {
        $users = User::factory()->count(3)->create();
        User::factory()->create([
            'name' => 'Renan'
        ]);

        $response = $this->actingAs($users[0])->json('GET', route('api.users.index'), [
            'name' => 'Renan'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'total' => 1
            ])
            ->assertJsonCount(1, 'data');
    }

    /**
     * Deve retornar 500 ao excluir usuário que não existe no sistema
     */
    public function testShouldReturn500WhenDeletingNonExistentUser()
    {
        $user = $this->createUser();
        $uuid = Str::uuid();

        $response = $this->actingAs($user)->deleteJson(
            route('api.users.destroy', $uuid),
            [],
        );

        $response->assertStatus(500)
            ->assertJson([
                'success' => false,
                'data' => null,
                'code' => 500
            ]);
    }

    /**
     * Teste deve retornar usuários filtrados por email
     */
    public function testShouldReturnUsersFilteredByEmail()
    {
        $users = User::factory()->count(3)->create();
        User::factory()->create([
            'email' => 'renan@imaxinformatica.com.br'
        ]);

        $response = $this->actingAs($users[0])->json('GET', route('api.users.index'), [
            'email' => 'renan@imaxinformatica.com.br'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'total' => 1
            ])
            ->assertJsonCount(1, 'data');
    }

    /**
     * Teste deve retornar usuários filtrados por telefone
     */
    public function testShouldReturnUsersFilteredByPhone()
    {
        $users = User::factory()->count(3)->create();
        User::factory()->create([
            'phone' => '51982423924'
        ]);

        $response = $this->actingAs($users[0])->json('GET', route('api.users.index'), [
            'phone' => '51982423924'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'total' => 1
            ])
            ->assertJsonCount(1, 'data');
    }

    /**
     * Teste deve retornar usuários filtrados por role
     */
    public function testShouldReturnUsersFilteredByRole()
    {
        $users = User::factory()->count(3)->create();

        $response = $this->actingAs($users[0])->json('GET', route('api.users.index'), [
            'role_id' => $users[0]->role_id
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'total' => 1
            ])
            ->assertJsonCount(1, 'data');
    }

    /**
     * Teste deve retornar usuários páginados por numero de página
     */
    public function testShouldReturnUsersPaginatedByNumberOfPagesInRequestParameter()
    {
        $user = $this->createUser();
        User::factory()->count(50)->create();

        $response = $this->actingAs($user)->json('GET', route('api.users.index'), [
            'per_page' => 5
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonCount(5, 'data');
    }
}
