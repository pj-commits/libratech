<?php

namespace Database\Seeders;

use App\Models\BorrowLog;
use App\Models\BorrowRequest;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class BorrowRequestsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $borrowLogs = BorrowLog::all();

        $statuses = ['approved', 'pending', 'rejected'];

        foreach ($borrowLogs as $index => $borrowLog) {
            BorrowRequest::create([
                'borrow_log_id' => $borrowLog->id,
                'borrow_status' => $statuses[$index] ?? 'pending',
                'approved_by' => $statuses[$index] === 'approved' ? 1 : null,
            ]);
        }
    }
}
