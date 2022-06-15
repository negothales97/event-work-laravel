<?php

namespace App\Repositories\Contracts;

use App\Models\Category;

interface CategoryRepository extends BaseRepositoryInterface
{
    public function deleteRelatedCategories(Category $category): void;

}
