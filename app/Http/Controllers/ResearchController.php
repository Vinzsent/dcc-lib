<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Research;
use App\Models\Shelf;
use Carbon\Carbon;

class ResearchController extends Controller
{
    // ----- RESEARCH CRUD -----
    public function index(Request $request)
    {
        $location = session('location');
        $isElem = $location && str_starts_with($location, 'DCC BED');

        if ($isElem) {
            return $this->indexElem($request);
        }

        $campuses = $this->getCampusFilter();
        $query = Research::query();
        $shelvesQuery = Shelf::orderBy('shelf_number');

        if ($campuses !== null) {
            $query->whereIn('campus', $campuses);
            $shelvesQuery->whereIn('campus', $campuses);
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

        $research = $query->paginate(10);
        $shelves = $shelvesQuery->get();

        return view('admin.library.research', compact('research', 'shelves'));
    }

    /**
     * Research index for DCC BED Elementary (uses research table directly).
     */
    private function indexElem(Request $request)
    {
        $query = Research::query();

        // Global Search
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('accession_no', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%")
                  ->orWhere('call_number', 'like', "%{$search}%");
            });
        }

        // Column-specific Filters
        if ($request->filled('accession_no')) {
            $query->where('accession_no', 'like', "%{$request->accession_no}%");
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

        // Sorting
        $sort = $request->input('sort', 'title');
        $direction = $request->input('direction', 'asc');
        $allowedSorts = ['accession_no', 'title', 'author', 'call_number'];
        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->orderBy('title', 'asc');
        }

        $research = $query->paginate(10);
        $shelves = collect();
        return view('admin.library.research_elem', compact('research', 'shelves'));
    }

    public function store(Request $request)
    {
        $location = session('location');

        if ($location && str_starts_with($location, 'DCC BED')) {
            $request->validate([
                'accession_no' => 'required|string|unique:researches,accession_no',
                'title'        => 'required|string',
                'author'       => 'required|string',
                'call_number'  => 'required|string',
            ]);

            Research::create([
                'accession_no' => $request->accession_no,
                'title'        => $request->title,
                'author'       => $request->author,
                'call_number'  => $request->call_number,
                'status'       => 'Available',
            ]);

            return response()->json(['success' => true, 'message' => 'Research added successfully']);
        }

        $rules = [
            'accession_no' => 'required|string|unique:researches,accession_no',
            'barcode' => 'nullable|string|unique:researches,barcode',
            'title' => 'required|string',
            'author' => 'required|string',
            'call_number' => 'required|string',
            'location' => 'nullable|string',
            'shelf_number' => 'nullable|string',
            'status' => 'required|in:Available,Borrowed,available,borrowed'
        ];

        $request->validate($rules);

        Research::create([
            'accession_no' => $request->accession_no,
            'barcode' => $request->barcode,
            'title' => $request->title,
            'author' => $request->author,
            'call_number' => $request->call_number,
            'location' => $request->location,
            'shelf_number' => $request->shelf_number,
            'campus' => 'DCC TED',
            'status' => $request->status ?? 'Available',
        ]);

        return response()->json(['success' => true, 'message' => 'Research added successfully']);
    }

    public function update(Request $request, $accession_no)
    {
        $location = session('location');

        if ($location && str_starts_with($location, 'DCC BED')) {
            $research = Research::findOrFail($accession_no);
            $request->validate([
                'accession_no' => 'required|string|unique:researches,accession_no,' . $accession_no . ',accession_no',
                'title'        => 'required|string',
                'author'       => 'required|string',
                'call_number'  => 'required|string',
            ]);

            $research->update([
                'accession_no' => $request->accession_no,
                'title'        => $request->title,
                'author'       => $request->author,
                'call_number'  => $request->call_number,
            ]);

            return response()->json(['success' => true, 'message' => 'Research updated successfully']);
        }

        $research = Research::findOrFail($accession_no);
        $rules = [
            'accession_no' => 'required|string|unique:researches,accession_no,' . $accession_no . ',accession_no',
            'barcode' => 'nullable|string|unique:researches,barcode,' . $accession_no . ',accession_no',
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

        $research->update($updateData);
        return response()->json(['success' => true, 'message' => 'Research updated successfully']);
    }

    public function destroy($accession_no)
    {
        $location = session('location');

        if ($location && str_starts_with($location, 'DCC BED')) {
            Research::findOrFail($accession_no)->delete();
        } else {
            Research::findOrFail($accession_no)->delete();
        }

        return response()->json(['success' => true, 'message' => 'Research deleted successfully']);
    }

    /**
     * Get allowed campuses for research based on session location.
     * Returns null if no filtering should be applied (Master).
     *
     * @return array|null
     */
    private function getCampusFilter(): ?array
    {
        $location = session('location');
        return match ($location) {
            // DCC TED has full (Master-level) access – no campus restriction
            'DCC TED', 'Master' => null,
            'DCC BED Highschool' => ['DCC BED Highschool'],
            'DCC BED SeniorHighSchool' => ['DCC BED SeniorHighSchool'],
            'DCC BED Elementary' => ['DCC BED Elementary'],
            'DCC BED' => ['DCC BED Highschool', 'DCC BED SeniorHighSchool', 'DCC BED Elementary'],
            default => [], // fallback empty array
        };
    }
}