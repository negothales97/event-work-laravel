<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\ApiController;
use App\Http\Responses\DefaultResponse;
use App\Http\Resources\Category\CategoryResource;
use App\Services\Contracts\CategoryServiceInterface;
use App\Http\Requests\Category\CreateCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;

class CategoryController extends ApiController
{
    /**
     * @var CategoryServiceInterface
     */
    protected $categoryService;

    public function __construct(CategoryServiceInterface $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * Retorna as categorias cadastradas no banco de dados paginadas
     * GET api/categories
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $per_page = request('per_page') ?? 50;
        $categoryServiceResponse = $this->categoryService->getAllPaginated($per_page);

        if (!$categoryServiceResponse->success) {
            return $this->errorResponseFromService($categoryServiceResponse);
        }

        return $this->response(
            new DefaultResponse(
                CategoryResource::collection($categoryServiceResponse->data)
            )
        );
    }

    /**
     * Cria uma categoria no banco de dados
     * POST api/categories
     *
     * @param CreateCategoryRequest $request
     *
     * @return JsonResponse
     */
    public function store(CreateCategoryRequest $request): JsonResponse
    {
        $categoryServiceResponse = $this->categoryService->store($request->validated());

        if (!$categoryServiceResponse->success) {
            return $this->errorResponseFromService($categoryServiceResponse);
        }

        return $this->response(
            new DefaultResponse(
                new CategoryResource($categoryServiceResponse->data)
            )
        );
    }

    /**
     * Retorna os dados de uma categoria cadastrada no banco
     * GET api/categories/{uuid}
     *
     * @param string $uuid
     *
     * @return JsonResponse
     */
    public function show(string $uuid): JsonResponse
    {
        $categoryServiceResponse = $this->categoryService->find($uuid);

        if (!$categoryServiceResponse->success) {
            return $this->errorResponseFromService($categoryServiceResponse);
        }

        return $this->response(
            new DefaultResponse(
                new CategoryResource($categoryServiceResponse->data)
            )
        );
    }

    /**
     * Atualiza uma categoria do banco de dados
     * PUT api/categories/{uuid}
     *
     * @param string $uuid
     * @param UpdateCategoryRequest $request
     *
     * @return JsonResponse
     */
    public function update(string $uuid, UpdateCategoryRequest $request): JsonResponse
    {
        $categoryServiceResponse = $this->categoryService->update($uuid, $request->validated());

        if (!$categoryServiceResponse->success) {
            return $this->errorResponseFromService($categoryServiceResponse);
        }

        return $this->response(
            new DefaultResponse(
                new CategoryResource($categoryServiceResponse->data)
            )
        );
    }

    /**
     * Exclui uma categoria do banco de dados
     *
     * @param string $uuid
     *
     * @return JsonResponse
     */
    public function destroy(string $uuid): JsonResponse
    {
        $categoryServiceResponse = $this->categoryService->delete($uuid);

        if (!$categoryServiceResponse->success) {
            return $this->errorResponseFromService($categoryServiceResponse);
        }

        return $this->response(
            new DefaultResponse()
        );
    }
}
