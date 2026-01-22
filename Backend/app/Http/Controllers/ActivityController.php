<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;
use Illuminate\Support\Facades\Validator;

class ActivityController extends Controller
{
    // Hämta alla aktiviteter
    public function index()
    {
        return response()->json(Activity::all());
    }

    // Skapa ny aktivitet
    public function store(Request $request)
    {
        // Validering
        $validator = Validator::make($request->all(), [
        'name' => 'required|string|unique:activities,name',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $activity = Activity::create($validator->validated());

        return response()->json($activity, 201);
    }

    // Uppdatera aktivitet
    public function update(Request $request, $id)
    {
        $activity = Activity::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:activities,name,'.$id,
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 442);
        }

        $activity->update($validator->validated());

        return response()->json($activity);
    }

    // Ta bort aktivitet
    public function destroy($id)
    {
        $activity = Activity::findOrFail($id);
        $activity->delete();

        return response()->json(['message' => 'Activity deleted']);
    }
}
