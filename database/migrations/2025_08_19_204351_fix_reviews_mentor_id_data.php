<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, let's update the mentor_id values to use the correct mentor.id instead of mentor.user_id
        $mentorMappings = DB::table('mentors')->pluck('id', 'user_id')->toArray();
        
        // Update all reviews where mentor_id is not null
        DB::table('reviews')
            ->whereNotNull('mentor_id')
            ->where('mentor_id', '!=', '')
            ->orderBy('id')
            ->chunk(100, function ($reviews) use ($mentorMappings) {
                foreach ($reviews as $review) {
                    if (isset($mentorMappings[$review->mentor_id])) {
                        DB::table('reviews')
                            ->where('id', $review->id)
                            ->update(['mentor_id' => $mentorMappings[$review->mentor_id]]);
                    }
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse the mapping: convert mentor.id back to mentor.user_id
        $mentorMappings = DB::table('mentors')->pluck('user_id', 'id')->toArray();
        
        // Update all reviews where mentor_id is not null
        DB::table('reviews')
            ->whereNotNull('mentor_id')
            ->where('mentor_id', '!=', '')
            ->orderBy('id')
            ->chunk(100, function ($reviews) use ($mentorMappings) {
                foreach ($reviews as $review) {
                    if (isset($mentorMappings[$review->mentor_id])) {
                        DB::table('reviews')
                            ->where('id', $review->id)
                            ->update(['mentor_id' => $mentorMappings[$review->mentor_id]]);
                    }
                }
            });
    }
};
