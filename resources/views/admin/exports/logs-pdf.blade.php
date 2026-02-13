<!DOCTYPE html>
<html>
<head>
    <title>Student Attendance Logs</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; color: #064e3b; }
        .header p { margin: 5px 0; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f3f4f6; color: #374151; font-weight: bold; }
        .status-in { color: #059669; }
        .status-out { color: #dc2626; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 10px; color: #999; border-top: 1px solid #eee; padding-top: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>DCC Library Attendance Report</h1>
        <p>Campus: {{ $location ?? 'All Campuses' }}</p>
        <p>Date Generated: {{ now()->format('M d, Y h:i A') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Student</th>
                <th>Campus</th>
                <th>Dept</th>
                <th>Course/Section</th>
                <th>Date</th>
                <th>Time In</th>
                <th>Time Out</th>
            </tr>
        </thead>
        <tbody>
            @foreach($logs as $log)
            <tr>
                <td>{{ $log->firstname }} {{ $log->lastname }}<br><small>{{ $log->sid }}</small></td>
                <td>{{ $log->campus }}</td>
                <td>{{ $log->department }}</td>
                <td>{{ $log->course }} {{ $log->section }}</td>
                <td>{{ \Carbon\Carbon::parse($log->time_in)->format('Y-m-d') }}</td>
                <td class="status-in">{{ \Carbon\Carbon::parse($log->time_in)->format('h:i A') }}</td>
                <td class="status-out">{{ $log->time_out ? \Carbon\Carbon::parse($log->time_out)->format('h:i A') : 'Active' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        DCC Library System - Design With ❤️ By MIS Team
    </div>
</body>
</html>
