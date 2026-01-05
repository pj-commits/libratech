<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\BorrowRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BorrowRequestInteractionTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_create_borrow_request()
    {
        $student = User::factory()->create(['role' => 'student']);
        $book = Book::factory()->create();

        $response = $this->actingAs($student)
            ->post(route('library.borrow', $book->id), [
                'expected_return_date' => now()->addWeek()->format('Y-m-d'),
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('borrow_requests', [
            'user_id' => $student->id,
            'book_id' => $book->id,
            'borrow_status' => 'pending',
        ]);
    }

    public function test_student_can_view_my_requests()
    {
        $student = User::factory()->create(['role' => 'student']);
        $book = Book::factory()->create();
        BorrowRequest::create([
            'user_id' => $student->id,
            'book_id' => $book->id,
            'borrow_status' => 'pending',
            'expected_return_date' => now()->addWeek(),
        ]);

        $response = $this->actingAs($student)->get(route('my-requests'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Requests/MyRequests')
            ->has('requests', 1)
        );
    }

    public function test_librarian_can_approve_request()
    {
        $librarian = User::factory()->create(['role' => 'librarian']);
        $student = User::factory()->create(['role' => 'student']);
        $book = Book::factory()->create();

        $request = BorrowRequest::create([
            'user_id' => $student->id,
            'book_id' => $book->id,
            'borrow_status' => 'pending',
            'expected_return_date' => now()->addWeek(),
        ]);

        $response = $this->actingAs($librarian)
            ->post(route('borrow-requests.approve', $request->id));

        $response->assertRedirect();
        $this->assertDatabaseHas('borrow_requests', [
            'id' => $request->id,
            'borrow_status' => 'approved',
            'approved_by' => $librarian->id,
        ]);

        // Also check that a BorrowLog was created (as per controller logic)
        $this->assertDatabaseHas('borrow_logs', [
            'user_id' => $student->id,
            'book_id' => $book->id,
        ]);
    }

    public function test_librarian_can_reject_request()
    {
        $librarian = User::factory()->create(['role' => 'librarian']);
        $student = User::factory()->create(['role' => 'student']);
        $book = Book::factory()->create();

        $request = BorrowRequest::create([
            'user_id' => $student->id,
            'book_id' => $book->id,
            'borrow_status' => 'pending',
            'expected_return_date' => now()->addWeek(),
        ]);

        $response = $this->actingAs($librarian)
            ->post(route('borrow-requests.reject', $request->id));

        $response->assertRedirect();
        $this->assertDatabaseHas('borrow_requests', [
            'id' => $request->id,
            'borrow_status' => 'rejected',
            'approved_by' => $librarian->id,
        ]);
    }
}
