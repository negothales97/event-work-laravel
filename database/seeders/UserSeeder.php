<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Company;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            [
                'uuid' => Str::uuid(),
                'name' => 'Thales Serra',
                'email' => 'admin@email.com.br',
                'company_id' => Company::first()->uuid,
                'role_id' => Role::first()->uuid,
                'password' => bcrypt('password'),
            ],
        ]);
    }
}
