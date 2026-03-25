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
            'title' => 'required|string',
            'author' => 'required|string',
            'call_number' => 'required|string'
        ]);

        Book::create([
            'accession_no' => $request->accession_no,
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
            'title' => 'required|string',
            'author' => 'required|string',
            'call_number' => 'required|string',
            'status' => 'required|in:Available,Borrowed'
        ]);

        $book->update($request->only('title', 'author', 'call_number', 'status'));
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
            'borrower_id' => 'required|string',
            'accession_no' => 'required|string'
        ]);

        // Check borrower type
        $student = Student::where('sid', $request->borrower_id)
                          ->orWhere('rfid', $request->borrower_id)->first();
                          
        $employee = Employee::where('id', $request->borrower_id)
                            ->orWhere('rfid', $request->borrower_id)->first();
        
        $borrower = $student ?: $employee;
        if (!$borrower) {
            return response()->json(['success' => false, 'message' => 'Borrower not found in Students or Employees.'], 404);
        }

        $book = Book::where('accession_no', $request->accession_no)->first();
        if (!$book) {
            return response()->json(['success' => false, 'message' => 'Book not found.'], 404);
        }

        if ($book->status === 'Borrowed') {
            return response()->json(['success' => false, 'message' => 'Book is currently borrowed.'], 400);
        }

        // Action
        $book->update(['status' => 'Borrowed']);
        
        $transaction = new Transaction();
        $transaction->borrower_id = $request->borrower_id;
        $transaction->borrower_type = get_class($borrower);
        $transaction->accession_no = $request->accession_no;
        $transaction->date_borrowed = Carbon::today();
        $transaction->due_date = Carbon::today()->addDays(7);
        $transaction->status = 'Borrowed';
        $transaction->save();

        return response()->json([
            'success' => true, 
            'message' => 'Book borrowed successfully.',
            'transaction' => $transaction
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

        return view('admin.library.reports', compact('monthlyReport', 'topBooks'));
    }
}
