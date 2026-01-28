<?php

namespace App\Http\Controllers;

use App\Models\Workout;
use Dompdf\Dompdf;
use Illuminate\Http\Request;
use Dompdf\Options;
use Carbon\Carbon;
use Laravel\Lumen\Routing\Controller;

class WorkoutPdfController extends Controller {
    public function export(Request $request) {
        $query = Workout::with('activity');

        // Filtrera på aktivitet om activity_id skickas med
        if ($request->has('activity_id')) {
            $query->where('activity_id', $request->activity_id);
        }

        // Filtrera på från-datum
        if ($request->has('from')) {
            try {
                $from = Carbon::createFromFormat('d-m-Y', $request->from)->format('Y-m-d');
                $query->where('date', '>=', $from);
            } catch (\Exception $e) {
                return response()->json(['error' => 'Felaktig from-datumformat. Använd d-m-Y'], 422);
            }
        }

        // Filtrera på till-datum
        if ($request->has('to')) {
            try {
                $to = Carbon::createFromFormat('d-m-Y', $request->to)->format('Y-m-d');
                $query->where('date', '<=', $to);
            }  catch (\Exception $e) {
                return response()->json(['error' => 'Felaktigt to-datumformat. Använd d-m-Y'], 422);
            }
        }

        $workouts = $query->orderBy('date')->get();

        // Skapa html
        $html = view('pdf.workouts', compact('workouts'))->render();

        // Initiera Dompdf
        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Returnera PDF
        return response(
            $dompdf->output(),
            200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="workouts.pdf"',
            ]
        );
    }
}
