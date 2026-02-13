<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Inout;
use Carbon\Carbon;

class ScannerController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $location = session('location');

        $query = Inout::query();
        if ($location && $location !== 'Master') {
            $query->where('campus', $location);
        }

        $studentsInside = (clone $query)->whereDate('time_in', $today)->whereNull('time_out')->count();
        $totalTimeIn = (clone $query)->whereDate('time_in', $today)->count();
        $totalTimeOut = (clone $query)->whereDate('time_out', $today)->count();

        return view('scanner', compact('studentsInside', 'totalTimeIn', 'totalTimeOut'));
    }

    public function scan(Request $request)
    {
        $sid = trim($request->input('sid'));

        if (!$sid) {
            return response()->json(['success' => false, 'message' => 'No Student ID or RFID provided.'], 400);
        }

        $location = session('location');
        $query = Student::where(function ($q) use ($sid) {
            $q->where('sid', $sid)
                ->orWhere('rfid', $sid);
        });

        if ($location && $location !== 'Master') {
            $query->where('campus', $location);
        }

        $student = $query->first();

        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Student not found in master records.'], 404);
        }

        // Use canonical SID from student record for inout logs
        $canonicalSid = $student->sid;

        // 2. Check for an active session (Time In but no Time Out)
        $activeLog = Inout::where('sid', $canonicalSid)
            ->whereNull('time_out')
            ->orderBy('time_in', 'desc')
            ->first();

        if ($activeLog) {
            // 3. Perform Time Out
            $now = Carbon::now();
            $activeLog->update([
                'time_out' => $now,
            ]);

            return response()->json([
                'success' => true,
                'status' => 'out',
                'message' => 'Time Out recorded successfully.',
                'student' => $student,
                'time' => $now->format('h:i A'),
                'counts' => $this->getCounts()
            ]);
        } else {
            // 4. Perform Time In
            $now = Carbon::now();
            $newLog = Inout::create([
                'sid' => $canonicalSid,
                'campus' => $student->campus,
                'rfid' => $student->rfid,
                'firstname' => $student->firstname,
                'lastname' => $student->lastname,
                'department' => $student->department,
                'course' => $student->course,
                'section' => $student->section,
                'year' => $student->year,
                'profile' => $student->profile,
                'time_in' => $now,
            ]);

            return response()->json([
                'success' => true,
                'status' => 'in',
                'message' => 'Time In recorded successfully.',
                'student' => $student,
                'time' => $now->format('h:i A'),
                'counts' => $this->getCounts()
            ]);
        }
    }

    private function getCounts()
    {
        $today = Carbon::today();
        $location = session('location');

        $query = Inout::query();
        if ($location && $location !== 'Master') {
            $query->where('campus', $location);
        }

        return [
            'inside' => (clone $query)->whereDate('time_in', $today)->whereNull('time_out')->count(),
            'in' => (clone $query)->whereDate('time_in', $today)->count(),
            'out' => (clone $query)->whereDate('time_out', $today)->count(),
        ];
    }
}
