<?php

namespace App\Http\Controllers;

use App\Models\BookType;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BookTypeController extends Controller
{
    private function checkMaster()
    {
        if (auth()->user()?->role !== 'Master') {
            abort(403, 'Unauthorized. Only the Master account can manage Book Types.');
        }
    }

    public function index(Request $request)
    {
        $this->checkMaster();

        $search = $request->input('search');
        $status = $request->input('status');
        $tab    = $request->input('tab', 'all'); // 'all', 'college', 'elementary', 'highschool'

        $query = BookType::query();

        // Filter by Tab: College, Elementary, and High School tabs include their specific types + universal ('All') types
        if ($tab === 'college') {
            $query->where(function ($q) {
                $q->where('level', 'College')
                  ->orWhere('level', 'All');
            });
        } elseif ($tab === 'elementary') {
            $query->where(function ($q) {
                $q->where('level', 'Elementary')
                  ->orWhere('level', 'All');
            });
        } elseif ($tab === 'highschool') {
            $query->where(function ($q) {
                $q->where('level', 'High School / Senior High School')
                  ->orWhere('level', 'All');
            });
        }
        // 'all' tab displays all book types in the database

        // Global Search
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($status && in_array($status, ['ACTIVE', 'INACTIVE'])) {
            $query->where('status', $status);
        }

        $bookTypes = $query->orderBy('name', 'asc')->paginate(12)->withQueryString();

        $totalCount    = BookType::count();
        $collegeCount  = BookType::where(fn($q) => $q->where('level', 'College')->orWhere('level', 'All'))->count();
        $elemCount     = BookType::where(fn($q) => $q->where('level', 'Elementary')->orWhere('level', 'All'))->count();
        $hsCount       = BookType::where(fn($q) => $q->where('level', 'High School / Senior High School')->orWhere('level', 'All'))->count();
        $allLevelCount = BookType::where('level', 'All')->count();
        $activeCount   = BookType::where('status', 'ACTIVE')->count();
        $inactiveCount = BookType::where('status', 'INACTIVE')->count();

        return view('admin.library.book_types', compact(
            'bookTypes',
            'search',
            'status',
            'tab',
            'totalCount',
            'collegeCount',
            'elemCount',
            'hsCount',
            'allLevelCount',
            'activeCount',
            'inactiveCount'
        ));
    }

    public function store(Request $request)
    {
        $this->checkMaster();

        $validated = $request->validate([
            'name'        => 'required|string|max:100|unique:book_types,name',
            'level'       => 'required|string|in:All,College,Elementary,High School / Senior High School',
            'description' => 'nullable|string|max:1000',
            'status'      => 'required|in:ACTIVE,INACTIVE',
        ]);

        BookType::create($validated);

        $tab = match ($validated['level']) {
            'College' => 'college',
            'Elementary' => 'elementary',
            'High School / Senior High School' => 'highschool',
            default => 'all',
        };

        return redirect()->route('admin.library.book-types.index', ['tab' => $tab])
            ->with('success', 'Book Type created successfully.');
    }

    public function update(Request $request, BookType $bookType)
    {
        $this->checkMaster();

        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:100', Rule::unique('book_types', 'name')->ignore($bookType->id)],
            'level'       => 'required|string|in:All,College,Elementary,High School / Senior High School',
            'description' => 'nullable|string|max:1000',
            'status'      => 'required|in:ACTIVE,INACTIVE',
        ]);

        $oldName = $bookType->name;
        $bookType->update($validated);

        if ($oldName !== $validated['name']) {
            Transaction::where('book_section', $oldName)->update(['book_section' => $validated['name']]);
        }

        $tab = $request->input('tab', 'all');

        return redirect()->route('admin.library.book-types.index', ['tab' => $tab])
            ->with('success', 'Book Type updated successfully.');
    }

    public function destroy(Request $request, BookType $bookType)
    {
        $this->checkMaster();

        $inUse = Transaction::where('book_section', $bookType->name)->exists();
        if ($inUse) {
            return redirect()->back()
                ->with('error', "Cannot delete Book Type '{$bookType->name}' because it is associated with existing transactions. You can set its status to INACTIVE instead.");
        }

        $bookType->delete();

        $tab = $request->input('tab', 'all');

        return redirect()->route('admin.library.book-types.index', ['tab' => $tab])
            ->with('success', "Book Type '{$bookType->name}' deleted successfully.");
    }
}
