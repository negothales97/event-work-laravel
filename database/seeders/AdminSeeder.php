<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('admins')->insert([
            'uuid' => Str::uuid(),
            'name' => 'Thales Serra',
            'email' => 'thales@helpdesk.com.br',
            'password' => bcrypt('password')
        ]);
    }
}
