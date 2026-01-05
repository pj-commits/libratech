<?php

namespace Database\Seeders;

use App\Models\LearningFile;
use Illuminate\Database\Seeder;

class LearningFilesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $files = [
            // Filename, Title, Description, Grade, Uploader_ID
            ['0VUhAQ2O7XBKPmsu4MhDPV7QxoKS2bzSsReOKnvG.pdf', 'Photosynthesis Guide', 'Comprehensive guide to plant energy.', 7, 3], // Hange
            ['3K4tQ7pb5Vo7ZF1dL7IvHig3mwvwyjMUfa1EJf3w.pdf', 'Plate Tectonics', 'Understanding earth crust movements.', 7, 3], // Hange
            ['3mGqPzbHdjOU2Ia0VajBSWHnpUdF8M93gnd4a8u5.pdf', 'Linear Equations', 'Solving for X made easy.', 7, 4], // Pyxis
            ['90MW0ABUniTD42bZH73lp7zPbD3ZsZdqSxqCOHo6.pdf', 'Poetry Analysis', 'How to read and understand poems.', 8, 2], // Levi
            ['D85yFOqD8iNbnX1sa4HK7Xkz0969qYPtVjzPljyt.pdf', 'Newton Laws Quiz', 'Practice problems for physics.', 8, 3], // Hange
            ['E5ecrZTo5QKfrrsD5LNKYpennHGYXRNOOEOg9Izf.pdf', 'World War II Summary', 'Key events of WWII.', 9, 1], // Erwin
            ['EQBeOVkIV1vBWIJmdAkGUF8jzvw7PFmszZmsFFjN.pdf', 'Periodic Table Reference', 'High-res periodic table.', 9, 3], // Hange
            ['jgOeWicf6GIDXxMSM71UWkRhhYRdohbLRrXPjmjB.pdf', 'Romeo and Juliet Notes', 'Themes and character analysis.', 9, 2], // Levi
            ['MEdJpbZASA23hhptG448ctnAQJ8KPfKK1Vx7IpBa.pdf', 'Trigonometry Cheat Sheet', 'SOH CAH TOA explanations.', 10, 4], // Pyxis
            ['n8Y0u1urO5bhucK4gMFx2xECJ99o9oAgQW13mVhR.pdf', 'Cellular Respiration', 'Bio-chemical processes deep dive.', 10, 3], // Hange
            ['oBgcad3KkBXwcjyd0kerJ3HuGe9mhinYtpU8dI8w.pdf', 'El Filibusterismo Context', 'Historical context of Rizal\'s work.', 10, 1], // Erwin
            ['ogx085lzhTSa1BW6g2e7ofcJgT3vPF1278L48gxF.pdf', 'Calculus Limits', 'Introduction to limits.', 11, 4], // Pyxis
            ['PrKHmoXCMY2QCWg4Ezl7jVNPhkdWlDP3HJoD0Hkg.pdf', 'Physics Mechanics', 'Advanced motion problems.', 11, 3], // Hange
            ['Uvi8OCGoSPCBraZJRSiGiNgi93ql6zneQxDXou6w.pdf', 'Research Methods', 'How to write a thesis.', 12, 2], // Levi
            ['ZtVGcEs0s6ozGuEAhkkXsid37oRjDiJcnAjIs6m8.pdf', 'Modern Economics', 'Supply and demand basics.', 12, 1], // Erwin
        ];

        foreach ($files as $f) {
            LearningFile::create([
                'title' => $f[1],
                'description' => $f[2],
                'grade_level' => $f[3],
                'file_path' => 'learning-files/'.$f[0], // Prefix with folder
                'teacher_id' => $f[4],
                'created_at' => now(),
            ]);
        }
    }
}
