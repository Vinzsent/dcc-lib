<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Inout;
use App\Models\Employee;
use App\Models\EmployeeLog;
use Carbon\Carbon;

class ScannerController extends Controller
{
    /**
     * Returns grade values (lowercased) that should be SHOWN for a given location.
     * Returns null if no grade-level filtering should be applied.
     */
    private function getAllowedGrades(string $location): ?array
    {
        return match ($location) {
            'DCC BED SeniorHighSchool' => ['grade 11', 'grade 12'],
            'DCC BED Highschool'       => ['grade 7', 'grade 8', 'grade 9', 'grade 10'],
            'DCC BED Elementary'       => ['kinder 2', 'kindergarten 2', 'grade 1', 'grade 2', 'grade 3', 'grade 4', 'grade 5', 'grade 6'],
            default                    => null, // no grade-level restriction
        };
    }

    /**
     * Apply a grade-level filter to a query builder.
     * Checks both grade and year columns (or just year if grade is missing).
     */
    private function applyGradeFilter($query, string $location)
    {
        $bedGrades = [
            'grade 1', 'grade 2', 'grade 3', 'grade 4', 'grade 5', 'grade 6',
            'grade 7', 'grade 8', 'grade 9', 'grade 10', 'grade 11', 'grade 12',
            'kinder 2', 'kindergarten 2',
        ];

        $allowed = $this->getAllowedGrades($location);
        
        $hasGradeColumn = true;
        try {
            $model = $query->getModel();
            if ($model && $model->getTable() === 'inouts') {
                $hasGradeColumn = false;
            }
        } catch (\Throwable $e) {
            // Fallback
        }

        if ($location === 'DCC TED') {
            // Exclude all BED grade-level students (check both grade and year columns)
            $query->where(function ($q) use ($bedGrades, $hasGradeColumn) {
                if ($hasGradeColumn) {
                    $q->where(function ($sub) use ($bedGrades) {
                        $sub->whereNull('grade')
                            ->orWhereNotIn(\DB::raw('LOWER(grade)'), $bedGrades);
                    });
                }
                $q->where(function ($sub) use ($bedGrades) {
                    $sub->whereNull('year')
                        ->orWhereNotIn(\DB::raw('LOWER(year)'), $bedGrades);
                });
            });
        } elseif ($allowed !== null) {
            // Show ONLY students in the allowed grades (check grade or year)
            $query->where(function ($q) use ($allowed, $hasGradeColumn) {
                if ($hasGradeColumn) {
                    $q->whereIn(\DB::raw('LOWER(grade)'), $allowed)
                      ->orWhereIn(\DB::raw('LOWER(year)'), $allowed);
                } else {
                    $q->whereIn(\DB::raw('LOWER(year)'), $allowed);
                }
            });
        }

        return $query;
    }

    /**
     * Resolve the campus value used for campus-column filtering.
     * All DCC BED sub-locations belong to the 'DCC BED' campus.
     */
    private function getCampus(string $location): string
    {
        return str_starts_with($location, 'DCC BED') ? 'DCC BED' : $location;
    }

    public function index()
    {
        $today = Carbon::today();

        // Default to 'DCC BED' on first visit so the session is never null
        // (the JS syncCampus() call on page load will immediately override this
        //  with whatever the dropdown shows, but this protects any race).
        if (!session()->has('scanner_campus')) {
            session(['scanner_campus' => 'DCC BED']);
        }

        $location = session('scanner_campus') ?? session('location');

        $query = Inout::query();
        if ($location && $location !== 'Master') {
            $query->where('campus', $this->getCampus($location));
        }

        $this->applyGradeFilter($query, $location ?? '');

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

        $location = session('scanner_campus') ?? session('location');
        $query = Student::where(function ($q) use ($sid) {
            $q->where('sid', $sid)
                ->orWhere('rfid', $sid);
        });

        if ($location && $location !== 'Master') {
            $query->where('campus', $this->getCampus($location));
        }

        $this->applyGradeFilter($query, $location ?? '');

        $student = $query->first();

        if (!$student) {
            // No student matched (or filtered out by campus/grade). Check for a
            // matching employee globally — employees are not bound by campus so
            // that an employee registered at one campus can tap in at any other.
            return $this->scanEmployee($sid);
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
                'campus' => ($location && $location !== 'Master') ? $this->getCampus($location) : $student->campus,
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

    /**
     * Handle an RFID/EID tap for an employee. Employees are looked up globally
     * (no campus or grade filter) so an employee can be identified on any
     * campus's tapping screen. Logs go to the employee_logs table.
     */
    private function scanEmployee(string $sid)
    {
        $employee = Employee::where('eid', $sid)
            ->orWhere('rfid', $sid)
            ->first();

        if (!$employee) {
            return response()->json(['success' => false, 'message' => 'Student/Employee not found in master records.'], 404);
        }

        $canonicalEid = $employee->eid;

        // Record the campus where the employee physically tapped (the scanner's
        // location), not their home/registered campus. This way an employee
        // registered at BED who taps at DCC TED is logged as "DCC TED".
        // Falls back to the employee's home campus when the scanner session
        // has no location (e.g. Master/global view, or session not set).
        $location = session('scanner_campus') ?? session('location');
        $tapCampus = ($location && $location !== 'Master')
            ? $this->getCampus($location)
            : ($employee->campus ?: null);

        // Check for an active session (Time In but no Time Out)
        $activeLog = EmployeeLog::where('eid', $canonicalEid)
            ->whereNull('time_out')
            ->orderBy('time_in', 'desc')
            ->first();

        if ($activeLog) {
            // Perform Time Out
            $now = Carbon::now();
            $activeLog->update(['time_out' => $now]);

            return response()->json([
                'success' => true,
                'type' => 'employee',
                'status' => 'out',
                'message' => 'Time Out recorded successfully.',
                'employee' => $employee,
                'time' => $now->format('h:i A'),
                'counts' => $this->getCounts()
            ]);
        }

        // Perform Time In
        $now = Carbon::now();
        EmployeeLog::create([
            'eid' => $canonicalEid,
            'campus' => $tapCampus,
            'rfid' => $employee->rfid,
            'firstname' => $employee->firstname,
            'middlename' => $employee->middlename,
            'lastname' => $employee->lastname,
            'department' => $employee->department,
            'position' => $employee->position,
            'employment_type' => $employee->employment_type,
            'time_in' => $now,
        ]);

        return response()->json([
            'success' => true,
            'type' => 'employee',
            'status' => 'in',
            'message' => 'Time In recorded successfully.',
            'employee' => $employee,
            'time' => $now->format('h:i A'),
            'counts' => $this->getCounts()
        ]);
    }

    private function getCounts()
    {
        $today = Carbon::today();
        $location = session('scanner_campus') ?? session('location');

        $query = Inout::query();
        if ($location && $location !== 'Master') {
            $query->where('campus', $this->getCampus($location));
        }

        $this->applyGradeFilter($query, $location ?? '');

        return [
            'inside' => (clone $query)->whereDate('time_in', $today)->whereNull('time_out')->count(),
            'in' => (clone $query)->whereDate('time_in', $today)->count(),
            'out' => (clone $query)->whereDate('time_out', $today)->count(),
        ];
    }

    public function setCampus(Request $request)
    {
        $campus = $request->input('campus');
        if (in_array($campus, ['DCC BED', 'DCC TED'])) {
            session(['scanner_campus' => $campus]);
        }
        return response()->json([
            'success' => true,
            'counts' => $this->getCounts()
        ]);
    }
}
