<?php

namespace Database\Seeders;

use App\Models\Book;
use Illuminate\Database\Seeder;

class BooksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $books = [
            // Grade 7
            ['07SCI00001', 'General Biology', 'Jane Doe', 'SCIENCE', 7, 'Introduction to cellular biology and ecosystems.', 'Understand the basic unit of life and ecological relationships.'],
            ['07SCI00002', 'Earth Science', 'Alfred Wegener', 'SCIENCE', 7, 'Geology, meteorology, and oceanography basics.', 'Analyze the physical makeup of Earth and its atmosphere.'],
            ['07MAT00001', 'Algebra Basics', 'John Smith', 'MATH', 7, 'Fundamental algebraic concepts and linear equations.', 'Solve linear equations and understand variable relationships.'],
            ['07ENG00001', 'English Grammar', 'Mary Johnson', 'ENGLISH', 7, 'Mastering syntax and sentence structure.', 'Apply correct grammatical structures in written communication.'],
            ['07HIS00001', 'Philippine History', 'Carlos Reyes', 'HISTORY', 7, 'Pre-colonial to Spanish era history.', 'Trace the roots of Filipino heritage and colonial impact.'],

            // Grade 8
            ['08SCI00001', 'Physics I', 'Albert Newton', 'SCIENCE', 8, 'Laws of motion and energy conservation.', "Demonstrate understanding of Newton's laws."],
            ['08MAT00001', 'Geometry', 'Euclid', 'MATH', 8, 'Planar geometry, shapes, and proofs.', 'Prove theorems related to triangles and circles.'],
            ['08ENG00001', 'Reading Skills', 'Emily Bronte', 'ENGLISH', 8, 'Advanced reading strategies and comprehension.', 'Analyze complex texts for meaning and inference.'],
            ['08HIS00001', 'Asian History', 'Sun Tzu', 'HISTORY', 8, 'History and culture of Asian nations.', 'Compare and contrast ancient Asian civilizations.'],
            ['08SCI00002', 'Chemistry Lab', 'Robert Boyle', 'SCIENCE', 8, 'Laboratory safety and basic experiments.', 'Perform basic chemical experiments safely.'],

            // Grade 9
            ['09SCI00001', 'Chemistry Intro', 'Marie Curie', 'SCIENCE', 9, 'Atomic structure and periodic table.', 'Explain the properties of elements and compounds.'],
            ['09MAT00001', 'Algebra II', 'Euler', 'MATH', 9, 'Quadratics, functions, and polynomials.', 'Solve quadratic equations and graph functions.'],
            ['09ENG00001', 'Literature', 'Shakespeare', 'ENGLISH', 9, 'Classic literary works and analysis.', 'Interpret themes and literary devices in classic texts.'],
            ['09HIS00001', 'World History', 'Howard Zinn', 'HISTORY', 9, 'Major global events from ancient to modern.', 'Connect historical events to contemporary global issues.'],
            ['09MAT00002', 'Geometry II', 'Euclid', 'MATH', 9, 'Solid geometry and volume calculations.', 'Calculate surface area and volume of 3D shapes.'],

            // Grade 10
            ['10SCI00001', 'Biology II', 'Mendel', 'SCIENCE', 10, 'Genetics, evolution, and heredity.', 'Explain the principles of genetics and evolution.'],
            ['10MAT00001', 'Trigonometry', 'Hipparchus', 'MATH', 10, 'Triangles, circles, and sine/cosine waves.', 'Apply trigonometric ratios to solve real-world problems.'],
            ['10ENG00001', 'Advanced Grammar', 'Austen', 'ENGLISH', 10, 'Complex sentence structures and rhetoric.', 'Construct sophisticated arguments using advanced grammar.'],
            ['10HIS00001', 'PH History II', 'Rizal', 'HISTORY', 10, 'American period to contemporary Philippines.', 'Analyze the impact of foreign occupation on modern society.'],
            ['10SCI00002', 'Environmental Sci', 'Carson', 'SCIENCE', 10, 'Ecosystems, pollution, and conservation.', 'Propose solutions to environmental challenges.'],

            // Grade 11
            ['11SCI00001', 'Physics II', 'Newton', 'SCIENCE', 11, 'Electromagnetism and thermodynamics.', 'Solve complex problems involving electricity and heat.'],
            ['11MAT00001', 'Pre-Calculus', 'Gauss', 'MATH', 11, 'Preparation for calculus: limits and series.', 'Analyze sequences, series, and limits.'],
            ['11ENG00001', 'Composition', 'Hemingway', 'ENGLISH', 11, 'Essay writing and academic discourse.', 'Write clear, cohesive, and persuasive academic essays.'],
            ['11HIS00001', 'World Civ', 'Toynbee', 'HISTORY', 11, 'Development of human civilizations.', 'Trace the rise and fall of major world civilizations.'],
            ['11ENG00002', 'Creative Writing', 'Angelou', 'ENGLISH', 11, 'Poetry, fiction, and creative non-fiction.', 'Produce original creative works in various genres.'],

            // Grade 12
            ['12SCI00001', 'Chemistry II', 'Mendeleev', 'SCIENCE', 12, 'Organic chemistry and biochemistry.', 'Identify organic compounds and their reactions.'],
            ['12MAT00001', 'Calculus', 'Newton', 'MATH', 12, 'Derivatives, integrals, and applications.', 'Apply calculus concepts to rates of change and accumulation.'],
            ['12ENG00001', 'Research Writing', 'Locke', 'ENGLISH', 12, 'Thesis writing and research methodologies.', 'Conduct and present independent research.'],
            ['12HIS00001', 'Modern History', 'Orwell', 'HISTORY', 12, '20th century conflicts and globalization.', 'Evaluate the causes and effects of modern global conflicts.'],
            ['12MAT00002', 'Statistics', 'Fisher', 'MATH', 12, 'Data analysis, probability, and inference.', 'Interpret data sets and draw statistical conclusions.'],
        ];

        foreach ($books as $b) {
            Book::create([
                'book_code' => $b[0],
                'title' => $b[1],
                'author' => $b[2],
                'subject' => $b[3],
                'grade_level' => $b[4],
                'description' => $b[5],
                'competency' => $b[6],
                'type' => 'pdf',
                'file_path' => null,
            ]);
        }
    }
}
