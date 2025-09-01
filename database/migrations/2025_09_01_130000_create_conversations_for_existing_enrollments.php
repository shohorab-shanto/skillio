<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\UserEnrollment;
use App\Models\Conversation;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create conversations for existing enrollments that don't have conversations
        $enrollmentsWithoutConversations = UserEnrollment::whereNotExists(function($query) {
            $query->select(\DB::raw(1))
                  ->from('conversations')
                  ->whereRaw('conversations.enrollment_id = user_enrollments.id');
        })->get();
        
        foreach ($enrollmentsWithoutConversations as $enrollment) {
            try {
                // Check if enrollable exists and has mentor
                if ($enrollment->enrollable && $enrollment->enrollable->mentor) {
                    Conversation::create([
                        'enrollment_id' => $enrollment->id,
                        'mentor_id' => $enrollment->enrollable->mentor->id,
                        'user_id' => $enrollment->user_id,
                        'unique_code' => Conversation::generateUniqueCode(),
                    ]);
                }
            } catch (\Exception $e) {
                // Log error but continue with other enrollments
                \Log::error('Failed to create conversation for enrollment: ' . $enrollment->id, [
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Delete conversations created for existing enrollments
        $enrollmentsWithConversations = UserEnrollment::whereExists(function($query) {
            $query->select(\DB::raw(1))
                  ->from('conversations')
                  ->whereRaw('conversations.enrollment_id = user_enrollments.id');
        })->get();
        
        foreach ($enrollmentsWithConversations as $enrollment) {
            $conversation = Conversation::where('enrollment_id', $enrollment->id)->first();
            if ($conversation) {
                $conversation->delete();
            }
        }
    }
};
