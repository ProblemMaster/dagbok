<?php

namespace App\Http\Controllers;

use App\Models\Workout;
use App\Models\Activity;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;
use Carbon\Carbon;

class TimelineController extends Controller
{
    // Timeline för en aktivitet
    public function index($activity_id, Request $request)
    {
        $query = Workout::with('activity')->where('activity_id', $activity_id);

        if ($request->has('from')) {
            $from = Carbon::createFromFormat('d-m-Y', $request->from)->format('Y-m-d');
            $query->where('date', '>=', $from);
        }

        if ($request->has('to')) {
            $to = Carbon::createFromFormat('d-m-Y', $request->to)->format('Y-m-d');
            $query->where('date', '<=', $to);
        }

        $workouts = $query->orderBy('date')->get();

        if ($workouts->isEmpty()) {
            return response()->json([
                'activity_id' => $activity_id,
                'activity_name' => Activity::find($activity_id)?->name ?? '',
                'labels' => [],
                'distance' => ['unit' => 'km', 'data' => []],
                'duration' => ['unit' => 'min', 'data' => []]
            ]);
        }

        return response()->json([
            'activity_id' => $workouts->first()->activity->id,
            'activity_name' => $workouts->first()->activity->name,
            'labels' => $workouts->pluck('date')->map(fn($d) => Carbon::parse($d)->format('d-m-Y')),
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

    // Timeline för alla aktiviteter
    public function allActivities(Request $request)
    {
        $activities = Activity::with(['workouts' => function($query) use ($request) {
            if ($request->has('from')) {
                $from = Carbon::createFromFormat('d-m-Y', $request->from)->format('Y-m-d');
                $query->where('date', '>=', $from);
            }
            if ($request->has('to')) {
                $to = Carbon::createFromFormat('d-m-Y', $request->to)->format('Y-m-d');
                $query->where('date', '<=', $to);
            }
            $query->orderBy('date');
        }])->get();

        $result = $activities->map(function($activity) {
            if ($activity->workouts->isEmpty()) {
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
                'labels' => $activity->workouts->pluck('date')->map(fn($d) => Carbon::parse($d)->format('d-m-Y')),
                'distance' => [
                    'unit' => 'km',
                    'data' => $activity->workouts->pluck('distance_value'),
                ],
                'duration' => [
                    'unit' => 'min',
                    'data' => $activity->workouts->pluck('duration_value'),
                ]
            ];
        });

        return response()->json($result);
    }
}
