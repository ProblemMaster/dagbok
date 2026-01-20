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
            'effort_level'   => 'required|integer|min:0|max:10',
            'distance_value' => 'nullable|numeric|min:0',
            'distance_unit'  => 'nullable|string|max:10',
            'duration_value' => 'nullable|numeric|min:0',
            'duration_unit'  => 'nullable|string|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $workout = Workout::create($validator->validated());

        return response()->json($workout, 201);
    }

    // Uppdatera workout
    public function update(Request $request, $id) {
        $workout = Workout::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'activity_id'    => 'required|exists:activities,id',
            'date'           => 'required|date',
            'effort_level'   => 'required|integer|min:0|max:10',
            'distance_value' => 'nullable|numeric|min:0',
            'distance_unit'  => 'nullable|string|max:10',
            'duration_value' => 'nullable|numeric|min:0',
            'duration_unit'  => 'nullable|string|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $workout->update($validator->validated());

        return response()->json($workout);
    }

    // Ta bort workout
    public function destroy($id) {
        $workout = Workout::findOrFail($id);
        $workout->delete();

        return response()->json(['message' => 'Workout deleted']);
    }
}
