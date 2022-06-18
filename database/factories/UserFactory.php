<?php

namespace Database\Factories;

use App\Models\Role;
use App\Models\User;
use App\Models\Company;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'role_id' => Role::factory(),
            'company_id' => Company::factory(),
            'password' => bcrypt('passsword'),
        ];
    }
}
