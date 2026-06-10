<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Inout;
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

        if ($location === 'DCC Main') {
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
        $location = session('location');

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

        $location = session('location');
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
            $query->where('campus', $this->getCampus($location));
        }

        $this->applyGradeFilter($query, $location ?? '');

        return [
            'inside' => (clone $query)->whereDate('time_in', $today)->whereNull('time_out')->count(),
            'in' => (clone $query)->whereDate('time_in', $today)->count(),
            'out' => (clone $query)->whereDate('time_out', $today)->count(),
        ];
    }
}
