<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Mentor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create regular users
        $users = [
            [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => Hash::make('password'),
                'phone' => '+1234567890',
                'address' => '123 Main St, City, Country',
                'role' => 'user',
                'status' => 'active',
                'gdpr_consent' => true,
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'password' => Hash::make('password'),
                'phone' => '+1234567891',
                'address' => '456 Oak Ave, City, Country',
                'role' => 'user',
                'status' => 'active',
                'gdpr_consent' => true,
            ],
            [
                'name' => 'Mike Johnson',
                'email' => 'mike@example.com',
                'password' => Hash::make('password'),
                'phone' => '+1234567892',
                'address' => '789 Pine St, City, Country',
                'role' => 'user',
                'status' => 'active',
                'gdpr_consent' => true,
            ],
        ];

        foreach ($users as $userData) {
            User::create($userData);
        }

        // Create mentor users
        $mentors = [
            [
                'name' => 'Dr. Sarah Wilson',
                'email' => 'sarah@example.com',
                'password' => Hash::make('password'),
                'phone' => '+1234567893',
                'address' => '321 University Ave, City, Country',
                'role' => 'mentor',
                'status' => 'active',
                'gdpr_consent' => true,
            ],
            [
                'name' => 'Prof. David Brown',
                'email' => 'david@example.com',
                'password' => Hash::make('password'),
                'phone' => '+1234567894',
                'address' => '654 Education Blvd, City, Country',
                'role' => 'mentor',
                'status' => 'active',
                'gdpr_consent' => true,
            ],
            [
                'name' => 'Lisa Chen',
                'email' => 'lisa@example.com',
                'password' => Hash::make('password'),
                'phone' => '+1234567895',
                'address' => '987 Tech Plaza, City, Country',
                'role' => 'mentor',
                'status' => 'active',
                'gdpr_consent' => true,
            ],
            [
                'name' => 'Robert Taylor',
                'email' => 'robert@example.com',
                'password' => Hash::make('password'),
                'phone' => '+1234567896',
                'address' => '147 Business Center, City, Country',
                'role' => 'mentor',
                'status' => 'active',
                'gdpr_consent' => true,
            ],
            [
                'name' => 'Maria Garcia',
                'email' => 'maria@example.com',
                'password' => Hash::make('password'),
                'phone' => '+1234567897',
                'address' => '258 Creative Hub, City, Country',
                'role' => 'mentor',
                'status' => 'active',
                'gdpr_consent' => true,
            ],
        ];

        foreach ($mentors as $mentorData) {
            $user = User::create($mentorData);
            
            // Create mentor profile
            Mentor::create([
                'user_id' => $user->id,
                'bio' => $this->getMentorBio($user->name),
                'photo' => null,
                'work_experience' => $this->getWorkExperience(),
                'certifications' => $this->getCertifications(),
                'availability' => 'available',
                'working_hours' => 'Monday-Friday 9AM-6PM',
                'verified' => true,
            ]);
        }

        // Create additional users using factory
        User::factory()->count(15)->create(['role' => 'user']);
        
        // Create additional mentors using factory
        $additionalMentors = User::factory()->count(5)->create(['role' => 'mentor']);
        
        foreach ($additionalMentors as $mentorUser) {
            Mentor::create([
                'user_id' => $mentorUser->id,
                'bio' => $this->getMentorBio($mentorUser->name),
                'photo' => null,
                'work_experience' => $this->getWorkExperience(),
                'certifications' => $this->getCertifications(),
                'availability' => 'available',
                'working_hours' => 'Monday-Friday 9AM-6PM',
                'verified' => rand(0, 1) ? true : false,
            ]);
        }

        echo "✅ Created users and mentors\n";
    }

    /**
     * Get a mentor bio.
     */
    private function getMentorBio(string $name): string
    {
        $bios = [
            "Experienced professional with over 10 years in the industry. Passionate about teaching and helping others achieve their goals.",
            "Expert instructor with a proven track record of student success. Specializes in practical, real-world applications.",
            "Industry veteran with extensive experience in both corporate and educational settings. Committed to personalized learning.",
            "Certified professional with multiple industry certifications. Focuses on hands-on learning and skill development.",
            "Former corporate executive turned educator. Brings real-world experience to every lesson.",
        ];

        return $bios[array_rand($bios)];
    }

    /**
     * Get work experience.
     */
    private function getWorkExperience(): string
    {
        $experiences = [
            "Senior Software Developer at TechCorp (2018-2023), Lead Developer at StartupInc (2015-2018)",
            "Marketing Director at BigBrand (2019-2024), Marketing Manager at SmallCorp (2016-2019)",
            "Business Consultant at ConsultingFirm (2017-2023), Project Manager at TechCompany (2014-2017)",
            "UX Designer at DesignStudio (2018-2024), Freelance Designer (2015-2018)",
            "Data Scientist at AnalyticsCorp (2019-2024), Research Analyst at DataFirm (2016-2019)",
        ];

        return $experiences[array_rand($experiences)];
    }

    /**
     * Get certifications.
     */
    private function getCertifications(): string
    {
        $certifications = [
            "AWS Certified Solutions Architect, Google Cloud Professional, Microsoft Azure Fundamentals",
            "Google Ads Certified, Facebook Blueprint Certified, HubSpot Content Marketing Certified",
            "PMP Certification, Agile Certified Practitioner, Six Sigma Green Belt",
            "Adobe Certified Expert, Google UX Design Certificate, Figma Professional Certificate",
            "Certified Data Scientist, Google Analytics Certified, Tableau Desktop Specialist",
        ];

        return $certifications[array_rand($certifications)];
    }
}
