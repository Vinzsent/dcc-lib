<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Student;
use App\Models\Employee;
use App\Models\EmployeeLog;
use Barryvdh\DomPDF\Facade\Pdf;


class AdminController extends Controller
{
    public function index()
    {
        $location = session('location');

        $studentQuery = Student::query();
        $inoutQuery = \App\Models\Inout::query();

        if ($location && $location !== 'Master') {
            $studentQuery->where('campus', $location);
            $inoutQuery->where('campus', $location);
        }

        $totalStudents = $studentQuery->count();
        $activeNow = (clone $inoutQuery)->whereNull('time_out')->count();
        $totalLogs = (clone $inoutQuery)->count();
        $recentLogs = (clone $inoutQuery)->latest('updated_at')->take(5)->get();

        return view('admin.dashboard', compact('totalStudents', 'activeNow', 'totalLogs', 'recentLogs'));
    }

    public function studentData(Request $request)
    {
        $location = session('location');
        $query = Student::query();

        if ($location && $location !== 'Master') {
            $query->where('campus', $location);
        }

        // Search Filter (Global)
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('firstname', 'like', "%{$search}%")
                    ->orWhere('middlename', 'like', "%{$search}%")
                    ->orWhere('lastname', 'like', "%{$search}%")
                    ->orWhere('department', 'like', "%{$search}%")
                    ->orWhere('sid', 'like', "%{$search}%")
                    ->orWhere('rfid', 'like', "%{$search}%")
                    ->orWhere('course', 'like', "%{$search}%")
                    ->orWhere('section', 'like', "%{$search}%")
                    ->orWhere('year', 'like', "%{$search}%")
                    ->orWhere('grade', 'like', "%{$search}%");
            });
        }

        // Column-specific Filters
        if ($request->filled('sid')) {
            $query->where('sid', 'like', "%{$request->sid}%");
        }
        if ($request->filled('rfid')) {
            $query->where('rfid', 'like', "%{$request->rfid}%");
        }
        if ($request->filled('firstname')) {
            $query->where('firstname', 'like', "%{$request->firstname}%");
        }
        if ($request->filled('middlename')) {
            $query->where('middlename', 'like', "%{$request->middlename}%");
        }
        if ($request->filled('lastname')) {
            $query->where('lastname', 'like', "%{$request->lastname}%");
        }
        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }
        if ($request->filled('course')) {
            $query->where('course', 'like', "%{$request->course}%");
        }
        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }
        if ($request->filled('grade')) {
            $query->where('grade', $request->grade);
        }
        if ($request->filled('section')) {
            $query->where('section', 'like', "%{$request->section}%");
        }

        // Sorting
        $sort = $request->input('sort', 'created_at');
        $direction = $request->input('direction', 'desc');

        // Validate sort column to prevent SQL injection
        $allowedSorts = ['sid', 'rfid', 'firstname', 'middlename', 'lastname', 'department', 'course', 'year', 'grade', 'section', 'created_at'];
        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->latest();
        }

        $students = $query->paginate(10);

        // Get unique values for filters
        $departments = Student::distinct()->whereNotNull('department')->pluck('department')->toArray();
        $years = Student::distinct()->whereNotNull('year')->pluck('year')->sort()->toArray();
        $grades = Student::distinct()->whereNotNull('grade')->pluck('grade')->toArray();

        return view('admin.student-data', compact('students', 'departments', 'years', 'grades'));
    }

    // BED Campus account
    public function storeStudent(Request $request)
    {
        if (session('location') == 'DCC BED') {
            $data = $request->validate([
                'sid' => 'required|string|unique:students|max:255',
                'rfid' => 'nullable|string|unique:students|max:255',
                'firstname' => 'required|string|max:255',
                'lastname' => 'required|string|max:255',
                'grade' => 'required|string|max:255',
                'section' => 'required|string|max:255',
            ]);
            $data['department'] = null;
            $data['course'] = 'N/A';
            $data['year'] = 'N/A';
        } else {
            $data = $request->validate([
                'sid' => 'required|string|unique:students|max:255',
                'rfid' => 'nullable|string|unique:students|max:255',
                'firstname' => 'required|string|max:255',
                'lastname' => 'required|string|max:255',
                'department' => 'required|string|max:255',
                'course' => 'required|string|max:255',
                'year' => 'required|string|max:255',
            ]);
            $data['grade'] = null;
            $data['section'] = null;
        }

        // The line `$data = $request->all();` from the instruction was removed
        // as it would overwrite the validated and defaulted data.
        // The validated data is already assigned to $data in the conditional blocks.

        if (session('location') && session('location') !== 'Master') {
            $data['campus'] = session('location');
        }

        Student::create($data);

        return redirect()->back()->with('success', 'Student added successfully.');
    }

    public function updateStudent(Request $request, Student $student)
    {
        if (session('location') == 'DCC BED') {
            $data = $request->validate([
                'sid' => 'required|string|max:255|unique:students,sid,' . $student->id,
                'rfid' => 'nullable|string|max:255|unique:students,rfid,' . $student->id,
                'firstname' => 'required|string|max:255',
                'lastname' => 'required|string|max:255',
                'grade' => 'required|string|max:255',
                'section' => 'required|string|max:255',
            ]);
            $data['department'] = null;
            $data['course'] = 'N/A';
            $data['year'] = 'N/A';
        } else {
            $data = $request->validate([
                'sid' => 'required|string|max:255|unique:students,sid,' . $student->id,
                'rfid' => 'nullable|string|max:255|unique:students,rfid,' . $student->id,
                'firstname' => 'required|string|max:255',
                'lastname' => 'required|string|max:255',
                'department' => 'required|string|max:255',
                'course' => 'required|string|max:255',
                'year' => 'required|string|max:255',
            ]);
            $data['grade'] = null;
            $data['section'] = null;
        }

        $student->update($data);

        return redirect()->back()->with('success', 'Student updated successfully.');
    }

    public function destroyStudent(Student $student)
    {
        $student->delete();
        return redirect()->back()->with('success', 'Student deleted successfully.');
    }

    public function studentLogs(Request $request)
    {
        $location = session('location');
        $query = \App\Models\Inout::query();

        if ($location && $location !== 'Master') {
            $query->where('campus', $location);
        }

        // Search Filter (Global)
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('firstname', 'like', "%{$search}%")
                    ->orWhere('middlename', 'like', "%{$search}%")
                    ->orWhere('lastname', 'like', "%{$search}%")
                    ->orWhere('department', 'like', "%{$search}%")
                    ->orWhere('sid', 'like', "%{$search}%")
                    ->orWhere('rfid', 'like', "%{$search}%")
                    ->orWhere('course', 'like', "%{$search}%")
                    ->orWhere('section', 'like', "%{$search}%")
                    ->orWhere('year', 'like', "%{$search}%");
            });
        }

        // Column-specific Filters
        if ($request->filled('campus')) {
            $query->where('campus', $request->campus);
        }
        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }
        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }
        if ($request->filled('course')) {
            $query->where('course', 'like', "%{$request->course}%");
        }
        if ($request->filled('section')) {
            $query->where('section', 'like', "%{$request->section}%");
        }

        // Sorting
        $sort = $request->input('sort', 'updated_at');
        $direction = $request->input('direction', 'desc');

        // Validate sort column to prevent SQL injection
        $allowedSorts = ['firstname', 'lastname', 'campus', 'department', 'course', 'section', 'year', 'time_in', 'time_out', 'updated_at'];
        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->latest('updated_at');
        }

        $logs = $query->paginate(10);

        // Get unique values for filters
        $campuses = \App\Models\Inout::distinct()->pluck('campus')->filter()->toArray();
        $departments = \App\Models\Inout::distinct()->pluck('department')->filter()->toArray();
        $years = \App\Models\Inout::distinct()->pluck('year')->filter()->sort()->toArray();

        return view('admin.student-logs', compact('logs', 'campuses', 'departments', 'years'));
    }

    public function reports(Request $request)
    {
        $location = session('location');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Initialize course summary
        $courseSummary = null;

        // If date range is provided, fetch course summary
        if ($startDate && $endDate) {
            $query = \App\Models\Inout::query();

            // Apply location filter
            if ($location && $location !== 'Master') {
                $query->where('campus', $location);
            }

            // Apply date range filter
            $query->whereBetween('time_in', [
                $startDate . ' 00:00:00',
                $endDate . ' 23:59:59'
            ]);

            // Group by course and count
            $courseSummary = $query->selectRaw('course, COUNT(*) as total_logs, COUNT(DISTINCT sid) as unique_students')
                ->whereNotNull('course')
                ->where('course', '!=', '')
                ->groupBy('course')
                ->orderBy('total_logs', 'desc')
                ->get();
        }

        return view('admin.reports', compact('courseSummary', 'startDate', 'endDate'));
    }

    public function users()
    {
        $users = User::paginate(10);
        return view('admin.users', compact('users'));
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|string|in:Admin,User',
        ]);

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->back()->with('success', 'User created successfully.');
    }

    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
            'role' => 'required|string|in:Admin,User',
        ]);

        $user->update([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'role' => $request->role,
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->back()->with('success', 'User updated successfully.');
    }

    public function destroyUser(User $user)
    {
        // Prevent self-deletion if logged in admin is the target
        if (\Illuminate\Support\Facades\Auth::id() === $user->id) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();
        return redirect()->back()->with('success', 'User deleted successfully.');
    }

    public function exportLogs(Request $request)
    {
        $location = session('location');
        $format = $request->input('format', 'csv');
        $type = $request->input('type', 'attendance');

        if ($type === 'students') {
            $query = Student::query();

            if ($location && $location !== 'Master') {
                $query->where('campus', $location);
            }

            if ($search = $request->input('search')) {
                $query->where(function ($q) use ($search) {
                    $q->where('firstname', 'like', "%{$search}%")
                        ->orWhere('lastname', 'like', "%{$search}%")
                        ->orWhere('sid', 'like', "%{$search}%")
                        ->orWhere('rfid', 'like', "%{$search}%")
                        ->orWhere('course', 'like', "%{$search}%")
                        ->orWhere('department', 'like', "%{$search}%");
                });
            }

            $students = $query->latest()->get();

            if ($format === 'pdf') {
                $pdf = Pdf::loadView('admin.exports.students-pdf', compact('students', 'location'));
                return $pdf->download('student_information_' . date('Y-m-d_H-i-s') . '.pdf');
            }

            // CSV for Students
            $fileName = 'student_information_' . date('Y-m-d_H-i-s') . '.csv';
            $headers = [
                "Content-type"        => "text/csv",
                "Content-Disposition" => "attachment; filename=$fileName",
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            ];

            $columns = ['Student ID', 'RFID', 'First Name', 'Last Name', 'Campus', 'Department', 'Course', 'Section', 'Year'];

            $callback = function () use ($students, $columns) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $columns);
                foreach ($students as $student) {
                    fputcsv($file, [
                        $student->sid,
                        $student->rfid ?? 'N/A',
                        $student->firstname,
                        $student->lastname,
                        $student->campus,
                        $student->department,
                        $student->course,
                        $student->section,
                        $student->year
                    ]);
                }
                fclose($file);
            };
            return response()->stream($callback, 200, $headers);
        }

        // Default: attendance type
        $query = \App\Models\Inout::query();

        if ($location && $location !== 'Master') {
            $query->where('campus', $location);
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('firstname', 'like', "%{$search}%")
                    ->orWhere('lastname', 'like', "%{$search}%")
                    ->orWhere('sid', 'like', "%{$search}%")
                    ->orWhere('rfid', 'like', "%{$search}%")
                    ->orWhere('course', 'like', "%{$search}%")
                    ->orWhere('year', 'like', "%{$search}%");
            });
        }

        if ($request->filled('start_date')) {
            $query->whereDate('time_in', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('time_in', '<=', $request->end_date);
        }

        $logs = $query->latest('time_in')->get();

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('admin.exports.logs-pdf', compact('logs', 'location'));
            return $pdf->download('student_logs_' . date('Y-m-d_H-i-s') . '.pdf');
        }

        // Default to CSV
        $fileName = 'student_logs_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('Campus', 'Student ID', 'RFID', 'First Name', 'Last Name', 'Department', 'Course', 'Section', 'Year', 'Date', 'Time In', 'Time Out');

        $callback = function () use ($logs, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($logs as $log) {
                $row['Date']        = \Carbon\Carbon::parse($log->time_in)->format('Y-m-d');
                $row['Time In']     = \Carbon\Carbon::parse($log->time_in)->format('h:i A');
                $row['Time Out']    = $log->time_out ? \Carbon\Carbon::parse($log->time_out)->format('h:i A') : 'Active';

                fputcsv($file, array($log->campus, $log->sid, $log->rfid, $log->firstname, $log->lastname, $log->department, $log->course, $log->section, $log->year, $row['Date'], $row['Time In'], $row['Time Out']));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // ==================== EMPLOYEE DATA METHODS ====================

    public function employeeData(Request $request)
    {
        $location = session('location');
        $query = Employee::query();

        if ($location && $location !== 'Master') {
            $query->where('campus', $location);
        }

        // Search Filter (Global)
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('firstname', 'like', "%{$search}%")
                    ->orWhere('middlename', 'like', "%{$search}%")
                    ->orWhere('lastname', 'like', "%{$search}%")
                    ->orWhere('department', 'like', "%{$search}%")
                    ->orWhere('eid', 'like', "%{$search}%")
                    ->orWhere('rfid', 'like', "%{$search}%")
                    ->orWhere('position', 'like', "%{$search}%")
                    ->orWhere('employment_type', 'like', "%{$search}%");
            });
        }

        // Column-specific Filters
        if ($request->filled('eid')) {
            $query->where('eid', 'like', "%{$request->eid}%");
        }
        if ($request->filled('rfid')) {
            $query->where('rfid', 'like', "%{$request->rfid}%");
        }
        if ($request->filled('firstname')) {
            $query->where('firstname', 'like', "%{$request->firstname}%");
        }
        if ($request->filled('middlename')) {
            $query->where('middlename', 'like', "%{$request->middlename}%");
        }
        if ($request->filled('lastname')) {
            $query->where('lastname', 'like', "%{$request->lastname}%");
        }
        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }
        if ($request->filled('position')) {
            $query->where('position', 'like', "%{$request->position}%");
        }
        if ($request->filled('employment_type')) {
            $query->where('employment_type', $request->employment_type);
        }

        // Sorting
        $sort = $request->input('sort', 'created_at');
        $direction = $request->input('direction', 'desc');

        // Validate sort column to prevent SQL injection
        $allowedSorts = ['eid', 'rfid', 'firstname', 'middlename', 'lastname', 'department', 'position', 'employment_type', 'created_at'];
        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->latest();
        }

        $employees = $query->paginate(10);

        // Get unique values for filters
        $departments = Employee::distinct()->whereNotNull('department')->pluck('department')->toArray();
        $employmentTypes = Employee::distinct()->whereNotNull('employment_type')->pluck('employment_type')->toArray();

        return view('admin.employee-data', compact('employees', 'departments', 'employmentTypes'));
    }

    public function storeEmployee(Request $request)
    {
        $data = $request->validate([
            'eid' => 'required|string|unique:employees|max:255',
            'rfid' => 'nullable|string|unique:employees|max:255',
            'firstname' => 'required|string|max:255',
            'middlename' => 'nullable|string|max:255',
            'lastname' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'employment_type' => 'nullable|string|max:255',
        ]);

        if (session('location') && session('location') !== 'Master') {
            $data['campus'] = session('location');
        }

        Employee::create($data);

        return redirect()->back()->with('success', 'Employee added successfully.');
    }

    public function updateEmployee(Request $request, Employee $employee)
    {
        $data = $request->validate([
            'eid' => 'required|string|max:255|unique:employees,eid,' . $employee->id,
            'rfid' => 'nullable|string|max:255|unique:employees,rfid,' . $employee->id,
            'firstname' => 'required|string|max:255',
            'middlename' => 'nullable|string|max:255',
            'lastname' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'employment_type' => 'nullable|string|max:255',
        ]);

        $employee->update($data);

        return redirect()->back()->with('success', 'Employee updated successfully.');
    }

    public function destroyEmployee(Employee $employee)
    {
        $employee->delete();
        return redirect()->back()->with('success', 'Employee deleted successfully.');
    }

    // ==================== EMPLOYEE LOGS METHODS ====================

    public function employeeLogs(Request $request)
    {
        $location = session('location');
        $query = EmployeeLog::query();

        if ($location && $location !== 'Master') {
            $query->where('campus', $location);
        }

        // Search Filter (Global)
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('firstname', 'like', "%{$search}%")
                    ->orWhere('middlename', 'like', "%{$search}%")
                    ->orWhere('lastname', 'like', "%{$search}%")
                    ->orWhere('department', 'like', "%{$search}%")
                    ->orWhere('eid', 'like', "%{$search}%")
                    ->orWhere('rfid', 'like', "%{$search}%")
                    ->orWhere('position', 'like', "%{$search}%");
            });
        }

        // Column-specific Filters
        if ($request->filled('campus')) {
            $query->where('campus', $request->campus);
        }
        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }
        if ($request->filled('position')) {
            $query->where('position', 'like', "%{$request->position}%");
        }
        if ($request->filled('employment_type')) {
            $query->where('employment_type', $request->employment_type);
        }

        // Sorting
        $sort = $request->input('sort', 'updated_at');
        $direction = $request->input('direction', 'desc');

        // Validate sort column to prevent SQL injection
        $allowedSorts = ['firstname', 'lastname', 'campus', 'department', 'position', 'employment_type', 'time_in', 'time_out', 'updated_at'];
        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->latest('updated_at');
        }

        $logs = $query->paginate(10);

        // Get unique values for filters
        $campuses = EmployeeLog::distinct()->pluck('campus')->filter()->toArray();
        $departments = EmployeeLog::distinct()->pluck('department')->filter()->toArray();
        $employmentTypes = EmployeeLog::distinct()->pluck('employment_type')->filter()->toArray();

        return view('admin.employee-logs', compact('logs', 'campuses', 'departments', 'employmentTypes'));
    }
}
