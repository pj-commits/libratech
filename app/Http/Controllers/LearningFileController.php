<?php

namespace App\Http\Controllers;

use App\Models\LearningFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class LearningFileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $query = LearningFile::with('uploader');

        if ($user->role === 'student') {
            // Students: ONLY files matching their exact grade level
            $query->where('grade_level', $user->grade_level);
        }
        // Teachers & Librarians: View all (no filter)

        $files = $query->latest()->get()->map(function ($file) {
            return [
                'id' => $file->id,
                'title' => $file->title,
                'description' => $file->description,
                'grade_level' => $file->grade_level,
                'uploader' => $file->uploader ? $file->uploader->name : 'Unknown',
                'created_at' => $file->created_at->format('Y-m-d'),
                'file_path' => Storage::url($file->file_path),
                'can_delete' => Auth::user()->can('delete-learning-files') || (Auth::user()->id === $file->teacher_id), // Librarian or Owner
            ];
        });

        return Inertia::render('LearningFiles/LearningIndex', [
            'files' => $files
        ]);
    }

    public function create()
    {
        return Inertia::render('LearningFiles/LearningCreate');
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        
        // Validation
        $request->validate([
            'title' => 'required|string|max:255',
            'grade_level' => 'required|integer|min:7|max:12',
            'file' => 'required|file|mimes:pdf,docx,doc,pptx,ppt,txt,jpg,png|max:51200', // 50MB key requirement
            'description' => 'nullable|string|max:1000',
        ]);

        $path = $request->file('file')->store('learning-files', 'public');

        // Check for duplicate title and append counter if exists
        $title = $request->title;
        $count = 1;
        while (LearningFile::where('title', $title)->exists()) {
             $title = $request->title . " ({$count})";
             $count++;
        }

        LearningFile::create([
            'teacher_id' => $user->id,
            'title' => $title,
            'description' => $request->description,
            'grade_level' => $request->grade_level,
            'file_path' => $path,
        ]);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'File uploaded successfully'], 201);
        }

        return redirect()->route('learning-files.index')->with('message', 'File uploaded successfully');
    }

    public function download(LearningFile $learningFile)
    {
        // Permission check: Viewable by all authenticated (implied by route middleware)
        // Check grade level for students? Requirement "Students see files intended for their grade level".
        // Assuming if they can see the button, they can download. strict check:
        $user = Auth::user();
        if ($user->role === 'student' && $user->grade_level !== $learningFile->grade_level) {
            abort(403);
        }

        if (!$learningFile->file_path || !Storage::disk('public')->exists($learningFile->file_path)) {
            abort(404);
        }

        return Storage::disk('public')->download($learningFile->file_path, $learningFile->title . '.' . pathinfo($learningFile->file_path, PATHINFO_EXTENSION));
    }

    public function update(Request $request, LearningFile $learningFile)
    {
        // Permission check: Librarian OR Owner (Teacher)
        if (Auth::user()->role !== 'librarian' && Auth::id() !== $learningFile->teacher_id) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'grade_level' => 'required|integer|min:7|max:12',
        ]);

        $learningFile->update($request->only(['title', 'description', 'grade_level']));

        return redirect()->back()->with('message', 'File details updated successfully');
    }

    public function destroy(LearningFile $learningFile)
    {
        // Permission check: Librarian OR Owner (Teacher)
        if (Auth::user()->role !== 'librarian' && Auth::id() !== $learningFile->teacher_id) {
            abort(403);
        }

        // Delete file from storage
        if ($learningFile->file_path && Storage::disk('public')->exists($learningFile->file_path)) {
            Storage::disk('public')->delete($learningFile->file_path);
        }

        $learningFile->delete();

        return redirect()->route('learning-files.index')->with('message', 'File deleted successfully');
    }
}
