<?php

namespace App\Http\Controllers;

use App\Models\Workout;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Laravel\Lumen\Routing\Controller;

class WorkoutController extends Controller {
    // Lista alla workouts (med optional filter på aktivitet eller datumintervall)
    public function index(Request $request) {
        $query = Workout::with('activity');

        if ($request->has('activity_id')) {
            $query->where('activity_id', $request->activity_id);
        }

        if ($request->has('from')) {
            $query->where('date', '>=', $request->from);
        }

        if ($request->has('to')) {
            $query->where('date', '<=', $request->to);
        }

        return response()->json($query->get());
    }

    // Skapa nytt workout-pass
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'activity_id'    => 'required|exists:activities,id',
            'date'           => 'required|date',
            'description' => 'nullable|string|max:1000',
            'effort_level'   => 'required|integer|min:0|max:10',
            'distance_value' => 'nullable|numeric|min:0',
            'distance_unit'  => 'nullable|in:m,km',
            'duration_value' => 'nullable|numeric|min:0',
            'duration_unit'  => 'nullable|in:min,h',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        $data['distance_value'] = $this->convertDistanceToKm(
            $data['distance_value'] ?? null,
            $data['distance_unit'] ?? null
        );

        $data['duration_value'] = $this->convertDurationToMinutes(
            $data['duration_value'] ?? null,
            $data['duration_unit'] ?? null
        );

        $data['distance_unit'] = 'km';
        $data['duration_unit'] = 'min';

        $workout = Workout::create($data);

        return response()->json($workout, 201);
    }

    // Uppdatera workout
    public function update(Request $request, $id) {
        $workout = Workout::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'activity_id'    => 'required|exists:activities,id',
            'date'           => 'required|date',
            'description' => 'nullable|string|max:1000',
            'effort_level'   => 'required|integer|min:0|max:10',
            'distance_value' => 'nullable|numeric|min:0',
            'distance_unit'  => 'nullable|in:m,km',
            'duration_value' => 'nullable|numeric|min:0',
            'duration_unit'  => 'nullable|in:min,h',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        $data['distance_value'] = $this->convertDistanceToKm(
            $data['distance_value'] ?? null,
            $data['distance_unit'] ?? null
        );

        $data['duration_value'] = $this->convertDurationToMinutes(
            $data['duration_value'] ?? null,
            $data['duration_unit'] ?? null
        );

        $data['distance_unit'] = 'km';
        $data['duration_unit'] = 'min';

        $workout->update($data);

        return response()->json($workout);
    }

    // Ta bort workout
    public function destroy($id) {
        $workout = Workout::findOrFail($id);
        $workout->delete();

        return response()->json(['message' => 'Workout deleted']);
    }

    private function convertDistanceToKm($value, $unit) {
        if($value === null) {
            return null;
        }

        return $unit === 'm'
            ? $value / 1000
            : $value; // km
    }

    private function convertDurationToMinutes($value, $unit) {
        if($value === null) {
            return null;
        }

        return $unit === 'h'
            ? $value * 60
            : $value; // min
    }
}
