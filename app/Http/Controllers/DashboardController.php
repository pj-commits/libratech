<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BorrowRequest;
use App\Models\LearningFile;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $role = $user->role;

        $stats = [];
        $recentActivity = [];

        if ($role === 'librarian') {
            $stats = [
                'total_books' => Book::count(),
                'pending_requests' => BorrowRequest::where('borrow_status', 'pending')->count(),
                'active_borrows' => \App\Models\BorrowLog::whereNull('returned_at')->count(),
                'overdue_books' => \App\Models\BorrowLog::whereNull('returned_at')
                    ->leftJoin('borrow_requests', function ($join) {
                        $join->on('borrow_logs.user_id', '=', 'borrow_requests.user_id')
                            ->on('borrow_logs.book_id', '=', 'borrow_requests.book_id')
                            ->where('borrow_requests.borrow_status', 'approved');
                    })
                    ->where('borrow_requests.expected_return_date', '<', now()->startOfDay())
                    ->count(),
            ];

            $recentActivity = BorrowRequest::with(['user', 'book'])
                ->latest()
                ->take(5)
                ->get()
                ->map(function ($req) {
                    return [
                        'id' => $req->id,
                        'user' => $req->user->name,
                        'book' => $req->book->title,
                        'status' => $req->borrow_status,
                        'date' => $req->created_at->diffForHumans(),
                    ];
                });

        } elseif ($role === 'student') {
            $stats = [
                'total_books' => Book::count(),
                'my_active_books' => \App\Models\BorrowLog::where('user_id', $user->id)
                    ->whereNull('returned_at')
                    ->count(),
                'my_pending_requests' => BorrowRequest::where('user_id', $user->id)
                    ->where('borrow_status', 'pending')
                    ->count(),
                // Count learning files for student's grade or all? Defaulting to all for now or creating logic
                'learning_files' => LearningFile::where('grade_level', $user->grade_level)->count(),
            ];

            $recentActivity = BorrowRequest::with('book')
                ->where('user_id', $user->id)
                ->latest()
                ->take(5)
                ->get()
                ->map(function ($req) {
                    return [
                        'id' => $req->id,
                        'book' => $req->book->title,
                        'status' => $req->borrow_status,
                        'date' => $req->created_at->diffForHumans(),
                    ];
                });

        } elseif ($role === 'teacher') {
            $stats = [
                'total_books' => Book::count(),
                'my_pending_requests' => BorrowRequest::where('user_id', $user->id)
                    ->where('borrow_status', 'pending')
                    ->count(),
                'uploaded_files' => LearningFile::where('teacher_id', $user->id)->count(),
            ];

            $recentActivity = LearningFile::where('teacher_id', $user->id)
                ->latest()
                ->take(5)
                ->get()
                ->map(function ($file) {
                    return [
                        'id' => $file->id,
                        'title' => $file->title,
                        'grade' => $file->grade_level,
                        'type' => 'upload', // Add type marker
                        'date' => $file->created_at->diffForHumans(),
                    ];
                });
        }

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'recentActivity' => $recentActivity,
            'role' => $role,
        ]);
    }
}
