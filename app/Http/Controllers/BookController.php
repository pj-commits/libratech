<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Inertia\Inertia;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index()
    {
        $books = Book::all(); // for simplicity
        return Inertia::render('Library/Index', compact('books'));
    }

    public function create()
    {
        return Inertia::render('Library/Create');
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
            'type' => 'required|in:type,pdf,physical',
            'file_path' => 'nullable|string|max:255',
        ]);
        $data['subject'] = strtoupper($data['subject']);

        $data['book_code'] = Book::generateBookCode(
            $data['grade_level'],
            $data['subject']
        );
        Book::create($data);
        return redirect()->route('library.index')->with('message', 'Book Added Successfully');
    }

    public function edit(Book $book)
    {
        return Inertia::render('Library/Edit', compact('book'));
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
        return Inertia::render('Library/Show', [
            'book' => $book,
        ]);
    }

    public function destroy(Book $book)
    {
        $book->delete();
        return redirect()->route('library.index');
    }
}
