<?php

namespace Database\Seeders;

use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some users and mentors
        $users = User::where('role', 'user')->limit(10)->get();
        $mentors = User::where('role', 'mentor')->limit(5)->get();

        if ($users->count() > 0 && $mentors->count() > 0) {
            // Create mentor reviews
            foreach ($mentors as $mentor) {
                // Create 3-8 reviews for each mentor
                $reviewCount = rand(3, 8);
                
                for ($i = 0; $i < $reviewCount; $i++) {
                    $randomUser = $users->random();
                    
                    Review::create([
                        'user_id' => $randomUser->id,
                        'mentor_id' => $mentor->id,
                        'course_id' => null,
                        'rating' => rand(3, 5), // Mostly positive reviews
                        'comment' => $this->getRandomComment(),
                    ]);
                }
            }

            // Create some additional random reviews
            Review::factory()->count(20)->mentorReview()->create();
            
            echo "✅ Created reviews for mentors\n";
        } else {
            echo "⚠️ No users or mentors found. Please run UserSeeder first.\n";
        }
    }

    /**
     * Get a random review comment.
     */
    private function getRandomComment(): string
    {
        $comments = [
            "Excellent mentor! Very knowledgeable and patient. Highly recommended.",
            "Great experience learning with this mentor. Clear explanations and good examples.",
            "Professional and helpful. Made complex topics easy to understand.",
            "Outstanding teaching skills. Would definitely book another session.",
            "Very experienced and provided valuable insights. Thank you!",
            "Good mentor but could improve on time management.",
            "Fantastic session! Learned a lot and got practical tips.",
            "Knowledgeable mentor with real-world experience. Very helpful.",
            "Patient and understanding. Great at explaining difficult concepts.",
            "Excellent communication skills and very supportive.",
            "Professional approach and well-prepared sessions.",
            "Helpful mentor who goes above and beyond to ensure understanding.",
            "Great personality and makes learning enjoyable.",
            "Very responsive and provides detailed feedback.",
            "Practical approach to teaching with real examples.",
        ];

        return $comments[array_rand($comments)];
    }
}
