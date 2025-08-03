<?php

namespace Database\Factories;

use App\Models\Review;
use App\Models\User;
use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Review>
 */
class ReviewFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Review::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::where('role', 'user')->inRandomOrder()->first()?->id ?? User::factory(),
            'mentor_id' => User::where('role', 'mentor')->inRandomOrder()->first()?->id ?? User::factory(),
            'course_id' => null,
            'rating' => $this->faker->numberBetween(1, 5),
            'comment' => $this->faker->paragraph(),
        ];
    }

    /**
     * Create a mentor review.
     */
    public function mentorReview(): static
    {
        return $this->state(fn (array $attributes) => [
            'course_id' => null,
        ]);
    }

    /**
     * Create a course review.
     */
    public function courseReview(): static
    {
        return $this->state(fn (array $attributes) => [
            'mentor_id' => null,
            'course_id' => Course::factory(),
        ]);
    }

    /**
     * Create a high rating review.
     */
    public function highRating(): static
    {
        return $this->state(fn (array $attributes) => [
            'rating' => $this->faker->numberBetween(4, 5),
        ]);
    }

    /**
     * Create a low rating review.
     */
    public function lowRating(): static
    {
        return $this->state(fn (array $attributes) => [
            'rating' => $this->faker->numberBetween(1, 2),
        ]);
    }
}
