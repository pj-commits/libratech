<?php

namespace Database\Seeders;

use App\Models\BorrowRequest;
use Illuminate\Database\Seeder;

class BorrowRequestsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $requests = [
            // user_id, book_id, status, expected_return_date, approved_by, reject_reason
            [7, 13, 'pending', now()->addDays(3), null, null], // Armin (G9) wants Literature
            [5, 2, 'pending', now()->addDays(5), null, null],  // Eren (G7) wants Earth Sci
            [8, 1, 'rejected', now()->addDays(7), 1, 'Grade level mismatch.'], // Jean (G10) rejected for Bio
            [6, 27, 'rejected', now()->addDays(7), 1, 'Prerequisites not met.'], // Mikasa (G8) rejected for Calculus

            [9, 25, 'pending', now()->addDays(4), null, null], // Maria LT (G11) wants Creative Writing
            [10, 21, 'rejected', now()->addDays(2), 1, 'Duplicate request.'], // Maria T rejected for Phys II
        ];

        foreach ($requests as $request) {
            BorrowRequest::create([
                'user_id' => $request[0],
                'book_id' => $request[1],
                'borrow_status' => $request[2],
                'expected_return_date' => $request[3],
                'approved_by' => $request[4],
                'reject_reason' => $request[5],
            ]);
        }
    }
}
