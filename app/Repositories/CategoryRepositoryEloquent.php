<?php

namespace App\Repositories;

use App\Models\Category;
use Prettus\Repository\Eloquent\BaseRepository;
use App\Repositories\Contracts\CategoryRepository;

class CategoryRepositoryEloquent extends BaseRepository implements CategoryRepository
{
    public function model()
    {
        return Category::class;
    }

    /**
     * Remove categorias relacionadas
     *
     * @param Category $category
     *
     * @return void
     */
    public function deleteRelatedCategories(Category $category): void
    {
        $category->categories()->delete();
    }
}
