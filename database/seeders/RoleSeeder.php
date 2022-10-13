<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::upsert([
            ['name' => 'ceo'],
            ['name' => 'administrator'],
            ['name' => 'superAdministrator'],
            ['name' => 'teacher'],
            ['name' => 'supervisor'],
        ], ['name']);
    }
}
