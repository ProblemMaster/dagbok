<?php

namespace App\Http\Controllers;

use App\Models\Workout;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;

class TimelineController extends Controller {
    public function index(Request $request) {
        $query = Workout::with('activity');

        if ($request->has('activity_id')) {
            $query->where('activity_id', $request->activity_id);
        }

        if ($request->has('form')) {
            $query->where('date', '>=', $request->from);
        }

        if ($request->has('to')) {
            $query->where('date', '<=', $request->to);
        }

        $workouts = $query->orderBy('date')->get();

        if($workouts->isEmpty()) {
            return response()->json([]);
        }

        return response()->json([
            'activity_id' => $workouts->first()->activity->id,
            'activity_name' => $workouts->first()->activity->name,

            'labels' => $workouts->pluck('date'),

            'distance' => [
                'unit' => 'km',
                'data' => $workouts->pluck('distance_value')
            ],

            'duration' => [
                'unit' => 'min',
                'data' => $workouts->pluck('duration_value')
            ]
        ]);
    }

    public function allActivities(Request $request) {
        $activities = \App\Models\Activity::with(['workouts' => function($query) use ($request) {
            if ($request->has('from')) {
                $query->where('date', '>=', $request->from);
            }

            if ($request->has('to')) {
                $query->where('date', '<=', $request->to);
            }
            $query->orderBy('date');
        }])->get();

        $result = $activities->map(function($activity) {
            if($activity->workouts->isEmpty()) {
                return [
                    'activity_id' => $activity->id,
                    'activity_name' => $activity->name,
                    'labels' => [],
                    'distance' => ['unit' => 'km', 'data' => []],
                    'duration' => ['unit' => 'min', 'data' => []],
                ];
            }

            return [
                'activity_id' => $activity->id,
                'activity_name' => $activity->name,
                'labels' => $activity->workouts->pluck('date'),
                'distance' => [
                    'units' => 'km',
                    'data' => $activity->workouts->pluck('distance_value'),
                ],
                'duration' => [
                    'unit' => 'min',
                    'data' => $activity->workouts->pluck('druation_value')
                ]
            ];
        });

        return response()->json($result);
    }
}
