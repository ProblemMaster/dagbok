<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>pdf</title>
    <style>
        body { font-family: DejaVu Sans; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 6px; }
        th { background: #eee; }
    </style>
</head>
<body>
    <h2>Träningspass</h2>

    <table>
        <thead>
            <tr>
                <th>Datum</th>
                <th>Aktivitet</th>
                <th>Beskrivning</th>
                <th>Distans (km)</th>
                <th>Tid (min)</th>
                <th>Jobbighet</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($workouts as $workout)
                <tr>
                    <td>{{ $workout->date }}</td>
                    <td>{{ $workout->activity->name }}</td>
                    <td>{{ $workout->description }}</td>
                    <td>{{ $workout->distance_value }}</td>
                    <td>{{ $workout->duration_value }}</td>
                    <td>{{ $workout->effort_level }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
