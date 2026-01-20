<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WorkoutSeeder extends Seeder
{
    public function run()
    {
        // Här skapar vi några exempelpass
        DB::table('workouts')->insert([
            [
                'activity_id' => 1, // Löpning
                'date' => '2026-01-20',
                'effort_level' => 7,
                'distance_value' => 5,
                'distance_unit' => 'km',
                'duration_value' => 30,
                'duration_unit' => 'min',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'activity_id' => 2, // Cykling
                'date' => '2026-01-21',
                'effort_level' => 6,
                'distance_value' => 15,
                'distance_unit' => 'km',
                'duration_value' => 45,
                'duration_unit' => 'min',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ]);
    }
}
