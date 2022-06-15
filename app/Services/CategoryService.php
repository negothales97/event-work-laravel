<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Services\Responses\ServiceResponse;
use App\Repositories\Contracts\CategoryRepository;
use App\Services\Contracts\CategoryServiceInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CategoryService extends BaseService implements CategoryServiceInterface
{
    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Retorna todas as categorias cadastras no banco
     *
     * @param int $per_page
     *
     * @return ServiceResponse
     */
    public function getAllPaginated(int $per_page = 50): ServiceResponse
    {
        try {
            $categories = $this->categoryRepository
                ->paginate($per_page);
        } catch (\Throwable $e) {
            return $this->defaultErrorReturn($e, compact('per_page'));
        }

        return new ServiceResponse(
            true,
            'Lista de categorias',
            $categories
        );
    }

    /**
     * Retorna os dados de uma categoria cadastrada do banco
     *
     * @param string $uuid
     *
     * @return ServiceResponse
     */
    public function find(string $uuid): ServiceResponse
    {
        try {
            $category = $this->categoryRepository->findByField('uuid', $uuid)->first();

            if (is_null($category)) {
                throw new ModelNotFoundException;
            }
        } catch (ModelNotFoundException $e) {
            return new ServiceResponse(
                false,
                'Categoria não localizada'
            );
        } catch (\Throwable $e) {
            return $this->defaultErrorReturn($e, compact('uuid'));
        }

        return new ServiceResponse(
            true,
            'Categoria encontrada com sucesso',
            $category
        );
    }

    /**
     * Cria uma categoria no banco de dados
     *
     * @param array $data
     *
     * @return ServiceResponse
     */
    public function store(array $data): ServiceResponse
    {
        DB::beginTransaction();
        try {
            $category = $this->categoryRepository->create($data);
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->defaultErrorReturn($e, $data);
        }
        DB::commit();

        return new ServiceResponse(
            true,
            'Categoria criada com sucesso',
            $category
        );
    }

    /**
     * Atualiza uma categoria que está cadastrada no banco
     *
     * @param string $uuid
     * @param array $data
     *
     * @return ServiceResponse
     */
    public function update(string $uuid, array $data): ServiceResponse
    {
        DB::beginTransaction();
        try {
            $categoryResponse = $this->find($uuid);

            if (!$categoryResponse->success) {
                return $categoryResponse;
            }

            $id = $categoryResponse->data->id;
            $category = $this->categoryRepository->update($data, $id);
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->defaultErrorReturn($e, compact('uuid', 'data'));
        }

        DB::commit();
        return new ServiceResponse(
            true,
            'Categoria atualizada com sucesso',
            $category
        );
    }

    /**
     * Deleta uma categoria do banco de dados
     *
     * @param string $uuid
     *
     * @return ServiceResponse
     */
    public function delete(string $uuid): ServiceResponse
    {
        DB::beginTransaction();
        try {
            $categoryResponse = $this->find($uuid);
            if (!$categoryResponse->success) {
                return $categoryResponse;
            }

            $category = $categoryResponse->data;
            $this->categoryRepository->deleteRelatedCategories($category);
            $this->categoryRepository->delete($category->id);
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->defaultErrorReturn($e);
        }
        DB::commit();
        return new ServiceResponse(
            true,
            'Categoria removida com sucesso'
        );
    }
}
