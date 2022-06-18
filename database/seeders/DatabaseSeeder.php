<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Company;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // User::factory()->count(100)->create();
        Company::factory()->create();
        $this->call([
            AdminSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
        ]);
    }
}
