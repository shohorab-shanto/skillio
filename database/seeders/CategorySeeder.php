<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'E-Commerce & Online Stores',
                'description' => 'Dropshipping / E-Commerce Print on Demand',
                'subcategories' => [
                    'Dropshipping',
                    'Print on Demand',
                    'Shopify',
                ],
            ],
            [
                'name' => 'Agency & Freelance Services',
                'description' => 'Facebook / Instagram Ads, TikTok Ads',
                'subcategories' => [
                    'Facebook Ads',
                    'Instagram Ads',
                    'TikTok Ads',
                ],
            ],
            [
                'name' => 'Creative & Video Careers',
                'description' => 'Video Editing YouTube Channel Management',
                'subcategories' => [
                    'Video Editing',
                    'YouTube Channel Management',
                ],
            ],
            [
                'name' => 'Technical & AI Skills',
                'description' => 'AI Content Creation, ChatGPT Prompting',
                'subcategories' => [
                    'AI Content Creation',
                    'ChatGPT Prompting',
                ],
            ],
            [
                'name' => 'Finance, Trading & Investment',
                'description' => 'Forex Trading Cryptocurrency Trading',
                'subcategories' => [
                    'Forex Trading',
                    'Cryptocurrency Trading',
                ],
            ],
            [
                'name' => 'Others',
                'description' => 'New Category',
                'subcategories' => [
                    'New Category',
                ],
            ],
        ];

        foreach ($categories as $cat) {
            $category = \App\Models\Category::create([
                'name' => $cat['name'],
                'description' => $cat['description'],
            ]);
            foreach ($cat['subcategories'] as $sub) {
                \App\Models\SubCategory::create([
                    'category_id' => $category->id,
                    'name' => $sub,
                    'description' => $sub,
                ]);
            }
        }
    }
}
