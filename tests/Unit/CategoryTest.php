<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Category;
use Illuminate\Support\Str;
use App\Services\Responses\ServiceResponse;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\Contracts\CategoryServiceInterface;

class CategoryTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

    protected $categoryService;

    public function setUp(): void
    {
        parent::setUp();
        $this->categoryService = app(CategoryServiceInterface::class);
    }

    /**
     * Teste deve criar uma categoria com sucesso
     */
    public function testShouldCreateCategoryWithSuccess()
    {
        $data = [
            'name' => $this->faker->name,
            'color' => $this->faker->colorName,
            'status' => rand(0, 1)
        ];

        $categoryServiceResponse = $this->categoryService->store($data);

        $this->assertDatabaseHas('categories', $data);
        $this->assertTrue($categoryServiceResponse->success);
        $this->assertNotEmpty($categoryServiceResponse->data);
        $this->assertEmpty($categoryServiceResponse->internalErrors);
        $this->assertInstanceOf(ServiceResponse::class, $categoryServiceResponse);
        $this->assertInstanceOf(Category::class, $categoryServiceResponse->data);
    }

    /**
     * Teste deve criar uma categoria com sucesso se passar parent id
     */
    public function testShouldCreateCategoryWithSuccessIfPassingParentCategoryParameter()
    {
        $category = Category::factory()->create();

        $data = [
            'name' => $this->faker->name,
            'color' => $this->faker->colorName,
            'status' => rand(0, 1),
            'parent_id' => $category->id
        ];

        $categoryServiceResponse = $this->categoryService->store($data);

        $this->assertDatabaseHas('categories', $data);
        $this->assertTrue($categoryServiceResponse->success);
        $this->assertNotEmpty($categoryServiceResponse->data);
        $this->assertEmpty($categoryServiceResponse->internalErrors);
        $this->assertInstanceOf(ServiceResponse::class, $categoryServiceResponse);
        $this->assertInstanceOf(Category::class, $categoryServiceResponse->data);
    }

    /**
     * Teste não deve criar categoria se não passar nome
     */
    public function testShouldNotCreateCategoryIfRequestIsMissingNameParameter()
    {
        $data = [
            'color' => $this->faker->colorName,
            'status' => rand(0, 1)
        ];

        $categoryServiceResponse = $this->categoryService->store($data);

        $this->assertDatabaseMissing('categories', $data);
        $this->assertFalse($categoryServiceResponse->success);
        $this->assertNotEmpty($categoryServiceResponse->data);
        $this->assertNotEmpty($categoryServiceResponse->internalErrors);
        $this->assertInstanceOf(ServiceResponse::class, $categoryServiceResponse);
    }

    /**
     * Teste não deve criar categoria se não passar cor
     */
    public function testShouldNotCreateCategoryIfRequestIsMissingColorParameter()
    {
        $data = [
            'name' => $this->faker->name,
            'status' => rand(0, 1)
        ];

        $categoryServiceResponse = $this->categoryService->store($data);

        $this->assertDatabaseMissing('categories', $data);
        $this->assertFalse($categoryServiceResponse->success);
        $this->assertNotEmpty($categoryServiceResponse->data);
        $this->assertNotEmpty($categoryServiceResponse->internalErrors);
        $this->assertInstanceOf(ServiceResponse::class, $categoryServiceResponse);
    }

    /**
     * Teste não deve criar categoria se passar categoria pai que não existe
     */
    public function testShouldNotCreateCategoryIfRequestHasParentIdThatDoesntExists()
    {
        $data = [
            'name' => $this->faker->name,
            'color' => $this->faker->colorName,
            'status' => rand(0, 1),
            'parent_id' => rand(5, 100)
        ];

        $categoryServiceResponse = $this->categoryService->store($data);

        $this->assertDatabaseMissing('categories', $data);
        $this->assertFalse($categoryServiceResponse->success);
        $this->assertNotEmpty($categoryServiceResponse->data);
        $this->assertNotEmpty($categoryServiceResponse->internalErrors);
        $this->assertInstanceOf(ServiceResponse::class, $categoryServiceResponse);
    }

    /**
     * Teste deve retornar uma categoria que existe com sucesso
     */
    public function testShouldReturnExistingCategoryWithSuccess()
    {
        $category = Category::factory()->create();

        $categoryServiceResponse = $this->categoryService->find($category->uuid);

        $this->assertTrue($categoryServiceResponse->success);
        $this->assertNotEmpty($categoryServiceResponse->data);
        $this->assertEmpty($categoryServiceResponse->internalErrors);
        $this->assertInstanceOf(Category::class, $categoryServiceResponse->data);
        $this->assertInstanceOf(ServiceResponse::class, $categoryServiceResponse);
        $this->assertEquals($category->uuid, $categoryServiceResponse->data->uuid);
    }

    /**
     * Teste não deve retornar categoria que não existe
     */
    public function testShouldNotReturnNonExistingCategory()
    {
        $uuid = Str::uuid();

        $categoryServiceResponse = $this->categoryService->find($uuid);

        $this->assertEmpty($categoryServiceResponse->data);
        $this->assertFalse($categoryServiceResponse->success);
        $this->assertEmpty($categoryServiceResponse->internalErrors);
        $this->assertInstanceOf(ServiceResponse::class, $categoryServiceResponse);
    }

    /**
     * Teste deve atualizar uma categoria com sucesso
     */
    public function testShouldUpdateCategoryWithSuccess()
    {
        $category = Category::factory()->create();
        $category2 = Category::factory()->create([
            'parent_id' => $category->id
        ]);

        $attributes = [
            'name' => $this->faker->name,
            'color' => $this->faker->colorName,
            'status' => rand(0, 1),
            'parent_id' => $category->id
        ];

        $categoryServiceResponse = $this->categoryService->update($category2->uuid, $attributes);

        $this->assertDatabaseHas('categories', $attributes);
        $this->assertTrue($categoryServiceResponse->success);
        $this->assertNotEmpty($categoryServiceResponse->data);
        $this->assertEmpty($categoryServiceResponse->internalErrors);
        $this->assertInstanceOf(Category::class, $categoryServiceResponse->data);
        $this->assertInstanceOf(ServiceResponse::class, $categoryServiceResponse);
    }

    /**
     * Teste deve atualizar uma categoria mesmo se não passar parent_id
     */
    public function testShouldUpdateCategoryEvenWithoutParentId()
    {
        $category = Category::factory()->create();

        $attributes = [
            'name' => $this->faker->name,
            'color' => $this->faker->colorName,
            'status' => rand(0, 1),
        ];

        $categoryServiceResponse = $this->categoryService->update($category->uuid, $attributes);

        $this->assertDatabaseHas('categories', $attributes);
        $this->assertTrue($categoryServiceResponse->success);
        $this->assertNotEmpty($categoryServiceResponse->data);
        $this->assertEmpty($categoryServiceResponse->internalErrors);
        $this->assertInstanceOf(Category::class, $categoryServiceResponse->data);
        $this->assertInstanceOf(ServiceResponse::class, $categoryServiceResponse);
    }

    /**
     * Teste não deve atualizar categoria se não existir
     */
    public function testShouldNotUpdateCategoryIfCategoryDoesntExists()
    {
        $uuid = Str::uuid();
        $attributes = [
            'name' => $this->faker->name,
            'color' => $this->faker->colorName,
            'status' => rand(0, 1),
        ];

        $categoryServiceResponse = $this->categoryService->update($uuid, $attributes);

        $this->assertEmpty($categoryServiceResponse->data);
        $this->assertFalse($categoryServiceResponse->success);
        $this->assertDatabaseMissing('categories', $attributes);
        $this->assertEmpty($categoryServiceResponse->internalErrors);
        $this->assertInstanceOf(ServiceResponse::class, $categoryServiceResponse);
    }

    /**
     * Teste deve excluir uma categoria com sucesso
     */
    public function testShouldDeleteCategoryWithSuccess()
    {
        $category = Category::factory()->create();

        $categoryServiceResponse = $this->categoryService->delete($category->uuid);

        $this->assertTrue($categoryServiceResponse->success);
        $this->assertEmpty($categoryServiceResponse->internalErrors);
        $this->assertDatabaseMissing('categories', $category->toArray());
        $this->assertInstanceOf(ServiceResponse::class, $categoryServiceResponse);
    }

    /**
     * Deve excluir uma categoria e suas categorias filhas junto
     */
    public function testShouldDeleteCategoryWithRelatedCategories()
    {
        $category = Category::factory()->create();
        $relatedCategory = Category::factory()->create([
            'parent_id' => $category->id
        ]);

        $categoryServiceResponse = $this->categoryService->delete($category->uuid);

        $this->assertTrue($categoryServiceResponse->success);
        $this->assertEmpty($categoryServiceResponse->internalErrors);
        $this->assertDatabaseMissing('categories', $category->toArray());
        $this->assertDatabaseMissing('categories', $relatedCategory->toArray());
        $this->assertInstanceOf(ServiceResponse::class, $categoryServiceResponse);
    }

    /**
     * Deve retornar erro ao tentar excluir categoria que não existe
     */
    public function testShouldReturnErrorsWhenTryingToDeleteNonExistingCategory()
    {
        $uuid = Str::uuid();

        $categoryServiceResponse = $this->categoryService->delete($uuid);

        $this->assertEmpty($categoryServiceResponse->data);
        $this->assertFalse($categoryServiceResponse->success);
        $this->assertEmpty($categoryServiceResponse->internalErrors);
        $this->assertInstanceOf(ServiceResponse::class, $categoryServiceResponse);
    }

    /**
     * Teste deve retornar categorias paginadas
     */
    public function testShouldReturnPaginatedCategories()
    {
        Category::factory()->count(50)->create();

        $categoryServiceResponse = $this->categoryService->getAllPaginated();

        $this->assertTrue($categoryServiceResponse->success);
        $this->assertCount(50, $categoryServiceResponse->data);
        $this->assertEmpty($categoryServiceResponse->internalErrors);
        $this->assertInstanceOf(ServiceResponse::class, $categoryServiceResponse);
    }

    /**
     * Teste deve retornar categorias paginadas conforme parametro
     */
    public function testShouldReturnPaginatedCategoriesByPerPageParameter()
    {
        Category::factory()->count(50)->create();
        $per_page = 10;

        $categoryServiceResponse = $this->categoryService->getAllPaginated($per_page);

        $this->assertTrue($categoryServiceResponse->success);
        $this->assertCount(10, $categoryServiceResponse->data);
        $this->assertEmpty($categoryServiceResponse->internalErrors);
        $this->assertInstanceOf(ServiceResponse::class, $categoryServiceResponse);
    }
}
