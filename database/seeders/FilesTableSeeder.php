<?php

namespace Database\Seeders;

use App\Models\File;
use Illuminate\Database\Seeder;

class FilesTableSeeder extends Seeder
{
    public function run(): void
    {
        $files = [
            [2, 'Science Notes', 7, 'storage/files/science_notes.pdf'],   // Teacher One
            [3, 'English Assignment', 8, 'storage/files/english_assignment.pdf'], // Teacher Two
            [2, 'Math Exercises', 10, 'storage/files/math_exercises.pdf'], // Teacher One
        ];

        foreach ($files as $file) {
            File::create([
                'teacher_id' => $file[0],
                'title' => $file[1],
                'grade_level' => $file[2],
                'file_path' => $file[3],
            ]);
        }
    }
}
