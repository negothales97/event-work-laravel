<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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
                'name' => 'Renan Rodrigues',
                'email' => 'renan@imaxinformatica.com.br',
                'phone' => '(99)999999999',
                'role_id' => 1,
                'password' => Hash::make('.Welcome09'),
            ],
            [
                'uuid' => Str::uuid(),
                'name' => 'Thales Serra',
                'email' => 'thales@imaxinformatica.com.br',
                'phone' => '(99)999999999',
                'role_id' => 1,
                'password' => Hash::make('.Welcome09'),
            ],
            [
                'uuid' => Str::uuid(),
                'name' => 'Lucas Borelli',
                'email' => 'lucas@imaxinformatica.com.br',
                'phone' => '(99)999999999',
                'role_id' => 1,
                'password' => Hash::make('.Welcome09'),
            ],
        ]);
    }
}
