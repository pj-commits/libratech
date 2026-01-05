<?php

namespace Database\Seeders;

use App\Models\BorrowLog;
use App\Models\BorrowRequest;
use Illuminate\Database\Seeder;

class BorrowLogsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $logs = [
            // user_id, book_id, borrowed_at, returned_at, expected_return_date, request_status
            [5, 1, now()->subDays(10), null, now()->subDays(2), 'approved'], // Overdue (Eren - Bio)
            [6, 6, now()->subDays(1), null, now()->addDays(5), 'approved'],  // Active (Mikasa - Physics)
            [2, 27, now()->subDays(2), null, now()->addDays(12), 'approved'], // Active (Levi - Calculus)
            [7, 2, now()->subDays(10), now(), now()->subDays(3), 'returned'], // Returned (Armin - Earth Sci)

            [9, 22, now()->subDays(1), null, now()->addDays(3), 'approved'], // Active (Maria LT - Pre-Calc)
            [10, 29, now()->subDays(2), null, now()->addDays(5), 'approved'], // Active (Maria T - Mod Hist)
            [10, 26, now()->subDays(14), now(), now()->subDays(7), 'returned'], // Returned (Maria T - Chem II)
        ];

        foreach ($logs as $log) {
            BorrowLog::create([
                'user_id' => $log[0],
                'book_id' => $log[1],
                'borrowed_at' => $log[2],
                'returned_at' => $log[3],
            ]);

            BorrowRequest::create([
                'user_id' => $log[0],
                'book_id' => $log[1],
                'borrow_status' => $log[5],
                'expected_return_date' => $log[4],
                'approved_by' => 1,
                'created_at' => $log[2],
                'updated_at' => $log[3] ? $log[3] : $log[2],
            ]);
        }
    }
}
