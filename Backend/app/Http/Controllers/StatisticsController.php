<?php

namespace App\Http\Controllers;

use App\Models\Workout;
use App\Models\Activity;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;

class StatisticsController extends controller {
    // Get /statistics/activity/{id}
    public function activity($id) {
        $workouts = Workout::where('activity_id', $id)->get();

        if($workouts->isEmpty()) {
            return response()->json([
                'activity_id' => $id,
                'total_distance' => 0,
                'total_duration' => 0,
                'average_effort' => 0
            ]);
        }

        return response()->json([
            'activity_id' => $id,

            'distance' => [
                'value' => $workouts->sum('distance_value'),
                'unit' => 'km'
            ],

            'duration' => [
                'value' => $workouts->sum('duration_value'),
                'unit' => 'min'
            ],

            'average_effort' => round($workouts->avg('effort_level'), 1)
        ]);
    }

    // GET /statistics/all
    public function all() {
        $activities = Activity::with('workouts')->get();

        $result = $activities->map(function ($activity) {
            return [
                'activity_id' => $activity->id,
                'activity_name' => $activity->name,

                'distance' => [
                    'value' => $activity->workouts->sum('distance_value'),
                    'unit' => 'km'
                ],

                'duration' => [
                    'value' => $activity->workouts->sum('duration_value'),
                    'unit' => 'min'
                ],

                'average_effort' => $activity->workouts->count() > 0
                    ? round($activity->workouts->avg('effort_level'), 1)
                    : 0
             ];
        });

        return response()->json($result);
    }
}
