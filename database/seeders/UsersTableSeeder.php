<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('12345678');

        // 1. Librarian
        User::create([
            'name' => 'Erwin Smith',
            'email' => 'erwin.smith@librarian.libratech.com',
            'password' => $password,
            'role' => 'librarian',
            'grade_level' => null,
        ]);

        // 2. Teachers
        $teachers = [
            ['Levi Ackerman', 'levi.ackerman@teacher.libratech.com'],
            ['Hange Zoe', 'hange.zoe@teacher.libratech.com'],
            ['Dot Pyxis', 'dot.pyxis@teacher.libratech.com'],
        ];

        foreach ($teachers as $t) {
            User::create([
                'name' => $t[0],
                'email' => $t[1],
                'password' => $password,
                'role' => 'teacher',
                'grade_level' => null,
            ]);
        }

        // 3. Students
        $students = [
            ['Eren Yeager', 'eren.yeager@student.libratech.com', 7],
            ['Mikasa Ackerman', 'mikasa.ackerman@student.libratech.com', 8],
            ['Armin Arlert', 'armin.arlert@student.libratech.com', 9],
            ['Jean Kirstein', 'jean.kirstein@student.libratech.com', 10],
            ['Maria Leonora Teresa Cruz', 'marialt.cruz@student.libratech.com', 11], // Case 1
            ['Maria Teresa Cruz', 'maria.t.cruz@student.libratech.com', 12], // Case 2
        ];

        foreach ($students as $s) {
            User::create([
                'name' => $s[0],
                'email' => $s[1],
                'password' => $password,
                'role' => 'student',
                'grade_level' => $s[2],
            ]);
        }
    }
}
