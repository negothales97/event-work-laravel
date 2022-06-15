<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Str;
use Tests\Feature\Traits\UtilsTrait;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryTest extends TestCase
{
    use WithFaker;
    use UtilsTrait;
    use RefreshDatabase;

    /**
     * Teste não deve criar categoria se usuário não estiver autenticado
     */
    public function testShouldNotCreateCategoryIfUserIsntAuthenticated()
    {
        $attributes = [
            'name' => $this->faker->name,
            'color' => $this->faker->colorName,
            'status' => rand(0, 1)
        ];

        $response = $this->postJson(route('api.categories.store'), $attributes);

        $response->assertStatus(401);
    }

    /**
     * Testa se criou uma categoria sem nenhum erro
     */
    public function testShouldCreateCategoryWithSuccess()
    {
        $user = $this->createUser();
        $attributes = [
            'name' => $this->faker->name,
            'color' => $this->faker->colorName,
            'status' => rand(0, 1)
        ];

        $response = $this->actingAs($user)->postJson(
            route('api.categories.store'),
            $attributes,
        );

        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'code' => 200
            ])
            ->assertJsonMissing(['errors'])
            ->assertJsonStructure([
                'data' => [
                    'name',
                    'color',
                    'status',
                ]
            ]);
    }

    /**
     * Deve criar categoria com sucesso passando categoria pai
     */
    public function testShouldCreateCategoryWithSuccessPassingParentCategoryParameter()
    {
        $user = $this->createUser();
        $category = Category::factory()->create();

        $attributes = [
            'name' => $this->faker->name,
            'color' => $this->faker->colorName,
            'status' => rand(0, 1),
            'parent_id' => $category->id
        ];

        $response = $this->actingAs($user)->postJson(
            route('api.categories.store'),
            $attributes,
        );

        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'code' => 200
            ])
            ->assertJsonMissing(['errors'])
            ->assertJsonStructure([
                'data' => [
                    'name',
                    'color',
                    'status',
                ]
            ]);
    }

    /**
     * Não deve criar categoria se não passar nome
     */
    public function testShouldNotCreateCategoryIfRequestIsMissingNameParameter()
    {
        $user = $this->createUser();
        $attributes = [
            'color' => $this->faker->colorName,
            'status' => rand(0, 1),
        ];


        $response = $this->actingAs($user)->postJson(route('api.categories.store'), $attributes);

        $response
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
            ])
            ->assertJsonStructure([
                'message',
                'data' => ['errors' => ['name']]
            ]);
    }

    /**
     * Não deve criar categoria se não passar cor
     */
    public function testShouldNotCreateCategoryIfRequestIsMissingColorParameter()
    {
        $user = $this->createUser();
        $attributes = [
            'name' => $this->faker->name,
            'status' => rand(0, 1),
        ];

        $response = $this->actingAs($user)->postJson(route('api.categories.store'), $attributes);

        $response
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
            ])
            ->assertJsonStructure([
                'message',
                'data' => ['errors' => ['color']]
            ]);
    }

    /**
     * Não deve criar categoria se categoria pai não existir
     */
    public function testShouldNotCreateCategoryIfParentCategoryDoesntExists()
    {
        $user = $this->createUser();
        $attributes = [
            'name' => $this->faker->name,
            'color' => $this->faker->colorName,
            'status' => rand(0, 1),
            'parent_id' => rand(4, 20)
        ];

        $response = $this->actingAs($user)->postJson(route('api.categories.store'), $attributes);

        $response
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
            ])
            ->assertJsonStructure([
                'message',
                'data' => ['errors' => ['parent_id']]
            ]);
    }

    /**
     * Não deve criar categoria se não informar status
     */
    public function testShouldNotCreateCategoryIfRequestIsMissingStatusParameter()
    {
        $user = $this->createUser();
        $attributes = [
            'name' => $this->faker->name,
            'color' => $this->faker->colorName,
            'parent_id' => rand(4, 20)
        ];

        $response = $this->actingAs($user)->postJson(route('api.categories.store'), $attributes);

        $response
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
            ])
            ->assertJsonStructure([
                'message',
                'data' => ['errors' => ['status']]
            ]);
    }

    /**
     * Teste deve retornar uma categoria com sucesso
     */
    public function testShouldReturnCategoryWithSuccess()
    {
        $user = $this->createUser();
        $category = Category::factory()->create();

        $response = $this->actingAs($user)->getJson(
            route('api.categories.show', $category->uuid)
        );

        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'code' => 200
            ])
            ->assertJsonStructure([
                'data' => [
                    'name',
                    'color',
                    'status',
                ]
            ]);
    }

    /**
     * Teste deve retornar 500 se categoria não existir
     */
    public function testShoulReturn500IfCategoryDoesntExists()
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
                'data' => null,
                'code' => 500
            ])
            ->assertJsonStructure([
                'errors'
            ]);
    }

    /**
     * Teste deve atualizar uma categoria com sucesso
     */
    public function testShouldUpdateCategoryWithSuccess()
    {
        $user = $this->createUser();
        $category = Category::factory()->create();
        $attributes = [
            'name' => $this->faker->name,
            'color' => $this->faker->colorName,
            'status' => rand(0, 1),
        ];

        $response = $this->actingAs($user)->putJson(
            route('api.categories.update', $category->uuid),
            $attributes,
        );

        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'code' => 200
            ])
            ->assertJsonStructure([
                'data' => [
                    'name',
                    'color',
                    'status',
                ]
            ]);
    }

    /**
     * Teste não deve atualizar categoria se não passar nome
     */
    public function testShouldNotUpdateIfRequestIsMissingNameParameter()
    {
        $user = $this->createUser();
        $category = Category::factory()->create();

        $attributes = [
            'color' => $this->faker->colorName,
            'status' => rand(0, 1),
        ];

        $response = $this->actingAs($user)->putJson(
            route('api.categories.update', $category->uuid),
            $attributes,
        );

        $response
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
            ])
            ->assertJsonStructure([
                'message',
                'data' => [
                    'errors' => [
                        'name'
                    ]
                ]
            ]);
    }

    /**
     * Teste não deve atualizar categoria se não passar cor
     */
    public function testShouldNotUpdateIfRequestIsMissingColorParameter()
    {
        $user = $this->createUser();
        $category = Category::factory()->create();

        $attributes = [
            'name' => $this->faker->colorName,
            'status' => rand(0, 1),
        ];

        $response = $this->actingAs($user)->putJson(
            route('api.categories.update', $category->uuid),
            $attributes,
        );

        $response
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
            ])
            ->assertJsonStructure([
                'message',
                'data' => [
                    'errors' => [
                        'color'
                    ]
                ]
            ]);
    }

    /**
     * Teste deve retornar 500 se atualizar categoria que não existe
     */
    public function testShouldReturn500WhenTryingToUpdateNonExistingCategory()
    {
        $user = $this->createUser();
        $uuid = Str::uuid();

        $attributes = [
            'name' => $this->faker->colorName,
            'color' => $this->faker->colorName,
            'status' => rand(0, 1),
        ];

        $response = $this->actingAs($user)->putJson(
            route('api.categories.update', $uuid),
            $attributes,
        );

        $response
            ->assertStatus(500)
            ->assertJson([
                'success' => false,
            ])
            ->assertJsonStructure([
                'errors'
            ]);
    }

    /**
     * Teste não deve atualizar categoria se parent id não existir
     */
    public function testShouldNotUpdateCategoryIfParentIdDoesntExists()
    {
        $user = $this->createUser();
        $uuid = Str::uuid();

        $attributes = [
            'name' => $this->faker->colorName,
            'color' => $this->faker->colorName,
            'status' => rand(0, 1),
            'parent_id' => rand(10, 20)
        ];

        $response = $this->actingAs($user)->putJson(
            route('api.categories.update', $uuid),
            $attributes,
        );

        $response
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
            ])
            ->assertJsonStructure([
                'message',
                'data' => [
                    'errors' => [
                        'parent_id'
                    ]
                ]
            ]);
    }

    /**
     * Teste deve deletar categoria com sucesso
     */
    public function testShouldDeleteCategoryWithSuccess()
    {
        $user = $this->createUser();
        $category = Category::factory()->create();

        $response = $this->actingAs($user)->deleteJson(
            route('api.categories.destroy', $category->uuid),
            [],
        );

        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'code' => 200
            ]);
    }

    /**
     * Teste deve retornar 500 ao excluir categoria que não existe
     */
    public function testShouldReturn500WhenTryingToDeleteNonExistingCategory()
    {
        $user = $this->createUser();
        $uuid = Str::uuid();

        $response = $this->actingAs($user)->deleteJson(
            route('api.categories.destroy', $uuid),
            [],
        );

        $response
            ->assertStatus(500)
            ->assertJson([
                'success' => false,
                'data' => null,
                'code' => 500
            ]);
    }

    /**
     * Teste deve retornar categorias paginadas
     */
    public function testShouldReturnPaginatedCategories()
    {
        $user = $this->createUser();
        Category::factory()->count(50)->create();

        $response = $this->actingAs($user)->getJson(route('api.categories.index'));

        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'code' => 200,
                'per_page' => 50
            ])
            ->assertJsonCount(50, 'data');
    }

    /**
     * Teste deve retornar categorias paginadas
     */
    public function testShouldReturnPaginatedCategoriesByNumberOfPagesInRequestParameter()
    {
        $user = $this->createUser();
        Category::factory()->count(50)->create();

        $response = $this->actingAs($user)->json('GET', route('api.categories.index'), [
            'per_page' => 10
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'code' => 200,
                'per_page' => 10
            ])
            ->assertJsonCount(10, 'data');
    }
}
