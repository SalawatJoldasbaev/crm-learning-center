<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Branch;
use App\Models\Course;
use App\Models\Employee;
use App\Models\Room;
use App\Models\Student;
use App\Models\Teacher;
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

        Teacher::create([
            'name' => 'TEST',
            'phone' => '+998907091931',
            'password' => Hash::make(8899),
            'salary_percentage' => 40,
            'gender' => 'male',
        ]);

        Student::create([
            'first_name' => 'Salawat',
            'last_name' => 'Joldasbaev',
            'phone' => '+998907091931',
            'password' => Hash::make(8899),
            'address' => 'Shomanay',
            'birthday' => '2003-09-19',
            'addition_phone' => json_encode([
                'label' => 'Mother',
                'phone' => '+998993898984'
            ]),
            'gender' => 'male',
            'balance' => 0,
        ]);
        Course::create([
            'name' => 'Back-end php',
            'file_id' => null,
            'description' => 'TEST',
            'lesson_duration' => 90,
            'month' => 6,
            'price' => 399000,
            'lessons_per_module' => 12,
        ]);
        Room::create([
            'name' => 'facebook room',
            'capacity' => 12,
        ]);
        $this->call([
            RoleSeeder::class,
            TimeCourseSeeder::class,
            ExpenseCategoriesSeeder::class,
        ]);
    }
}
