<?php

namespace Database\Seeders;

use App\Models\TimeCourse;
use Illuminate\Database\Seeder;

class TimeCourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $start = strtotime('07:00');
        $end = strtotime('20:00');
        $next_time = $start;
        while ($end >= $next_time) {
            TimeCourse::create([
                'time' => date('H:i', $next_time),
            ]);
            $next_time = strtotime('+30mins', $next_time);
        }
    }
}
