<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearningFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'title',
        'description',
        'grade_level',
        'file_path',
    ];

    public function uploader()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}
