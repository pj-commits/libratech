<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'author',
        'subject',
        'description',
        'grade_level',
        'competency',
        'type',
        'file_path',
        'book_code',
    ];

    public function borrowLogs()
    {
        return $this->hasMany(BorrowLog::class);
    }

    public static function generateBookCode(int $grade, string $subject): string
    {
        $gradePart = str_pad($grade, 2, '0', STR_PAD_LEFT); // 07
        $subjectPart = strtoupper(substr($subject, 0, 3)); // SCI

        $lastCode = self::where('grade_level', $grade)
            ->where('subject', strtoupper($subject))
            ->where('book_code', 'like', "{$gradePart}{$subjectPart}%")
            ->max('book_code');

        $next = $lastCode
            ? ((int) substr($lastCode, -5)) + 1
            : 1;

        return $gradePart
            .$subjectPart
            .str_pad($next, 5, '0');
    }
}
