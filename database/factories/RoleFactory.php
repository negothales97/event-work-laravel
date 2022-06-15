<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Role::class;

    public function definition()
    {
        return [
            'uuid' => Str::uuid(),
            'name' => $this->faker->name,
            'label' => $this->faker->name
        ];
    }

    public function admin()
    {
        return $this->state(function (array $attributes) {
            return [
                'uuid' => Str::uuid(),
                'name' => 'admin',
                'label' => 'Administrador'
            ];
        });
    }
    public function seller()
    {
        return $this->state(function (array $attributes) {
            return [
                'uuid' => Str::uuid(),
                'name' => 'seller',
                'label' => 'Vendedor'
            ];
        });
    }
}
