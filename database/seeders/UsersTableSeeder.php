<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            // Librarian
            ['Admin Librarian','librarian@school.com','librarian',null],
            // Teachers
            ['Teacher One','teacher1@school.com','teacher',null],
            ['Teacher Two','teacher2@school.com','teacher',null],
            // Students
            ['Student One','student1@school.com','student',7],
            ['Student Two','student2@school.com','student',8],
            ['Student Three','student3@school.com','student',10],
            ['Student Four','student4@school.com','student',12],
        ];

        foreach ($users as $user) {
            User::create([
                'name' => $user[0],
                'email' => $user[1],
                'password' => Hash::make('12345678'), // all passwords same
                'role' => $user[2],
                'grade_level' => $user[3]
            ]);
        }
    }
}
