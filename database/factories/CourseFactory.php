<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\User;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Course>
 */
class CourseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Course::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $courseTopics = [
            'Introduction to Web Development',
            'Advanced JavaScript Fundamentals',
            'React.js for Beginners',
            'Python Data Science Bootcamp',
            'Digital Marketing Mastery',
            'Graphic Design Principles',
            'Mobile App Development with Flutter',
            'Machine Learning Basics',
            'Photography for Beginners',
            'Business Strategy and Planning',
            'UI/UX Design Workshop',
            'Content Writing Masterclass',
            'Social Media Marketing',
            'Video Editing with Adobe Premiere',
            'Excel for Data Analysis',
        ];

        return [
            'mentor_id' => User::where('role', 'mentor')->inRandomOrder()->first()?->id 
                        ?? User::factory()->create(['role' => 'mentor'])->id,
            'category_id' => Category::inRandomOrder()->first()?->id 
                           ?? 1, // fallback to category ID 1
            'title' => $this->faker->randomElement($courseTopics),
            'description' => $this->faker->paragraphs(3, true),
            'thumbnail' => null, // Will be set when file upload is implemented
            'cover_photo' => null, // Will be set when file upload is implemented
            'price' => $this->faker->randomFloat(2, 29.99, 299.99),
            'discount' => $this->faker->boolean(30) ? $this->faker->randomFloat(2, 5, 50) : 0, // 30% chance of discount
            'duration_days' => $this->faker->randomElement([7, 14, 30, 60, 90]),
            'status' => $this->faker->randomElement(['pending', 'approved', 'rejected']),
            'rejection_reason' => null,
            'needs_reapproval' => false,
        ];
    }

    /**
     * Create an approved course.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
            'rejection_reason' => null,
            'needs_reapproval' => false,
        ]);
    }

    /**
     * Create a pending course.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'rejection_reason' => null,
            'needs_reapproval' => false,
        ]);
    }

    /**
     * Create a rejected course.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
            'rejection_reason' => $this->faker->sentence(),
            'needs_reapproval' => false,
        ]);
    }

    /**
     * Create a course that needs reapproval.
     */
    public function needsReapproval(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
            'needs_reapproval' => true,
        ]);
    }

    /**
     * Create a free course.
     */
    public function free(): static
    {
        return $this->state(fn (array $attributes) => [
            'price' => 0,
            'discount' => 0,
        ]);
    }

    /**
     * Create a premium course.
     */
    public function premium(): static
    {
        return $this->state(fn (array $attributes) => [
            'price' => $this->faker->randomFloat(2, 199.99, 599.99),
        ]);
    }
}
