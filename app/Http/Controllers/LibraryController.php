<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\BookElem;
use App\Models\BookHighschool;
use App\Models\Transaction;
use App\Models\Student;
use App\Models\Employee;
use App\Models\Shelf;
use Carbon\Carbon;
use App\Models\Research;

class LibraryController extends Controller
{
    /**
     * Map user role to the book model class they should use.
     */
    private function getBookModel(): string
    {
        $role = auth()->user()?->role ?? '';

        return match ($role) {
            'Admin BEDELEM' => BookElem::class,
            'Admin BEDHS'   => BookHighschool::class,
            default         => Book::class, // Master, Admin, Admin TED, Admin BED, Admin BEDSHS
        };
    }

    /**
     * Map user role to the book table name.
     */
    private function getBookTable(): string
    {
        $role = auth()->user()?->role ?? '';

        return match ($role) {
            'Admin BEDELEM' => 'books_elem',
            'Admin BEDHS'   => 'books_highschool',
            default         => 'books_main',
        };
    }

    /**
     * Get the allowed campuses based on the user's role.
     * Returns null if no filtering (Master / Admin).
     */
    private function getRoleCampusFilter(): ?array
    {
        $role = auth()->user()?->role ?? '';

        return match ($role) {
            'Admin TED'     => null, // Global admin for books_main (TED / Main campus books)
            'Admin BEDELEM' => null, // separate table, no campus filter needed
            'Admin BEDHS'   => null, // separate table, no campus filter needed
            'Admin BED'     => ['DCC BED Highschool', 'DCC BED SeniorHighSchool', 'DCC BED Elementary'],
            'Admin BEDSHS'  => ['DCC BED SeniorHighSchool'],
            'Master'        => null, // sees all
            default         => null,
        };
    }

    // ----- BOOKS CRUD -----
    public function booksIndex(Request $request)
    {
        $modelClass = $this->getBookModel();
        $campusFilter = $this->getRoleCampusFilter();
        $table = $this->getBookTable();

        // Elementary books use the dedicated elem view
        if ($modelClass === BookElem::class) {
            return $this->booksIndexElem($request);
        }

        if ($modelClass === BookHighschool::class) {
            $query = BookHighschool::query();
            $shelvesQuery = Shelf::orderBy('shelf_number');
        } else {
            $query = Book::query();
            $shelvesQuery = Shelf::orderBy('shelf_number');

            if ($campusFilter !== null) {
                $query->whereIn('campus', $campusFilter);
                $shelvesQuery->whereIn('campus', $campusFilter);
            }
        }

        // Global Search
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('accession_no', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%")
                  ->orWhere('call_number', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhere('campus', 'like', "%{$search}%")
                  ->orWhere('shelf_number', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%");
            });
        }

        // Column-specific Filters
        if ($request->filled('accession_no')) {
            $query->where('accession_no', 'like', "%{$request->accession_no}%");
        }
        if ($request->filled('barcode')) {
            $query->where('barcode', 'like', "%{$request->barcode}%");
        }
        if ($request->filled('title')) {
            $query->where('title', 'like', "%{$request->title}%");
        }
        if ($request->filled('author')) {
            $query->where('author', 'like', "%{$request->author}%");
        }
        if ($request->filled('call_number')) {
            $query->where('call_number', 'like', "%{$request->call_number}%");
        }
        if ($request->filled('location')) {
            $query->where('location', 'like', "%{$request->location}%");
        }
        if ($request->filled('campus')) {
            $query->where('campus', $request->campus);
        }
        if ($request->filled('shelf_number')) {
            $query->where('shelf_number', $request->shelf_number);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Sorting
        $sort = $request->input('sort', 'created_at');
        $direction = $request->input('direction', 'desc');
        $allowedSorts = ['accession_no', 'barcode', 'title', 'author', 'call_number', 'location', 'campus', 'shelf_number', 'status', 'created_at'];
        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->latest();
        }

        $books = $query->paginate(10);
        $shelves = $shelvesQuery->get();

        // Route to the correct view based on model
        $view = ($modelClass === BookHighschool::class)
            ? 'admin.library.books_highschool'
            : 'admin.library.books';

        return view($view, compact('books', 'shelves'));
    }

    /**
     * Books index for DCC BED Elementary (uses books_elem table).
     */
    private function booksIndexElem(Request $request)
    {
        $query = BookElem::query();

        // Global Search
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('accession_number', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%")
                  ->orWhere('call_number', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%");
            });
        }

        // Column-specific Filters
        if ($request->filled('accession_no')) {
            $query->where('accession_number', 'like', "%{$request->accession_no}%");
        }
        if ($request->filled('barcode')) {
            $query->where('barcode', 'like', "%{$request->barcode}%");
        }
        if ($request->filled('title')) {
            $query->where('title', 'like', "%{$request->title}%");
        }
        if ($request->filled('author')) {
            $query->where('author', 'like', "%{$request->author}%");
        }
        if ($request->filled('call_number')) {
            $query->where('call_number', 'like', "%{$request->call_number}%");
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Sorting
        $sort = $request->input('sort', 'created_at');
        $direction = $request->input('direction', 'desc');
        $allowedSorts = ['id', 'accession_number', 'barcode', 'title', 'author', 'call_number', 'status', 'created_at'];
        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $elemBooks = $query->paginate(10);
        $shelves = collect();
        return view('admin.library.books_elem', compact('elemBooks', 'shelves'));
    }

    public function booksElementaryIndex(Request $request)
    {
        if (auth()->user()?->role !== 'Admin BEDELEM') {
            abort(403, 'This page is only available to the BED Elementary admin account.');
        }

        return $this->booksIndexElem($request);
    }

    public function booksStore(Request $request)
    {
        $role = auth()->user()?->role ?? '';
        $modelClass = $this->getBookModel();

        if ($modelClass === BookElem::class) {
            $request->validate([
                'accession_no' => 'required|string|unique:books_elem,accession_number',
                'barcode'      => 'nullable|string|unique:books_elem,barcode',
                'title'        => 'required|string',
                'author'       => 'required|string',
                'call_number'  => 'required|string',
            ]);

            BookElem::create([
                'accession_number' => $request->accession_no,
                'barcode'          => $request->barcode,
                'title'            => $request->title,
                'author'           => $request->author,
                'call_number'      => $request->call_number,
                'campus'           => 'DCC BED Elementary',
                'status'           => 'Available',
            ]);

            return response()->json(['success' => true, 'message' => 'Book added successfully']);
        }

        if ($modelClass === BookHighschool::class) {
            $request->validate([
                'accession_no' => 'required|string|unique:books_highschool,accession_no',
                'barcode' => 'nullable|string|unique:books_highschool,barcode',
                'title' => 'required|string',
                'author' => 'required|string',
                'call_number' => 'required|string',
                'location' => 'nullable|string',
                'shelf_number' => 'nullable|string'
            ]);

            BookHighschool::create([
                'accession_no' => $request->accession_no,
                'barcode' => $request->barcode,
                'title' => $request->title,
                'author' => $request->author,
                'call_number' => $request->call_number,
                'location' => $request->location,
                'shelf_number' => $request->shelf_number,
                'campus' => 'DCC BED Highschool',
                'status' => 'Available'
            ]);

            return response()->json(['success' => true, 'message' => 'Book added successfully']);
        }

        // Default: books_main (TED / Admin / Master)
        $rules = [
            'accession_no' => 'required|string|unique:books_main,accession_no',
            'barcode' => 'nullable|string|unique:books_main,barcode',
            'title' => 'required|string',
            'author' => 'required|string',
            'call_number' => 'required|string',
            'location' => 'nullable|string',
            'shelf_number' => 'nullable|string'
        ];

        $request->validate($rules);

        Book::create([
            'accession_no' => $request->accession_no,
            'barcode' => $request->barcode,
            'title' => $request->title,
            'author' => $request->author,
            'call_number' => $request->call_number,
            'location' => $request->location,
            'shelf_number' => $request->shelf_number,
            'campus' => 'DCC TED',
            'status' => 'Available'
        ]);

        return response()->json(['success' => true, 'message' => 'Book added successfully']);
    }

    public function booksElementaryStore(Request $request)
    {
        if (auth()->user()?->role !== 'Admin BEDELEM') {
            abort(403, 'Only the BED Elementary admin can add books here.');
        }

        $request->validate([
            'accession_no' => 'required|string|unique:books_elem,accession_number',
            'barcode' => 'nullable|string|unique:books_elem,barcode',
            'title' => 'required|string',
            'author' => 'required|string',
            'call_number' => 'required|string',
        ]);

        BookElem::create([
            'accession_number' => $request->accession_no,
            'barcode' => $request->barcode,
            'title' => $request->title,
            'author' => $request->author,
            'call_number' => $request->call_number,
            'campus' => 'DCC BED Elementary',
            'status' => 'Available',
        ]);

        return response()->json(['success' => true, 'message' => 'Book added successfully']);
    }

    public function booksUpdate(Request $request, $accession_no)
    {
        $modelClass = $this->getBookModel();

        if ($modelClass === BookElem::class) {
            $book = BookElem::findOrFail($accession_no);
            $request->validate([
                'accession_no' => 'required|string|unique:books_elem,accession_number,' . $accession_no . ',accession_number',
                'barcode'      => 'nullable|string|unique:books_elem,barcode,' . $accession_no . ',accession_number',
                'title'        => 'required|string',
                'author'       => 'required|string',
                'call_number'  => 'required|string',
                'status'       => 'nullable|string|in:Available,Borrowed,available,borrowed',
            ]);

            $book->update([
                'accession_number' => $request->accession_no,
                'barcode'          => $request->barcode,
                'title'            => $request->title,
                'author'           => $request->author,
                'call_number'      => $request->call_number,
                'status'           => $request->input('status', $book->status ?? 'Available'),
            ]);

            return response()->json(['success' => true, 'message' => 'Book updated successfully']);
        }

        if ($modelClass === BookHighschool::class) {
            $book = BookHighschool::findOrFail($accession_no);
            $request->validate([
                'accession_no' => 'required|string|unique:books_highschool,accession_no,' . $accession_no . ',accession_no',
                'barcode' => 'nullable|string|unique:books_highschool,barcode,' . $accession_no . ',accession_no',
                'title' => 'required|string',
                'author' => 'required|string',
                'call_number' => 'required|string',
                'location' => 'nullable|string',
                'shelf_number' => 'nullable|string',
                'status' => 'required|in:Available,Borrowed,available,borrowed'
            ]);

            $updateData = $request->only('accession_no', 'barcode', 'title', 'author', 'call_number', 'location', 'shelf_number', 'status');
            $updateData['campus'] = 'DCC BED Highschool';

            $book->update($updateData);
            return response()->json(['success' => true, 'message' => 'Book updated successfully']);
        }

        // Default: books_main
        $book = Book::findOrFail($accession_no);
        $rules = [
            'accession_no' => 'required|string|unique:books_main,accession_no,' . $accession_no . ',accession_no',
            'barcode' => 'nullable|string|unique:books_main,barcode,' . $accession_no . ',accession_no',
            'title' => 'required|string',
            'author' => 'required|string',
            'call_number' => 'required|string',
            'location' => 'nullable|string',
            'shelf_number' => 'nullable|string',
            'status' => 'required|in:Available,Borrowed,available,borrowed'
        ];

        $request->validate($rules);

        $updateData = $request->only('accession_no', 'barcode', 'title', 'author', 'call_number', 'location', 'shelf_number', 'status');
        $updateData['campus'] = 'DCC TED';

        $book->update($updateData);
        return response()->json(['success' => true, 'message' => 'Book updated successfully']);
    }

    public function booksElementaryUpdate(Request $request, $accession_no)
    {
        if (auth()->user()?->role !== 'Admin BEDELEM') {
            abort(403, 'Only the BED Elementary admin can update books here.');
        }

        $book = BookElem::findOrFail($accession_no);
        $request->validate([
            'accession_no' => 'required|string|unique:books_elem,accession_number,' . $accession_no . ',accession_number',
            'barcode' => 'nullable|string|unique:books_elem,barcode,' . $accession_no . ',accession_number',
            'title' => 'required|string',
            'author' => 'required|string',
            'call_number' => 'required|string',
        ]);

        $book->update([
            'accession_number' => $request->accession_no,
            'barcode' => $request->barcode,
            'title' => $request->title,
            'author' => $request->author,
            'call_number' => $request->call_number,
        ]);

        return response()->json(['success' => true, 'message' => 'Book updated successfully']);
    }

    public function booksDestroy($accession_no)
    {
        $modelClass = $this->getBookModel();

        if ($modelClass === BookElem::class) {
            BookElem::findOrFail($accession_no)->delete();
        } elseif ($modelClass === BookHighschool::class) {
            BookHighschool::findOrFail($accession_no)->delete();
        } else {
            Book::findOrFail($accession_no)->delete();
        }

        return response()->json(['success' => true, 'message' => 'Book deleted successfully']);
    }

    public function booksElementaryDestroy($accession_no)
    {
        if (auth()->user()?->role !== 'Admin BEDELEM') {
            abort(403, 'Only the BED Elementary admin can delete books here.');
        }

        BookElem::findOrFail($accession_no)->delete();

        return response()->json(['success' => true, 'message' => 'Book deleted successfully']);
    }

    // ----- BORROWING -----
    public function borrowIndex()
    {
        return view('admin.library.borrow');
    }

    public function checkBook(Request $request)
    {
        $request->validate([
            'accession_no' => 'required|string',
        ]);

        $modelClass = $this->getBookModel();

        if ($modelClass === BookElem::class) {
            $book = BookElem::where(function ($q) use ($request) {
                $q->where('barcode', $request->accession_no)
                    ->orWhere('accession_number', $request->accession_no);
            })->first();
        } elseif ($modelClass === BookHighschool::class) {
            $book = BookHighschool::where(function ($q) use ($request) {
                $q->where('barcode', $request->accession_no)
                    ->orWhere('accession_no', $request->accession_no);
            })->first();
        } else {
            $campusFilter = $this->getRoleCampusFilter();
            $bookQuery = Book::where(function ($q) use ($request) {
                $q->where('barcode', $request->accession_no)
                    ->orWhere('accession_no', $request->accession_no);
            });

            if ($campusFilter !== null) {
                $bookQuery->whereIn('campus', $campusFilter);
            }

            $book = $bookQuery->first();
        }

        if (!$book) {
            return response()->json(['success' => false, 'message' => 'Book not found (by barcode or accession no).'], 404);
        }

        if ($book->status === 'Borrowed') {
            return response()->json(['success' => false, 'message' => 'Book is currently borrowed.'], 400);
        }

        $accNo = $book->accession_no ?? $book->accession_number;

        return response()->json([
            'success' => true,
            'book' => [
                'id' => $book->id ?? $book->accession_number,
                'title' => $book->title,
                'author' => $book->author,
                'call_number' => $book->call_number,
                'accession_no' => $accNo,
                'barcode' => $book->barcode
            ]
        ]);
    }

    public function borrowStore(Request $request)
    {
        // Support both batch (array of books) and single-book format
        if ($request->has('books') && is_array($request->books)) {
            $request->validate([
                'borrower_id'  => 'required|string',
                'borrow_type'  => 'required|in:Student,Faculty,Staff',
                'books'        => 'required|array|min:1',
                'books.*.accession_no' => 'required|string',
                'books.*.book_section' => 'required|in:Reserved,Filipiniana,Circulation,Fiction,Thesis & Dissertation',
                'books.*.borrow_period' => 'required|string',
            ]);
            $booksList = $request->books;
        } else {
            $request->validate([
                'borrower_id'  => 'required|string',
                'accession_no' => 'required|string',
                'borrow_type'  => 'required|in:Student,Faculty,Staff',
                'book_section' => 'required|in:Reserved,Filipiniana,Circulation,Fiction,Thesis & Dissertation',
                'borrow_period' => 'required|string',
            ]);
            $booksList = [[
                'accession_no' => $request->accession_no,
                'book_section' => $request->book_section,
                'borrow_period' => $request->borrow_period
            ]];
        }

        // Resolve borrower
        $student = Student::where('sid', $request->borrower_id)
            ->orWhere('rfid', $request->borrower_id)->first();

        $employee = Employee::where('id', $request->borrower_id)
            ->orWhere('rfid', $request->borrower_id)->first();

        $borrower = $student ?: $employee;
        if (!$borrower) {
            return response()->json(['success' => false, 'message' => 'Borrower not found in Students or Employees.'], 404);
        }

        $modelClass = $this->getBookModel();
        $campusFilter = $this->getRoleCampusFilter();

        try {
            $transactionsData = \Illuminate\Support\Facades\DB::transaction(function () use ($booksList, $request, $borrower, $modelClass, $campusFilter) {
                $createdTransactions = [];
                $processedBooks = [];

                foreach ($booksList as $item) {
                    if ($modelClass === BookElem::class) {
                        $book = BookElem::where(function ($q) use ($item) {
                            $q->where('barcode', $item['accession_no'])
                              ->orWhere('accession_number', $item['accession_no']);
                        })->first();
                    } elseif ($modelClass === BookHighschool::class) {
                        $book = BookHighschool::where(function ($q) use ($item) {
                            $q->where('barcode', $item['accession_no'])
                              ->orWhere('accession_no', $item['accession_no']);
                        })->first();
                    } else {
                        $bookQuery = Book::where(function ($q) use ($item) {
                            $q->where('barcode', $item['accession_no'])
                              ->orWhere('accession_no', $item['accession_no']);
                        });

                        if ($campusFilter !== null) {
                            $bookQuery->whereIn('campus', $campusFilter);
                        }

                        $book = $bookQuery->first();
                    }

                    if (!$book) {
                        throw new \Exception("Book with barcode/accession '" . $item['accession_no'] . "' not found.");
                    }

                    if ($book->status === 'Borrowed') {
                        throw new \Exception("Book '" . $book->title . "' is currently borrowed.");
                    }

                    $period = $item['borrow_period'];

                    $firstSemDueDate = Carbon::today()->startOfMonth()->month(12)->day(31);
                    if (Carbon::today()->gt($firstSemDueDate)) {
                        $firstSemDueDate->addYear();
                    }

                    $secondSemDueDate = Carbon::today()->startOfMonth()->month(6)->day(30);
                    if (Carbon::today()->gt($secondSemDueDate)) {
                        $secondSemDueDate->addYear();
                    }

                    $due_date = match ($period) {
                        '30 minutes'   => Carbon::now()->addMinutes(30),
                        '1 day'        => Carbon::today()->addDay(),
                        '3 days'       => Carbon::today()->addDays(3),
                        '5 days'       => Carbon::today()->addDays(5),
                        '1st Semester' => $firstSemDueDate,
                        '2nd Semester' => $secondSemDueDate,
                        'Inside Reading' => Carbon::now()->addHours(4),
                        'Summer Class' => Carbon::today()->addWeeks(6),
                        default        => Carbon::today()->addWeek(),
                    };

                    $book->update(['status' => 'Borrowed']);

                    $accNo = $book->accession_no ?? $book->accession_number;

                    $transaction = new Transaction();
                    $transaction->borrower_id   = $request->borrower_id;
                    $transaction->borrower_type = get_class($borrower);
                    $transaction->borrow_type   = $request->borrow_type;
                    $transaction->book_section  = $item['book_section'];
                    $transaction->book_id       = $book->id ?? null;
                    $transaction->book_type     = $modelClass;
                    $transaction->accession_no  = $accNo;
                    $transaction->date_borrowed = Carbon::now();
                    $transaction->due_date      = $due_date;
                    $transaction->status        = 'Borrowed';
                    $transaction->save();

                    $createdTransactions[] = $transaction;
                    $processedBooks[] = [
                        'title'       => $book->title,
                        'author'      => $book->author,
                        'call_number' => $book->call_number,
                        'location'    => $book->location ?? '',
                        'shelf_number' => $book->shelf_number ?? '',
                        'accession_no' => $accNo,
                        'barcode'     => $book->barcode,
                        'book_section' => $item['book_section'],
                        'due_date'    => $due_date,
                        'date_borrowed' => Carbon::now()
                    ];
                }

                return [
                    'transactions' => $createdTransactions,
                    'books' => $processedBooks
                ];
            });

            $borrowerName = trim(implode(' ', array_filter([
                $borrower->firstname,
                $borrower->middlename ? $borrower->middlename[0] . '.' : null,
                $borrower->lastname,
            ])));

            return response()->json([
                'success'       => true,
                'message'       => 'Books borrowed successfully.',
                'transactions'  => $transactionsData['transactions'],
                'borrower_name' => $borrowerName,
                'borrower_year'   => $borrower->year   ?? null,
                'borrower_course' => $borrower->course ?? null,
                'borrow_type'   => $request->borrow_type,
                'borrower_id'   => $request->borrower_id,
                'books'         => $transactionsData['books'],
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
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

        $modelClass = $this->getBookModel();

        if ($modelClass === BookElem::class) {
            $book = BookElem::where(function ($q) use ($request) {
                $q->where('accession_number', $request->accession_no)
                    ->orWhere('barcode', $request->accession_no);
            })->first();
        } elseif ($modelClass === BookHighschool::class) {
            $book = BookHighschool::where(function ($q) use ($request) {
                $q->where('accession_no', $request->accession_no)
                    ->orWhere('barcode', $request->accession_no);
            })->first();
        } else {
            $campusFilter = $this->getRoleCampusFilter();
            $bookQuery = Book::where(function ($q) use ($request) {
                $q->where('accession_no', $request->accession_no)
                    ->orWhere('barcode', $request->accession_no);
            });

            if ($campusFilter !== null) {
                $bookQuery->whereIn('campus', $campusFilter);
            }

            $book = $bookQuery->first();
        }

        if (!$book) {
            return response()->json(['success' => false, 'message' => 'Book not found.'], 404);
        }

        $accNo = $book->accession_no ?? $book->accession_number;

        $transaction = Transaction::where('accession_no', $accNo)
            ->where('status', 'Borrowed')
            ->first();

        if (!$transaction) {
            return response()->json(['success' => false, 'message' => 'No active borrow transaction found for this book.'], 404);
        }

        $now = Carbon::now();
        $dueDate = Carbon::parse($transaction->due_date);

        $daysOverdue = 0;
        $hoursOverdue = 0;
        $fine = 0;

        if ($now->gt($dueDate)) {
            if ($transaction->book_section === 'Reserved') {
                $hoursOverdue = $dueDate->diffInHours($now);
                $fine = max(1, $hoursOverdue) * 5;
            } else {
                $daysOverdue = $dueDate->diffInDays($now);
                $fine = $daysOverdue * 5;
            }
        }

        $transaction->update([
            'date_returned' => $now,
            'fine' => $fine,
            'status' => 'Returned'
        ]);

        $book->update(['status' => 'Available']);

        return response()->json([
            'success' => true,
            'message' => 'Book returned successfully.',
            'fine' => $fine,
            'days_overdue' => $daysOverdue,
            'hours_overdue' => $hoursOverdue,
            'book_section' => $transaction->book_section
        ]);
    }

    // ----- HISTORY -----
    public function historyIndex()
    {
        $modelClass = $this->getBookModel();
        $campusFilter = $this->getRoleCampusFilter();
        $query = Transaction::with(['book', 'borrower'])->orderBy('created_at', 'desc');

        // For users on separate book tables (BooksHighschool, BookElem), 
        // filter by book_type so they only see their own transactions
        if ($modelClass === BookHighschool::class) {
            $query->where('book_type', BookHighschool::class);
        } elseif ($modelClass === BookElem::class) {
            $query->where('book_type', BookElem::class);
        } elseif ($campusFilter !== null) {
            // For users on books_main, apply campus filter
            $query->whereHas('book', function ($q) use ($campusFilter) {
                $q->whereIn('campus', $campusFilter);
            });
        }

        $transactions = $query->get();
        return view('admin.library.history', compact('transactions'));
    }

    // ----- CHARGES / OVERDUE FINES -----
    public function chargesIndex(Request $request)
    {
        $modelClass = $this->getBookModel();
        $campusFilter = $this->getRoleCampusFilter();

        $query = Transaction::with(['book', 'borrower'])
            ->where(function ($q) {
                $q->where('fine', '>', 0)
                  ->orWhere(function ($sq) {
                      $sq->where('status', 'Borrowed')
                         ->where('due_date', '<', Carbon::now());
                  });
            });

        // For users on separate book tables, filter by book_type
        if ($modelClass === BookHighschool::class) {
            $query->where('book_type', BookHighschool::class);
        } elseif ($modelClass === BookElem::class) {
            $query->where('book_type', BookElem::class);
        } elseif ($campusFilter !== null) {
            $query->whereHas('book', function ($q) use ($campusFilter) {
                $q->whereIn('campus', $campusFilter);
            });
        }

        // Search Filter
        if ($request->filled('search')) {
            $search = $request->search;
            $searchTerm = strtolower($search);
            $query->where(function ($q) use ($search, $searchTerm) {
                $q->where('borrower_id', 'like', "%{$search}%")
                  ->orWhere(function ($borrowerQuery) use ($searchTerm) {
                      $borrowerQuery->whereRaw('LOWER(COALESCE(borrower_id, "")) LIKE ?', ['%' . $searchTerm . '%']);

                      $studentIds = Student::query()
                          ->whereRaw('LOWER(COALESCE(firstname, "")) LIKE ?', ['%' . $searchTerm . '%'])
                          ->orWhereRaw('LOWER(COALESCE(lastname, "")) LIKE ?', ['%' . $searchTerm . '%'])
                          ->orWhereRaw('LOWER(COALESCE(middlename, "")) LIKE ?', ['%' . $searchTerm . '%'])
                          ->orWhereRaw('LOWER(COALESCE(department, "")) LIKE ?', ['%' . $searchTerm . '%'])
                          ->pluck('sid');

                      $employeeIds = Employee::query()
                          ->whereRaw('LOWER(COALESCE(firstname, "")) LIKE ?', ['%' . $searchTerm . '%'])
                          ->orWhereRaw('LOWER(COALESCE(lastname, "")) LIKE ?', ['%' . $searchTerm . '%'])
                          ->orWhereRaw('LOWER(COALESCE(middlename, "")) LIKE ?', ['%' . $searchTerm . '%'])
                          ->orWhereRaw('LOWER(COALESCE(department, "")) LIKE ?', ['%' . $searchTerm . '%'])
                          ->pluck('id');

                      if ($studentIds->isNotEmpty() || $employeeIds->isNotEmpty()) {
                          $borrowerQuery->orWhereIn('borrower_id', $studentIds->merge($employeeIds)->all());
                      }
                  })
                  ->orWhereHas('book', function ($bookQuery) use ($searchTerm) {
                      $bookQuery->whereRaw('LOWER(COALESCE(title, "")) LIKE ?', ['%' . $searchTerm . '%'])
                          ->orWhereRaw('LOWER(COALESCE(author, "")) LIKE ?', ['%' . $searchTerm . '%'])
                          ->orWhereRaw('LOWER(COALESCE(call_number, "")) LIKE ?', ['%' . $searchTerm . '%'])
                          ->orWhereRaw('LOWER(COALESCE(location, "")) LIKE ?', ['%' . $searchTerm . '%']);
                  });
            });
        }

        // Status Filter
        if ($request->filled('status')) {
            $status = $request->status;
            if ($status === 'active') {
                $query->where('status', 'Borrowed')
                      ->where('due_date', '<', Carbon::now());
            } elseif ($status === 'returned') {
                $query->where('status', 'Returned')
                      ->where('fine', '>', 0);
            }
        }

        $query->orderBy('due_date', 'desc');

        // Total active overdue books count
        $totalActiveOverdueQuery = Transaction::where('status', 'Borrowed')
            ->where('due_date', '<', Carbon::now());
        if ($modelClass === BookHighschool::class) {
            $totalActiveOverdueQuery->where('book_type', BookHighschool::class);
        } elseif ($modelClass === BookElem::class) {
            $totalActiveOverdueQuery->where('book_type', BookElem::class);
        } elseif ($campusFilter !== null) {
            $totalActiveOverdueQuery->whereHas('book', function ($q) use ($campusFilter) {
                $q->whereIn('campus', $campusFilter);
            });
        }
        $totalActiveOverdueCount = $totalActiveOverdueQuery->count();

        // Total collected/recorded fines
        $totalFinesQuery = Transaction::where('fine', '>', 0);
        if ($modelClass === BookHighschool::class) {
            $totalFinesQuery->where('book_type', BookHighschool::class);
        } elseif ($modelClass === BookElem::class) {
            $totalFinesQuery->where('book_type', BookElem::class);
        } elseif ($campusFilter !== null) {
            $totalFinesQuery->whereHas('book', function ($q) use ($campusFilter) {
                $q->whereIn('campus', $campusFilter);
            });
        }
        $totalFinesSum = $totalFinesQuery->sum('fine');

        $transactions = $query->paginate(15)->withQueryString();

        $now = Carbon::now();
        $transactions->getCollection()->transform(function ($txn) use ($now) {
            if ($txn->status === 'Borrowed' && $now->gt(Carbon::parse($txn->due_date))) {
                $dueDate = Carbon::parse($txn->due_date);
                if ($txn->book_section === 'Reserved') {
                    $hoursOverdue = $dueDate->diffInHours($now);
                    $txn->calculated_fine = max(1, $hoursOverdue) * 5;
                } else {
                    $daysOverdue = $dueDate->diffInDays($now);
                    $txn->calculated_fine = $daysOverdue * 5;
                }
                $txn->is_active_overdue = true;
            } else {
                $txn->calculated_fine = $txn->fine;
                $txn->is_active_overdue = false;
            }

            if ($txn->borrower_type === 'App\Models\Student') {
                $txn->borrower_details = Student::where('sid', $txn->borrower_id)
                    ->orWhere('rfid', $txn->borrower_id)
                    ->first();
            } else {
                $txn->borrower_details = Employee::where('id', $txn->borrower_id)
                    ->orWhere('rfid', $txn->borrower_id)
                    ->orWhere('eid', $txn->borrower_id)
                    ->first();
            }

            return $txn;
        });

        return view('admin.library.charges', compact('transactions', 'totalActiveOverdueCount', 'totalFinesSum'));
    }


    // ----- REPORTS -----
    public function reportsIndex()
    {
        $campusFilter = $this->getRoleCampusFilter();

        // Monthly report
        $monthlyQuery = Transaction::selectRaw('MONTHNAME(date_borrowed) as month,
            COUNT(*) as total_borrowed,
            SUM(CASE WHEN date_returned IS NOT NULL THEN 1 ELSE 0 END) as total_returned,
            SUM(CASE WHEN status != "Returned" AND due_date < CURDATE() THEN 1 ELSE 0 END) as total_overdue');

        // Top borrowed books
        $topBooksQuery = Transaction::selectRaw('accession_no, COUNT(*) as times_borrowed')
            ->with('book')
            ->groupBy('accession_no')
            ->orderByDesc('times_borrowed')
            ->limit(10);

        // Top student borrowers
        $topStudentsQuery = Transaction::selectRaw('borrower_id, COUNT(*) as total_borrowed')
            ->where('borrower_type', 'App\Models\Student')
            ->groupBy('borrower_id')
            ->orderByDesc('total_borrowed')
            ->limit(10);

        // Top employee borrowers
        $topEmployeesQuery = Transaction::selectRaw('borrower_id, COUNT(*) as total_borrowed')
            ->whereIn('borrower_type', ['App\Models\Employee', 'App\\Models\\Employee'])
            ->groupBy('borrower_id')
            ->orderByDesc('total_borrowed')
            ->limit(10);

        $modelClass = $this->getBookModel();
        if ($modelClass === BookHighschool::class || $modelClass === BookElem::class) {
            $monthlyQuery->where('book_type', $modelClass);
            $topBooksQuery->where('book_type', $modelClass);
            $topStudentsQuery->where('book_type', $modelClass);
            $topEmployeesQuery->where('book_type', $modelClass);
        } elseif ($campusFilter !== null) {
            $monthlyQuery->whereHas('book', function ($q) use ($campusFilter) {
                $q->whereIn('campus', $campusFilter);
            });
            $topBooksQuery->whereHas('book', function ($q) use ($campusFilter) {
                $q->whereIn('campus', $campusFilter);
            });
            $topStudentsQuery->whereHas('book', function ($q) use ($campusFilter) {
                $q->whereIn('campus', $campusFilter);
            });
            $topEmployeesQuery->whereHas('book', function ($q) use ($campusFilter) {
                $q->whereIn('campus', $campusFilter);
            });
        }

        $monthlyReport = $monthlyQuery->groupBy('month')->get();
        $topBooks = $topBooksQuery->get();

        $topStudents = $topStudentsQuery->get()
            ->map(function ($t) {
                $student = Student::where('sid', $t->borrower_id)
                    ->orWhere('rfid', $t->borrower_id)
                    ->first();
                $t->borrower = $student;
                return $t;
            })
            ->filter(fn($t) => $t->borrower !== null)
            ->values();

        $topEmployees = $topEmployeesQuery->get()
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
            'monthlyReport',
            'topBooks',
            'topStudents',
            'topEmployees'
        ));
    }

    public function reportsExport()
    {
        $filename = 'library_report_' . now()->format('Y-m-d') . '.xls';
        $modelClass   = $this->getBookModel();
        $campusFilter = $this->getRoleCampusFilter();

        // ── Fetch all data ──────────────────────────────────────────────
        $monthlyQuery = Transaction::selectRaw('MONTHNAME(date_borrowed) as month,
            COUNT(*) as total_borrowed,
            SUM(CASE WHEN date_returned IS NOT NULL THEN 1 ELSE 0 END) as total_returned,
            SUM(CASE WHEN status != "Returned" AND due_date < CURDATE() THEN 1 ELSE 0 END) as total_overdue');

        $booksQuery = Transaction::selectRaw('accession_no, COUNT(*) as times_borrowed')
            ->with('book')->groupBy('accession_no')->orderByDesc('times_borrowed')->limit(10);

        $studentTxnsQuery = Transaction::selectRaw('borrower_id, COUNT(*) as total_borrowed')
            ->where('borrower_type', 'App\Models\Student')
            ->groupBy('borrower_id')->orderByDesc('total_borrowed')->limit(10);

        $employeeTxnsQuery = Transaction::selectRaw('borrower_id, COUNT(*) as total_borrowed')
            ->where('borrower_type', 'like', '%Employee%')
            ->groupBy('borrower_id')->orderByDesc('total_borrowed')->limit(10);

        if ($modelClass === BookHighschool::class || $modelClass === BookElem::class) {
            $monthlyQuery->where('book_type', $modelClass);
            $booksQuery->where('book_type', $modelClass);
            $studentTxnsQuery->where('book_type', $modelClass);
            $employeeTxnsQuery->where('book_type', $modelClass);
        } elseif ($campusFilter !== null) {
            $monthlyQuery->whereHas('book', function ($q) use ($campusFilter) {
                $q->whereIn('campus', $campusFilter);
            });
            $booksQuery->whereHas('book', function ($q) use ($campusFilter) {
                $q->whereIn('campus', $campusFilter);
            });
            $studentTxnsQuery->whereHas('book', function ($q) use ($campusFilter) {
                $q->whereIn('campus', $campusFilter);
            });
            $employeeTxnsQuery->whereHas('book', function ($q) use ($campusFilter) {
                $q->whereIn('campus', $campusFilter);
            });
        }

        $monthly = $monthlyQuery->groupBy('month')->get();
        $books = $booksQuery->get();
        $studentTxns = $studentTxnsQuery->get();
        $employeeTxns = $employeeTxnsQuery->get();

        $studentRows = [];
        $rank = 1;
        foreach ($studentTxns as $t) {
            $s = Student::where('sid', $t->borrower_id)->orWhere('rfid', $t->borrower_id)->first();
            if (!$s) continue;
            $name = trim("{$s->firstname} " . ($s->middlename ? $s->middlename[0] . '. ' : '') . $s->lastname);
            $studentRows[] = [$rank++, $name, $s->course ?? '', $s->year ?? '', $s->sid ?? $t->borrower_id, $t->total_borrowed];
        }

        $employeeRows = [];
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
            $r->month,
            $r->total_borrowed,
            $r->total_returned,
            $r->total_overdue
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

    // ----- SHELVES CRUD -----
    public function shelvesIndex()
    {
        $location = session('location');
        $query = Shelf::orderBy('shelf_number');

        if ($location !== null && $location !== 'Master') {
            $campuses = match ($location) {
                'DCC BED' => ['DCC BED Highschool', 'DCC BED SeniorHighSchool', 'DCC BED Elementary'],
                default   => [$location],
            };
            $query->whereIn('campus', $campuses);
        }

        $shelves = $query->get();

        // Route to the view matching the logged-in admin's campus scope.
        $view = match ($location) {
            'Master'   => 'admin.library.shelves_combined',
            'DCC TED'  => 'admin.library.shelves_ted',
            default    => 'admin.library.shelves_bed', // DCC BED + the 3 BED sub-campuses
        };

        return view($view, compact('shelves'));
    }

    public function shelvesStore(Request $request)
    {
        $location = session('location');
        $rules = [
            'shelf_number' => 'required|string|unique:shelves,shelf_number',
            'description'  => 'nullable|string'
        ];

        if ($location === 'Master' || $location === 'DCC BED') {
            $rules['campus'] = 'required|string|in:DCC TED,DCC BED Highschool,DCC BED SeniorHighSchool,DCC BED Elementary';
        }

        $request->validate($rules);

        $campus = ($location === 'Master' || $location === 'DCC BED') ? $request->campus : $location;

        Shelf::create([
            'shelf_number' => $request->shelf_number,
            'description'  => $request->description,
            'campus'       => $campus
        ]);

        return response()->json(['success' => true, 'message' => 'Shelf added successfully']);
    }

    public function shelvesUpdate(Request $request, $id)
    {
        $shelf = Shelf::findOrFail($id);
        $location = session('location');
        $rules = [
            'shelf_number' => 'required|string|unique:shelves,shelf_number,' . $id,
            'description'  => 'nullable|string'
        ];

        if ($location === 'Master' || $location === 'DCC BED') {
            $rules['campus'] = 'required|string|in:DCC TED,DCC BED Highschool,DCC BED SeniorHighSchool,DCC BED Elementary';
        }

        $request->validate($rules);

        $updateData = $request->only('shelf_number', 'description');
        if ($location === 'Master' || $location === 'DCC BED') {
            $updateData['campus'] = $request->campus;
        }

        $shelf->update($updateData);

        return response()->json(['success' => true, 'message' => 'Shelf updated successfully']);
    }

    public function shelvesDestroy($id)
    {
        Shelf::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Shelf deleted successfully']);
    }
}