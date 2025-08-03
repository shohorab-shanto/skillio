<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\User;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get mentors, categories, and subcategories
        $mentors = User::where('role', 'mentor')->limit(10)->get();
        $categories = Category::limit(5)->get();
        $subCategories = SubCategory::limit(10)->get();

        if ($mentors->count() > 0 && $categories->count() > 0 && $subCategories->count() > 0) {
            
            // Create courses for each mentor
            foreach ($mentors as $mentor) {
                $courseCount = rand(2, 5); // Each mentor creates 2-5 courses
                
                for ($i = 0; $i < $courseCount; $i++) {
                    $randomCategory = $categories->random();
                    
                    $course = Course::create([
                        'mentor_id' => $mentor->id,
                        'category_id' => $randomCategory->id,
                        'title' => $this->getCourseTitle(),
                        'description' => $this->getCourseDescription(),
                        'price' => rand(2999, 19999) / 100, // $29.99 to $199.99
                        'discount' => rand(0, 50), // 0% to 50% discount
                        'duration_days' => collect([7, 14, 30, 60, 90])->random(),
                        'status' => $this->getRandomStatus(), // Get weighted random status
                        'rejection_reason' => null,
                        'needs_reapproval' => rand(0, 100) < 10, // 10% chance of needing reapproval
                    ]);

                    // Attach 1-3 random sub-categories to each course
                    $randomSubCategories = $subCategories->random(rand(1, 3));
                    $course->subCategories()->attach($randomSubCategories->pluck('id'));
                }
            }

            // Create additional courses using factory and attach sub-categories
            $factoryCourses = collect([
                Course::factory()->count(20)->approved()->create(),
                Course::factory()->count(10)->pending()->create(),
                Course::factory()->count(5)->rejected()->create(),
                Course::factory()->count(3)->needsReapproval()->create()
            ])->flatten();

            // Attach sub-categories to factory-created courses
            foreach ($factoryCourses as $course) {
                $randomSubCategories = $subCategories->random(rand(1, 3));
                $course->subCategories()->attach($randomSubCategories->pluck('id'));
            }
            
            echo "✅ Created courses for mentors with sub-category relationships\n";
        } else {
            echo "⚠️ No mentors, categories, or subcategories found. Please run other seeders first.\n";
        }
    }

    /**
     * Get a random course title.
     */
    private function getCourseTitle(): string
    {
        $titles = [
            'Complete Web Development Bootcamp',
            'JavaScript Fundamentals for Beginners',
            'React.js: Build Modern Web Applications',
            'Python for Data Science',
            'Digital Marketing Strategy',
            'Graphic Design Masterclass',
            'Mobile App Development',
            'Machine Learning with Python',
            'Photography Basics',
            'Business Development Fundamentals',
            'UI/UX Design Complete Course',
            'Content Writing Workshop',
            'Social Media Marketing',
            'Video Production and Editing',
            'Excel Data Analysis',
            'WordPress Website Creation',
            'E-commerce Business Setup',
            'Personal Branding Strategy',
            'Project Management Essentials',
            'Public Speaking Mastery',
        ];

        return $titles[array_rand($titles)];
    }

    /**
     * Get a random course description.
     */
    private function getCourseDescription(): string
    {
        return "This comprehensive course will take you from beginner to advanced level. You'll learn practical skills through hands-on projects and real-world examples. By the end of this course, you'll have the confidence and knowledge to apply what you've learned in your professional or personal projects. The course includes video lectures, downloadable resources, quizzes, and lifetime access to all materials.";
    }

    /**
     * Get a weighted random status (70% approved, 20% pending, 10% rejected).
     */
    private function getRandomStatus(): string
    {
        $rand = rand(1, 100);
        
        if ($rand <= 70) {
            return 'approved';
        } elseif ($rand <= 90) {
            return 'pending';
        } else {
            return 'rejected';
        }
    }
}
