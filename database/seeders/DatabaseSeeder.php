<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Branch;
use App\Models\Employee;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Branch::create([
            'name' => 'Main Branch',
        ]);

        Employee::create([
            'name' => 'CEO',
            'phone' => '+998953558899',
            'password' => Hash::make(8899),
            'role' => ['ceo'],
            'gender' => 'male',
        ]);

        $this->call([
            RoleSeeder::class,
            TimeCourseSeeder::class,
            ExpenseCategoriesSeeder::class,
        ]);
    }
}
