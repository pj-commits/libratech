<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BorrowLog;
use App\Models\BorrowRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

class BorrowRequestController extends BaseController
{
    use AuthorizesRequests;

    // Student / Teacher
    public function store(Request $request, Book $book)
    {
        $this->authorize('borrow-books');

        $request->validate([
            'expected_return_date' => 'required|date|after:today',
        ]);

        $exists = BorrowRequest::where('user_id', Auth::id())
            ->where('book_id', $book->id)
            ->where('borrow_status', 'pending')
            ->exists();

        if ($exists) {
            return back()->with('error', 'You already have a pending request for this book.');
        }

        BorrowRequest::create([
            'user_id' => Auth::id(),
            'book_id' => $book->id,
            'borrow_status' => 'pending',
            'expected_return_date' => $request->expected_return_date,
        ]);

        return back()->with('message', 'Borrow request sent');
    }

    // Student: cancel request
    public function cancel(BorrowRequest $borrowRequest)
    {
        if ($borrowRequest->user_id !== Auth::id()) {
            abort(403);
        }

        if ($borrowRequest->borrow_status !== 'pending') {
            return back()->with('error', 'Cannot cancel non-pending request');
        }

        $borrowRequest->delete();

        return back()->with('message', 'Request cancelled');
    }

    // Logic to prevent duplicate requests
    public function checkBorrowStatus(Book $book)
    {
        $existing = BorrowRequest::where('user_id', Auth::id())
            ->where('book_id', $book->id)
            ->whereIn('borrow_status', ['pending', 'approved'])
            ->first();

        return response()->json([
            'status' => $existing ? $existing->borrow_status : null,
        ]);
    }

    // Librarian: approve
    public function approve(BorrowRequest $borrowRequest)
    {
        $this->authorize('manage-borrows');

        $borrowRequest->update([
            'borrow_status' => 'approved',
            'approved_by' => Auth::id(),
        ]);

        BorrowLog::create([
            'user_id' => $borrowRequest->user_id,
            'book_id' => $borrowRequest->book_id,
            'borrowed_at' => now(),
        ]);

        return back()->with('message', 'Borrow approved');
    }

    // Librarian: reject
    public function reject(BorrowRequest $borrowRequest)
    {
        $this->authorize('manage-borrows');

        $borrowRequest->update([
            'borrow_status' => 'rejected',
            'approved_by' => Auth::id(),
        ]);

        return back()->with('message', 'Borrow rejected');
    }

    // Librarian: reject with reason
    public function rejectWithReason(Request $request, BorrowRequest $borrowRequest)
    {
        $this->authorize('manage-borrows');

        $request->validate(['reason' => 'required|string']);

        $borrowRequest->update([
            'borrow_status' => 'rejected',
            'approved_by' => Auth::id(),
            'reject_reason' => $request->reason,
        ]);

        return back()->with('message', 'Borrow rejected');
    }

    // Student / Teacher: return
    public function return(Book $book)
    {
        $log = BorrowLog::where('book_id', $book->id)
            ->where('user_id', Auth::id())
            ->whereNull('returned_at')
            ->firstOrFail();

        $log->update([
            'returned_at' => now(),
        ]);

        // Update Request Status
        BorrowRequest::where('user_id', Auth::id())
            ->where('book_id', $book->id)
            ->where('borrow_status', 'approved')
            ->update(['borrow_status' => 'returned']);

        return back()->with('message', 'Book returned');
    }

    public function myRequests()
    {
        $requests = BorrowRequest::with('book')
            ->where('user_id', Auth::id())
            ->whereIn('borrow_status', ['pending', 'rejected', 'approved'])
            ->latest()
            ->get();

        return inertia('Requests/MyRequests', [
            'requests' => $requests,
        ]);
    }

    public function myBooks()
    {
        // "My Books" are those that are currently borrowed (in BorrowLog and not returned)
        $myBooks = BorrowLog::with('book')
            ->where('user_id', Auth::id())
            ->whereNull('returned_at')
            ->latest()
            ->get();

        return inertia('Requests/MyBooks', [
            'books' => $myBooks,
        ]);
    }

    public function manageRequests()
    {
        $this->authorize('manage-borrows');

        $requests = BorrowRequest::with(['book', 'user'])
            ->where('borrow_status', 'pending')
            ->latest()
            ->get();

        return inertia('Requests/ManageRequests', [
            'requests' => $requests,
        ]);
    }

    public function activeBorrows(Request $request)
    {
        $this->authorize('manage-borrows');

        // Query BorrowLog directly to match Dashboard counts
        // Query BorrowLog joined with approved BorrowRequest
        $query = \App\Models\BorrowLog::with(['user', 'book'])
            ->select('borrow_logs.*', 'borrow_requests.expected_return_date')
            ->leftJoin('borrow_requests', function ($join) {
                $join->on('borrow_logs.user_id', '=', 'borrow_requests.user_id')
                    ->on('borrow_logs.book_id', '=', 'borrow_requests.book_id')
                    ->where('borrow_requests.borrow_status', 'approved');
            })
            ->whereNull('borrow_logs.returned_at');

        // Search
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->whereHas('user', function ($u) use ($request) {
                    $u->where('name', 'like', '%'.$request->search.'%');
                })->orWhereHas('book', function ($b) use ($request) {
                    $b->where('title', 'like', '%'.$request->search.'%');
                });
            });
        }

        // Filter
        if ($request->filter === 'overdue') {
            $query->where('borrow_requests.expected_return_date', '<', now()->startOfDay());
        } elseif ($request->filter === 'active') {
            $query->where('borrow_requests.expected_return_date', '>=', now()->startOfDay());
        }

        // Sort
        $sort = $request->input('sort', 'created_at');
        if ($sort === 'created_at') {
            $sort = 'borrowed_at';
        }

        $direction = $request->input('direction', 'desc');

        if ($sort === 'user') {
            $query->join('users', 'borrow_logs.user_id', '=', 'users.id')
                ->orderBy('users.name', $direction);
        } elseif ($sort === 'book') {
            $query->join('books', 'borrow_logs.book_id', '=', 'books.id')
                ->orderBy('books.title', $direction);
        } elseif ($sort === 'due_date') {
            $query->orderBy('borrow_requests.expected_return_date', $direction);
        } else {
            $query->orderBy('borrow_logs.borrowed_at', $direction);
        }

        $activeBorrows = $query->paginate(10)->withQueryString();

        // Transform for Frontend
        $activeBorrows = $query->paginate(10)->withQueryString();

        // Transform for Frontend
        $activeBorrows->getCollection()->transform(function ($log) {
            $log->created_at = $log->borrowed_at; // Map to expected frontend prompt

            return $log;
        });

        return inertia('ActiveBorrows/Index', [
            'borrows' => $activeBorrows,
            'filters' => $request->only(['search', 'filter', 'sort', 'direction']),
        ]);
    }
}
