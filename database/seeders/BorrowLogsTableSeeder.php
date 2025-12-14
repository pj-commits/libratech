<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\BorrowLog;
use App\Models\BorrowRequest;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class BorrowLogsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $borrowLogs = [
            [4, 1], // Student One borrows Book 1
            [5, 2], // Student Two borrows Book 2
            [6, 3], // Student Three borrows Book 3
        ];

        foreach ($borrowLogs as $entry) {
            BorrowLog::create([
                'user_id' => $entry[0],
                'book_id' => $entry[1],
                'borrowed_at' => now(),
                'returned_at' => null,
            ]);
        }
    }
}
