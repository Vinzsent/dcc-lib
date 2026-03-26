<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Transaction;
use App\Models\Student;
use App\Models\Employee;
use Carbon\Carbon;

class LibraryController extends Controller
{
    // ----- BOOKS CRUD -----
    public function booksIndex()
    {
        $books = Book::orderBy('created_at', 'desc')->get();
        return view('admin.library.books', compact('books'));
    }

    public function booksStore(Request $request)
    {
        $request->validate([
            'accession_no' => 'required|string|unique:books,accession_no',
            'barcode' => 'nullable|string|unique:books,barcode',
            'title' => 'required|string',
            'author' => 'required|string',
            'call_number' => 'required|string'
        ]);

        Book::create([
            'accession_no' => $request->accession_no,
            'barcode' => $request->barcode,
            'title' => $request->title,
            'author' => $request->author,
            'call_number' => $request->call_number,
            'status' => 'Available'
        ]);

        return response()->json(['success' => true, 'message' => 'Book added successfully']);
    }

    public function booksUpdate(Request $request, $accession_no)
    {
        $book = Book::findOrFail($accession_no);
        $request->validate([
            'barcode' => 'nullable|string|unique:books,barcode,' . $accession_no . ',accession_no',
            'title' => 'required|string',
            'author' => 'required|string',
            'call_number' => 'required|string',
            'status' => 'required|in:Available,Borrowed'
        ]);

        $book->update($request->only('barcode', 'title', 'author', 'call_number', 'status'));
        return response()->json(['success' => true, 'message' => 'Book updated successfully']);
    }

    public function booksDestroy($accession_no)
    {
        Book::findOrFail($accession_no)->delete();
        return response()->json(['success' => true, 'message' => 'Book deleted successfully']);
    }

    // ----- BORROWING -----
    public function borrowIndex()
    {
        return view('admin.library.borrow');
    }

    public function borrowStore(Request $request)
    {
        $request->validate([
            'borrower_id'  => 'required|string',
            'accession_no' => 'required|string',
            'borrow_type'  => 'required|in:Student,Faculty,Staff',
            'book_section' => 'required|in:Reserved,Filipiniana,Circulation,Fiction',
            'borrow_period' => 'required|in:30 minutes,1 day,3 days,5 days',
        ]);

        // Resolve borrower
        $student = Student::where('sid', $request->borrower_id)
                          ->orWhere('rfid', $request->borrower_id)->first();

        $employee = Employee::where('id', $request->borrower_id)
                            ->orWhere('rfid', $request->borrower_id)->first();

        $borrower = $student ?: $employee;
        if (!$borrower) {
            return response()->json(['success' => false, 'message' => 'Borrower not found in Students or Employees.'], 404);
        }

        $book = Book::where('barcode', $request->accession_no)
                    ->orWhere('accession_no', $request->accession_no)
                    ->first();

        if (!$book) {
            return response()->json(['success' => false, 'message' => 'Book not found (by barcode or accession no).'], 404);
        }

        if ($book->status === 'Borrowed') {
            return response()->json(['success' => false, 'message' => 'Book is currently borrowed.'], 400);
        }

        // Compute due_date from borrow_period
        $due_date = match ($request->borrow_period) {
            '30 minutes' => Carbon::now()->addMinutes(30),
            '1 day'      => Carbon::today()->addDay(),
            '3 days'     => Carbon::today()->addDays(3),
            '5 days'     => Carbon::today()->addDays(5),
            default      => Carbon::today()->addWeek(),
        };

        $book->update(['status' => 'Borrowed']);

        $transaction = new Transaction();
        $transaction->borrower_id   = $request->borrower_id;
        $transaction->borrower_type = get_class($borrower);
        $transaction->borrow_type   = $request->borrow_type;
        $transaction->book_section  = $request->book_section;
        $transaction->accession_no  = $book->accession_no;
        $transaction->date_borrowed = Carbon::today();
        $transaction->due_date      = $due_date;
        $transaction->status        = 'Borrowed';
        $transaction->save();

        $borrowerName = trim(implode(' ', array_filter([
            $borrower->firstname,
            $borrower->middlename ? $borrower->middlename[0] . '.' : null,
            $borrower->lastname,
        ])));

        return response()->json([
            'success'       => true,
            'message'       => 'Book borrowed successfully.',
            'transaction'   => $transaction,
            'borrower_name' => $borrowerName,
            'borrower_year'   => $borrower->year   ?? null,
            'borrower_course' => $borrower->course ?? null,
            'book'          => [
                'title'       => $book->title,
                'author'      => $book->author,
                'call_number' => $book->call_number,
                'accession_no'=> $book->accession_no,
                'barcode'     => $book->barcode
            ],
        ]);
    }

    // ----- RETURNING -----
    public function returnIndex()
    {
        return view('admin.library.return');
    }

    public function returnUpdate(Request $request)
    {
        $request->validate([
            'accession_no' => 'required|string'
        ]);

        $book = Book::where('accession_no', $request->accession_no)->first();
        if (!$book) {
            return response()->json(['success' => false, 'message' => 'Book not found.'], 404);
        }

        $transaction = Transaction::where('accession_no', $request->accession_no)
                        ->where('status', 'Borrowed')
                        ->first();

        if (!$transaction) {
            return response()->json(['success' => false, 'message' => 'No active borrow transaction found for this book.'], 404);
        }

        $today = Carbon::today();
        $dueDate = Carbon::parse($transaction->due_date);
        
        $daysOverdue = 0;
        $fine = 0;

        if ($today->gt($dueDate)) {
            $daysOverdue = $today->diffInDays($dueDate);
            $fine = $daysOverdue * 5; // User formula: days * 5
        }

        $transaction->update([
            'date_returned' => $today,
            'fine' => $fine,
            'status' => 'Returned'
        ]);

        $book->update(['status' => 'Available']);

        return response()->json([
            'success' => true, 
            'message' => 'Book returned successfully.',
            'fine' => $fine,
            'days_overdue' => $daysOverdue
        ]);
    }

    // ----- HISTORY -----
    public function historyIndex()
    {
        $transactions = Transaction::with(['book', 'borrower'])->orderBy('created_at', 'desc')->get();
        return view('admin.library.history', compact('transactions'));
    }

    // ----- REPORTS -----
    public function reportsIndex()
    {
        // Monthly report
        $monthlyReport = Transaction::selectRaw('MONTHNAME(date_borrowed) as month,
            COUNT(*) as total_borrowed,
            SUM(CASE WHEN date_returned IS NOT NULL THEN 1 ELSE 0 END) as total_returned,
            SUM(CASE WHEN status != "Returned" AND due_date < CURDATE() THEN 1 ELSE 0 END) as total_overdue')
            ->groupBy('month')
            ->get();

        // Top borrowed books
        $topBooks = Transaction::selectRaw('accession_no, COUNT(*) as times_borrowed')
            ->with('book')
            ->groupBy('accession_no')
            ->orderByDesc('times_borrowed')
            ->limit(10)
            ->get();

        // Top student borrowers
        $topStudents = Transaction::selectRaw('borrower_id, COUNT(*) as total_borrowed')
            ->where('borrower_type', 'App\Models\Student')
            ->groupBy('borrower_id')
            ->orderByDesc('total_borrowed')
            ->limit(10)
            ->get()
            ->map(function ($t) {
                $student = Student::where('sid', $t->borrower_id)
                    ->orWhere('rfid', $t->borrower_id)
                    ->first();
                $t->borrower = $student;
                return $t;
            })
            ->filter(fn($t) => $t->borrower !== null)
            ->values();

        // Top employee borrowers
        $topEmployees = Transaction::selectRaw('borrower_id, COUNT(*) as total_borrowed')
            ->whereIn('borrower_type', ['App\Models\Employee', 'App\\Models\\Employee'])
            ->groupBy('borrower_id')
            ->orderByDesc('total_borrowed')
            ->limit(10)
            ->get()
            ->map(function ($t) {
                $employee = Employee::where('id', $t->borrower_id)
                    ->orWhere('rfid', $t->borrower_id)
                    ->first();
                $t->borrower = $employee;
                return $t;
            })
            ->filter(fn($t) => $t->borrower !== null)
            ->values();

        return view('admin.library.reports', compact(
            'monthlyReport', 'topBooks', 'topStudents', 'topEmployees'
        ));
    }

    public function reportsExport()
    {
        $filename = 'library_report_' . now()->format('Y-m-d') . '.xls';

        // ── Fetch all data ──────────────────────────────────────────────
        $monthly = Transaction::selectRaw('MONTHNAME(date_borrowed) as month,
            COUNT(*) as total_borrowed,
            SUM(CASE WHEN date_returned IS NOT NULL THEN 1 ELSE 0 END) as total_returned,
            SUM(CASE WHEN status != "Returned" AND due_date < CURDATE() THEN 1 ELSE 0 END) as total_overdue')
            ->groupBy('month')->get();

        $books = Transaction::selectRaw('accession_no, COUNT(*) as times_borrowed')
            ->with('book')->groupBy('accession_no')->orderByDesc('times_borrowed')->limit(10)->get();

        $studentRows = [];
        $studentTxns = Transaction::selectRaw('borrower_id, COUNT(*) as total_borrowed')
            ->where('borrower_type', 'App\Models\Student')
            ->groupBy('borrower_id')->orderByDesc('total_borrowed')->limit(10)->get();
        $rank = 1;
        foreach ($studentTxns as $t) {
            $s = Student::where('sid', $t->borrower_id)->orWhere('rfid', $t->borrower_id)->first();
            if (!$s) continue;
            $name = trim("{$s->firstname} " . ($s->middlename ? $s->middlename[0] . '. ' : '') . $s->lastname);
            $studentRows[] = [$rank++, $name, $s->course ?? '', $s->year ?? '', $s->sid ?? $t->borrower_id, $t->total_borrowed];
        }

        $employeeRows = [];
        $employeeTxns = Transaction::selectRaw('borrower_id, COUNT(*) as total_borrowed')
            ->where('borrower_type', 'like', '%Employee%')
            ->groupBy('borrower_id')->orderByDesc('total_borrowed')->limit(10)->get();
        $rank = 1;
        foreach ($employeeTxns as $t) {
            $e = Employee::where('id', $t->borrower_id)->orWhere('rfid', $t->borrower_id)->first();
            if (!$e) continue;
            $name = trim("{$e->firstname} " . ($e->middlename ? $e->middlename[0] . '. ' : '') . $e->lastname);
            $employeeRows[] = [$rank++, $name, $e->position ?? '', $e->department ?? '', $t->total_borrowed];
        }

        // ── Helper: escape XML ──────────────────────────────────────────
        $x = fn($v) => htmlspecialchars((string) $v, ENT_QUOTES | ENT_XML1, 'UTF-8');

        // ── Helper: build a worksheet ───────────────────────────────────
        $sheet = function (string $name, array $headers, array $rows) use ($x): string {
            $colCount = count($headers);
            $colWidth = 120; // default width in points

            $out  = "<Worksheet ss:Name=\"{$x($name)}\">";
            $out .= '<Table>';

            // Header row
            $out .= '<Row>';
            foreach ($headers as $h) {
                $out .= "<Cell ss:StyleID=\"header\"><Data ss:Type=\"String\">{$x($h)}</Data></Cell>";
            }
            $out .= '</Row>';

            // Data rows
            foreach ($rows as $row) {
                $out .= '<Row>';
                foreach ($row as $cell) {
                    $type = is_numeric($cell) ? 'Number' : 'String';
                    $out .= "<Cell ss:StyleID=\"data\"><Data ss:Type=\"{$type}\">{$x($cell)}</Data></Cell>";
                }
                $out .= '</Row>';
            }

            $out .= '</Table>';
            $out .= '<WorksheetOptions xmlns="urn:schemas-microsoft-com:office:excel">';
            $out .= '<FreezePanes/><FrozenNoSplit/><SplitHorizontal>1</SplitHorizontal><TopRowBottomPane>1</TopRowBottomPane>';
            $out .= '</WorksheetOptions>';
            $out .= '</Worksheet>';
            return $out;
        };

        // ── Build monthly rows ──────────────────────────────────────────
        $monthlyRows = $monthly->map(fn($r) => [
            $r->month, $r->total_borrowed, $r->total_returned, $r->total_overdue
        ])->toArray();

        // ── Build book rows ─────────────────────────────────────────────
        $bookRows = $books->map(fn($b, $i) => [
            $i + 1,
            $b->book->title   ?? 'Unknown',
            $b->book->author  ?? 'Unknown',
            $b->accession_no,
            $b->times_borrowed,
        ])->toArray();

        // ── Assemble workbook XML ───────────────────────────────────────
        $xml  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<?mso-application progid="Excel.Sheet"?>' . "\n";
        $xml .= '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"'
              . ' xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"'
              . ' xmlns:x="urn:schemas-microsoft-com:office:excel">' . "\n";

        // Styles
        $xml .= '<Styles>';
        $xml .= '<Style ss:ID="header">'
              . '<Font ss:Bold="1" ss:Color="#FFFFFF" ss:Size="11"/>'
              . '<Interior ss:Color="#1a7a4a" ss:Pattern="Solid"/>'
              . '<Alignment ss:Horizontal="Center" ss:Vertical="Center"/>'
              . '<Borders><Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/></Borders>'
              . '</Style>';
        $xml .= '<Style ss:ID="data">'
              . '<Alignment ss:Vertical="Center"/>'
              . '</Style>';
        $xml .= '</Styles>';

        // Sheets
        $xml .= $sheet(
            'Monthly Overview',
            ['Month', 'Borrowed', 'Returned', 'Overdue Active'],
            $monthlyRows
        );
        $xml .= $sheet(
            'Top Borrowed Books',
            ['Rank', 'Title', 'Author', 'Accession No', 'Times Borrowed'],
            $bookRows
        );
        $xml .= $sheet(
            'Top Student Borrowers',
            ['Rank', 'Full Name', 'Course', 'Year', 'SID', 'Times Borrowed'],
            $studentRows
        );
        $xml .= $sheet(
            'Top Employee Borrowers',
            ['Rank', 'Full Name', 'Position', 'Department', 'Times Borrowed'],
            $employeeRows
        );

        $xml .= '</Workbook>';

        return response($xml, 200, [
            'Content-Type'        => 'application/vnd.ms-excel',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control'       => 'no-cache, must-revalidate',
        ]);
    }
}

