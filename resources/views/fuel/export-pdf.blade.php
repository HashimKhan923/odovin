<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Fuel Logs</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
        th { background: #f5f5f5; }
    </style>
</head>
<body>

<h2>Fuel Logs</h2>
<p>User: {{ $user->name }}</p>
<p>Date: {{ now()->format('Y-m-d') }}</p>

<table>
    <thead>
        <tr>
            <th>Vehicle</th>
            <th>Date</th>
            <th>Odometer</th>
            <th>Gallons</th>
            <th>Total Cost</th>
            <th>MPG</th>
        </tr>
    </thead>
    <tbody>
        @foreach($logs as $log)
        <tr>
            <td>{{ $log->vehicle->full_name }}</td>
            <td>{{ $log->fill_date->format('Y-m-d') }}</td>
            <td>{{ $log->odometer }}</td>
            <td>{{ $log->gallons }}</td>
            <td>${{ number_format($log->total_cost, 2) }}</td>
            <td>{{ $log->mpg ?? '—' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>
