<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Workout;
use Carbon\Carbon;

class WorkoutSeeder extends Seeder
{
    public function run()
    {
        Workout::insert([
            // 🏃 Löpning (id = 1)
            [
                'activity_id' => 1,
                'date' => '2024-01-05',
                'description' => 'Lugn morgonjogg',
                'effort_level' => 4,
                'distance_value' => 5,     // km
                'distance_unit' => 'km',
                'duration_value' => 30,    // min
                'duration_unit' => 'min',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

            [
                'activity_id' => 1,
                'date' => '2024-01-10',
                'description' => 'Intervallpass',
                'effort_level' => 8,
                'distance_value' => 3000,  // meter
                'distance_unit' => 'm',
                'duration_value' => 0.75,  // timmar
                'duration_unit' => 'h',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

            // 🚴 Cykling (id = 2)
            [
                'activity_id' => 2,
                'date' => '2024-01-12',
                'description' => 'Landsvägscykling',
                'effort_level' => 6,
                'distance_value' => 20,
                'distance_unit' => 'km',
                'duration_value' => 60,
                'duration_unit' => 'min',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

            // 🏊 Simning (id = 3)
            [
                'activity_id' => 3,
                'date' => '2024-01-15',
                'description' => 'Bassängsim',
                'effort_level' => 5,
                'distance_value' => 1500, // meter
                'distance_unit' => 'm',
                'duration_value' => 1,    // timmar
                'duration_unit' => 'h',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

            // 🚶 Promenad (id = 4)
            [
                'activity_id' => 4,
                'date' => '2024-01-18',
                'description' => 'Kvällspromenad',
                'effort_level' => 2,
                'distance_value' => 3,
                'distance_unit' => 'km',
                'duration_value' => 45,
                'duration_unit' => 'min',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

            // 🏋️ Styrketräning (id = 5)
            [
                'activity_id' => 5,
                'date' => '2024-01-20',
                'description' => 'Helkroppspass',
                'effort_level' => 7,
                'distance_value' => null,
                'distance_unit' => null,
                'duration_value' => 90,
                'duration_unit' => 'min',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
