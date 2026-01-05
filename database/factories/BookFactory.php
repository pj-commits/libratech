<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'book_code' => $this->faker->unique()->isbn13(),
            'title' => $this->faker->sentence(3),
            'author' => $this->faker->name(),
            'description' => $this->faker->paragraph(),
            'grade_level' => $this->faker->numberBetween(1, 12),
            'subject' => $this->faker->word(),
            'competency' => $this->faker->sentence(),
            'type' => $this->faker->randomElement(['Fiction', 'Non-fiction', 'Reference']),
            'file_path' => null,
        ];
    }
}
