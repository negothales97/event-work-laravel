<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
     /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Category::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'color' => $this->faker->colorName,
            'status' => rand(0, 1),
            'parent_id' => null
        ];
    }
}
