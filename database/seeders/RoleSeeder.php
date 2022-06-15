<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert([
            [
                'uuid' => Str::uuid(),
                'name' => 'superAdmin',
                'label' => 'Super admin'
            ],
            [
                'uuid' => Str::uuid(),
                'name' => 'admin',
                'label' => 'Administrador'
            ],
            [
                'uuid' => Str::uuid(),
                'name' => 'seller',
                'label' => 'Vendedor'
            ],
        ]);
    }
}
