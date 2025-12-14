<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class BooksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $books = [
            ['07SCI00001','General Biology','Jane Doe','SCIENCE',7],
            ['07SCI00002','Earth Science','Alfred Wegener','SCIENCE',7],
            ['07MAT00001','Algebra Basics','John Smith','MATH',7],
            ['07ENG00001','English Grammar','Mary Johnson','ENGLISH',7],
            ['07HIS00001','Philippine History','Carlos Reyes','HISTORY',7],

            ['08SCI00001','Physics I','Albert Newton','SCIENCE',8],
            ['08MAT00001','Geometry','Euclid','MATH',8],
            ['08ENG00001','Reading Skills','Emily Bronte','ENGLISH',8],
            ['08HIS00001','Asian History','Sun Tzu','HISTORY',8],
            ['08SCI00002','Chemistry Lab','Robert Boyle','SCIENCE',8],

            ['09SCI00001','Chemistry Intro','Marie Curie','SCIENCE',9],
            ['09MAT00001','Algebra II','Euler','MATH',9],
            ['09ENG00001','Literature','Shakespeare','ENGLISH',9],
            ['09HIS00001','World History','Howard Zinn','HISTORY',9],
            ['09MAT00002','Geometry II','Euclid','MATH',9],

            ['10SCI00001','Biology II','Mendel','SCIENCE',10],
            ['10MAT00001','Trigonometry','Hipparchus','MATH',10],
            ['10ENG00001','Advanced Grammar','Austen','ENGLISH',10],
            ['10HIS00001','PH History II','Rizal','HISTORY',10],
            ['10SCI00002','Environmental Sci','Carson','SCIENCE',10],

            ['11SCI00001','Physics II','Newton','SCIENCE',11],
            ['11MAT00001','Pre-Calculus','Gauss','MATH',11],
            ['11ENG00001','Composition','Hemingway','ENGLISH',11],
            ['11HIS00001','World Civ','Toynbee','HISTORY',11],
            ['11ENG00002','Creative Writing','Angelou','ENGLISH',11],

            ['12SCI00001','Chemistry II','Mendeleev','SCIENCE',12],
            ['12MAT00001','Calculus','Newton','MATH',12],
            ['12ENG00001','Research Writing','Locke','ENGLISH',12],
            ['12HIS00001','Modern History','Orwell','HISTORY',12],
            ['12MAT00002','Statistics','Fisher','MATH',12],
        ];

        foreach ($books as $b) {
            Book::create([
                'book_code'   => $b[0],
                'title'       => $b[1],
                'author'      => $b[2],
                'subject'     => $b[3],
                'grade_level' => $b[4],
                'description' => 'Seeded content',
                'competency'  => 'Seed competency',
                'type'        => 'pdf',
                'file_path'   => null,
            ]);
        }
    }
}
