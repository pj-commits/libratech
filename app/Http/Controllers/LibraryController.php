<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BorrowLog;
use App\Models\BorrowRequest;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LibraryController extends Controller
{
    public function index()
    {
        $books = Book::all()->map(function ($book) {
            $isBorrowed = BorrowLog::where('book_id', $book->id)
                ->whereNull('returned_at')
                ->exists();

            $book->is_available = !$isBorrowed;

             if (Auth::check()) {
                $userId = Auth::id();
                
                // Check if current user has the book
                $book->current_user_has_book = BorrowLog::where('book_id', $book->id)
                    ->where('user_id', $userId)
                    ->whereNull('returned_at')
                    ->exists();

                $req = BorrowRequest::where('book_id', $book->id)
                    ->where('user_id', $userId)
                    ->latest()
                    ->first();

                $book->current_user_request_status = $req ? $req->borrow_status : null;
            } else {
                $book->current_user_request_status = null;
                $book->current_user_has_book = false;
            }
            return $book;
        });

        return Inertia::render('Library/LibrayIndex', compact('books'));
    }

    public function create()
    {
        return Inertia::render('Library/LibraryCreate');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:50',
            'author' => 'required|string|max:50',
            'subject' => 'required|string|max:50',
            'description' => 'nullable|string|max:255',
            'grade_level' => 'required|integer',
            'competency' => 'nullable|string|max:255',
            'file_path' => 'nullable|string|max:255',
        ]);
        
        $data['title'] = \Illuminate\Support\Str::title($data['title']);
        $data['author'] = \Illuminate\Support\Str::title($data['author']);
        $data['subject'] = strtoupper($data['subject']);
        $data['type'] = 'physical'; // Default to physical

        $data['book_code'] = Book::generateBookCode(
            $data['grade_level'],
            $data['subject']
        );
        Book::create($data);
        // Removed flash message here as user requested (or rather, the frontend won't display it, but good to keep "message" for toast if needed, but standardizing)
        return redirect()->route('library.index');
    }

    public function edit(Book $book)
    {
        return Inertia::render('Library/LibraryEdit', compact('book'));
    }

    public function update(Request $request, Book $book)
    {
        $request->validate([
            'title' => 'required|string',
            'author' => 'required|string',
            'description' => 'nullable|string',
            'grade_level' => 'required|integer',
            'competency' => 'nullable|string',
            'type' => 'required|in:pdf,link,physical',
            'file_path' => 'nullable|string',
        ]);

        $book->update($request->all());

        return redirect()->route('library.index');
    }

    public function show(Book $book)
    {
        $isBorrowed = BorrowLog::where('book_id', $book->id)
            ->whereNull('returned_at')
            ->exists();
        
        $book->is_available = !$isBorrowed;
        
        $book->is_available = !$isBorrowed;
        
        if (Auth::check()) {
            $userId = Auth::id();

            $book->current_user_has_book = BorrowLog::where('book_id', $book->id)
                ->where('user_id', $userId)
                ->whereNull('returned_at')
                ->exists();

            $req = BorrowRequest::where('book_id', $book->id)
                ->where('user_id', $userId)
                ->latest()
                ->first();
            $book->current_user_request_status = $req ? $req->borrow_status : null;
        } else {
            $book->current_user_request_status = null;
            $book->current_user_has_book = false;
        }

        $borrowHistory = BorrowLog::with('user')
            ->where('book_id', $book->id)
            ->latest('borrowed_at')
            ->get();

        return Inertia::render('Library/LibraryDetails', [
            'book' => $book,
            'borrowHistory' => $borrowHistory,
        ]);
    }

    public function destroy(Book $book)
    {
        $book->delete();
        return redirect()->route('library.index');
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:books,id',
        ]);

        Book::whereIn('id', $request->ids)->delete();

        return redirect()->route('library.index')->with('message', count($request->ids) . ' books deleted successfully.');
    }

    public function import(Request $request)
    {
        // Relaxed validation for CSV
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx,xls|max:2048',
        ]);

        $file = $request->file('file');
        
        // Use mapping to ensure we read it correctly.
        // We will just read lines to avoid mime issues if possible, but fgetcsv is fine.
        $handle = fopen($file->getPathname(), 'r');
        $header = fgetcsv($handle); // Skip header

        $count = 0;

        while (($row = fgetcsv($handle)) !== false) {
            // Expected Format: title, author, grade_level, description, quantity, subject
            // Index: 0, 1, 2, 3, 4, 5
            
            if (count($row) < 3) continue;

            $title = trim($row[0]);
            $author = trim($row[1]);
            $gradeRaw = trim($row[2]);

            // Strict Validation: Required fields
            if (empty($title) || empty($author) || empty($gradeRaw)) {
                continue; // Skip invalid row
            }

            $grade = (int)$gradeRaw;
            $desc = isset($row[3]) ? trim($row[3]) : null;
            $qtyRaw = isset($row[4]) ? (int)$row[4] : 1;
            $qty = $qtyRaw > 0 ? $qtyRaw : 1;
            
            $subjectRaw = isset($row[5]) ? trim($row[5]) : '';
            $subject = !empty($subjectRaw) ? strtoupper($subjectRaw) : 'GENERAL';

            $titleFormatted = \Illuminate\Support\Str::title($title);
            $authorFormatted = \Illuminate\Support\Str::title($author);

            for ($i = 0; $i < $qty; $i++) {
                Book::create([
                    'title' => $titleFormatted,
                    'author' => $authorFormatted,
                    'grade_level' => $grade,
                    'type' => 'physical',
                    'description' => $desc,
                    'subject' => $subject,
                    'book_code' => Book::generateBookCode($grade, $subject),
                    'is_available' => true,
                ]);
            }
            $count += $qty;
        }

        fclose($handle);

        return redirect()->route('library.index');
    }
}
