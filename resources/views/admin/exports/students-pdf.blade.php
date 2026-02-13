<!DOCTYPE html>
<html>
<head>
    <title>Student Information Master List</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 11px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; color: #064e3b; }
        .header p { margin: 5px 0; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background-color: #f3f4f6; color: #374151; font-weight: bold; text-transform: uppercase; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 9px; color: #999; border-top: 1px solid #eee; padding-top: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>DCC Library Student Master List</h1>
        <p>Campus: {{ $location ?? 'All Campuses' }}</p>
        <p>Date Generated: {{ now()->format('M d, Y h:i A') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Student ID</th>
                <th>RFID</th>
                <th>Full Name</th>
                <th>Campus</th>
                <th>Department</th>
                <th>Course & Section</th>
                <th>Year</th>
            </tr>
        </thead>
        <tbody>
            @foreach($students as $student)
            <tr>
                <td>{{ $student->sid }}</td>
                <td>{{ $student->rfid ?? 'N/A' }}</td>
                <td>{{ $student->firstname }} {{ $student->lastname }}</td>
                <td>{{ $student->campus }}</td>
                <td>{{ $student->department }}</td>
                <td>{{ $student->course }} - {{ $student->section }}</td>
                <td>{{ $student->year }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        DCC Library System - Design With ❤️ By MIS Team
    </div>
</body>
</html>
