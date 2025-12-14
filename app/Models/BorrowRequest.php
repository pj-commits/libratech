<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BorrowRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'borrow_log_id', 'borrow_status', 'approved_by'
    ];

    public function borrowLog()
    {
        return $this->belongsTo(BorrowLog::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
