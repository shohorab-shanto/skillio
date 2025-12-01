<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Models\SessionBooking;
use App\Models\UserEnrollment;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class UserConversationsApiController extends Controller
{
    /**
     * Get user's conversations
     */
    public function index()
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated'
                ], 401);
            }
            
            // Get user's conversations with latest message and unread count
            $conversations = Conversation::where(function($query) use ($user) {
                // User is a student in conversation
                $query->where('user_id', $user->id);
            })->orWhereHas('mentor', function($query) use ($user) {
                // User is a mentor in conversation
                $query->where('user_id', $user->id);
            })
            ->with(['mentor.user', 'user', 'latestMessage', 'enrollment.enrollable'])
            ->orderBy('last_message_at', 'desc')
            ->get()
            ->map(function($conversation) use ($user) {
                // Safety checks for null relationships
                if (!$conversation->mentor || !$conversation->mentor->user || !$conversation->user) {
                    return null;
                }
                
                $isUser = $conversation->user_id == $user->id;
                $otherUser = $isUser ? $conversation->mentor->user : $conversation->user;
                
                return [
                    'id' => $conversation->id,
                    'unique_code' => $conversation->unique_code,
                    'other_user' => [
                        'id' => $otherUser->id,
                        'name' => $otherUser->name ?? '',
                        'photo' => $otherUser->photo ? asset('storage/' . $otherUser->photo) : null,
                        'role' => $otherUser->role ?? 'user',
                    ],
                    'is_active' => $conversation->isConversationActive(),
                    'last_message' => $conversation->latestMessage ? [
                        'id' => $conversation->latestMessage->id,
                        'content' => $conversation->latestMessage->content ?? '',
                        'type' => $conversation->latestMessage->type ?? 'text',
                        'sender_id' => $conversation->latestMessage->sender_id,
                        'is_from_me' => $conversation->latestMessage->sender_id == $user->id,
                        'created_at' => $conversation->latestMessage->created_at->format('Y-m-d H:i:s'),
                    ] : null,
                    'last_message_at' => $conversation->last_message_at ? $conversation->last_message_at->format('Y-m-d H:i:s') : null,
                    'enrollment' => $conversation->enrollment ? [
                        'id' => $conversation->enrollment->id,
                        'enrollable_type' => class_basename($conversation->enrollment->enrollable_type),
                        'enrollable_id' => $conversation->enrollment->enrollable_id,
                    ] : null,
                ];
            })
            ->filter(function($conversation) {
                return $conversation !== null;
            })
            ->values();
            
            return response()->json([
                'success' => true,
                'data' => $conversations
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error in UserConversationsApiController@index: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve conversations',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get specific conversation
     */
    public function show($code)
    {
        $user = Auth::user();
        $conversation = Conversation::findByCode($code);
        
        if (!$conversation) {
            return response()->json([
                'success' => false,
                'message' => 'Conversation not found.'
            ], 404);
        }
        
        // Check if user can access this conversation
        if (!$conversation->canAccess($user->id)) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to access this conversation.'
            ], 403);
        }
        
        // Get messages for this conversation
        $perPage = request()->get('per_page', 20);
        $messages = Message::where('conversation_id', $conversation->id)
            ->with('sender')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
        
        // Transform messages
        $messages->getCollection()->transform(function($message) use ($user) {
            return [
                'id' => $message->id,
                'content' => $message->content,
                'type' => $message->type,
                'file_path' => $message->file_path ? asset('storage/' . $message->file_path) : null,
                'file_name' => $message->file_name,
                'file_size' => $message->file_size,
                'file_type' => $message->file_type,
                'sender' => [
                    'id' => $message->sender->id,
                    'name' => $message->sender->name,
                    'photo' => $message->sender->photo ? asset('storage/' . $message->sender->photo) : null,
                ],
                'is_from_me' => $message->sender_id == $user->id,
                'read_at' => $message->read_at ? $message->read_at->format('Y-m-d H:i:s') : null,
                'created_at' => $message->created_at->format('Y-m-d H:i:s'),
            ];
        });
        
        // Get chat permission status
        $chatStatus = $this->getChatPermissionStatus($user->id, $conversation->mentor_id);
        
        $isUser = $conversation->user_id == $user->id;
        $otherUser = $isUser ? $conversation->mentor->user : $conversation->user;
        
        return response()->json([
            'success' => true,
            'data' => [
                'conversation' => [
                    'id' => $conversation->id,
                    'unique_code' => $conversation->unique_code,
                    'other_user' => [
                        'id' => $otherUser->id,
                        'name' => $otherUser->name,
                        'photo' => $otherUser->photo ? asset('storage/' . $otherUser->photo) : null,
                        'role' => $otherUser->role,
                    ],
                    'is_active' => $conversation->isConversationActive(),
                    'last_message_at' => $conversation->last_message_at ? $conversation->last_message_at->format('Y-m-d H:i:s') : null,
                ],
                'messages' => $messages->items(),
                'pagination' => [
                    'current_page' => $messages->currentPage(),
                    'last_page' => $messages->lastPage(),
                    'per_page' => $messages->perPage(),
                    'total' => $messages->total(),
                ],
                'chat_status' => $chatStatus,
            ]
        ]);
    }

    /**
     * Get conversation messages
     */
    public function getMessages($code)
    {
        $user = Auth::user();
        $conversation = Conversation::findByCode($code);
        
        if (!$conversation) {
            return response()->json([
                'success' => false,
                'message' => 'Conversation not found.'
            ], 404);
        }
        
        // Check if user can access this conversation
        if (!$conversation->canAccess($user->id)) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to access this conversation.'
            ], 403);
        }
        
        $perPage = request()->get('per_page', 20);
        $messages = Message::where('conversation_id', $conversation->id)
            ->with('sender')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
        
        // Transform messages
        $messages->getCollection()->transform(function($message) use ($user) {
            return [
                'id' => $message->id,
                'content' => $message->content,
                'type' => $message->type,
                'file_path' => $message->file_path ? asset('storage/' . $message->file_path) : null,
                'file_name' => $message->file_name,
                'file_size' => $message->file_size,
                'file_type' => $message->file_type,
                'sender' => [
                    'id' => $message->sender->id,
                    'name' => $message->sender->name,
                    'photo' => $message->sender->photo ? asset('storage/' . $message->sender->photo) : null,
                ],
                'is_from_me' => $message->sender_id == $user->id,
                'read_at' => $message->read_at ? $message->read_at->format('Y-m-d H:i:s') : null,
                'created_at' => $message->created_at->format('Y-m-d H:i:s'),
            ];
        });
        
        return response()->json([
            'success' => true,
            'data' => [
                'messages' => $messages->items(),
                'pagination' => [
                    'current_page' => $messages->currentPage(),
                    'last_page' => $messages->lastPage(),
                    'per_page' => $messages->perPage(),
                    'total' => $messages->total(),
                ],
            ]
        ]);
    }

    /**
     * Send message
     */
    public function sendMessage(Request $request, $code)
    {
        $conversation = Conversation::findByCode($code);
        
        if (!$conversation) {
            return response()->json([
                'success' => false,
                'message' => 'Conversation not found.'
            ], 404);
        }
        
        $user = Auth::user();
        
        // Check if user can access this conversation
        if (!$conversation->canAccess($user->id)) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to send messages in this conversation.'
            ], 403);
        }

        // Check if conversation is active based on enrollment validity
        if (!$conversation->isConversationActive()) {
            return response()->json([
                'success' => false,
                'message' => 'This conversation is not active. Please check your enrollment status.',
                'can_chat' => false
            ], 403);
        }

        $request->validate([
            'content' => 'required_without:file|string|max:1000',
            'file' => 'nullable|file|max:10240', // 10MB max
        ]);

        $messageData = [
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'type' => 'text',
            'content' => $request->content,
        ];

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('chat_files', $fileName, 'public');
            
            $messageData['type'] = str_starts_with($file->getMimeType(), 'image/') ? 'image' : 'file';
            $messageData['file_path'] = $filePath;
            $messageData['file_name'] = $file->getClientOriginalName();
            $messageData['file_size'] = $file->getSize();
            $messageData['file_type'] = $file->getMimeType();
        }

        $message = Message::create($messageData);

        // Update conversation's last message time
        $conversation->update(['last_message_at' => now()]);

        // Load sender relationship for response
        $message->load('sender');

        // Create notification for recipient if they're not active in the conversation
        $recipientId = $user->id == $conversation->user_id ? $conversation->mentor->user_id : $conversation->user_id;
        $recipient = User::find($recipientId);
        
        if ($recipient && !$this->isUserActiveInConversation($recipientId, $conversation->id)) {
            // Create notification for new message
            \App\Services\NotificationService::createMessageNotification($user, $recipient, $conversation, $message);
        }

        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully',
            'data' => [
                'id' => $message->id,
                'content' => $message->content,
                'type' => $message->type,
                'file_path' => $message->file_path ? asset('storage/' . $message->file_path) : null,
                'file_name' => $message->file_name,
                'file_size' => $message->file_size,
                'file_type' => $message->file_type,
                'sender' => [
                    'id' => $message->sender->id,
                    'name' => $message->sender->name,
                    'photo' => $message->sender->photo ? asset('storage/' . $message->sender->photo) : null,
                ],
                'is_from_me' => true,
                'created_at' => $message->created_at->format('Y-m-d H:i:s'),
            ]
        ]);
    }

    /**
     * Check chat permission status
     */
    public function getChatStatus($code)
    {
        $user = Auth::user();
        $conversation = Conversation::findByCode($code);
        
        if (!$conversation) {
            return response()->json([
                'success' => false,
                'message' => 'Conversation not found.'
            ], 404);
        }
        
        // Check if user can access this conversation
        if (!$conversation->canAccess($user->id)) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to access this conversation.'
            ], 403);
        }
        
        $chatStatus = $this->getChatPermissionStatus($user->id, $conversation->mentor_id);
        
        return response()->json([
            'success' => true,
            'data' => $chatStatus
        ]);
    }

    /**
     * Check if user can chat with mentor and return detailed status
     */
    private function getChatPermissionStatus($userId, $mentorId)
    {
        $now = Carbon::now();
        $today = $now->toDateString();
        $currentTime = $now->format('H:i:s');
        
        // Initialize flags
        $canChat = false;
        $chatReason = '';
        $chatType = 'none';
        $chatDetails = '';
        $sessionEndTime = null;
        $courseEndDate = null;
        
        // Check course enrollments first (highest priority)
        $activeCourses = UserEnrollment::where('user_id', $userId)
            ->where('enrollment_status', 'active')
            ->whereHas('enrollable', function($query) use ($mentorId) {
                $query->where('mentor_id', $mentorId);
            })
            ->with('enrollable')
            ->get();
        
        if ($activeCourses->count() > 0) {
            $canChat = true;
            $chatType = 'course';
            $chatReason = 'Active course enrollment';
            $chatDetails = 'You can chat with this mentor because you have an active course enrollment.';
            
            // Get course end date for reference
            $course = $activeCourses->first()->enrollable;
            if ($course && $course->end_date) {
                $courseEndDate = $course->end_date->format('Y-m-d');
            }
        }
        
        // Check session bookings if no active courses
        if (!$canChat) {
            $activeSessions = UserEnrollment::where('user_id', $userId)
                ->where('enrollment_status', 'active')
                ->whereHas('enrollable', function($query) use ($mentorId) {
                    $query->where('mentor_id', $mentorId);
                })
                ->with('enrollable')
                ->get();
            
            foreach ($activeSessions as $enrollment) {
                $session = $enrollment->enrollable;
                
                // Check if session is today and hasn't ended yet
                if ($session->date == $today) {
                    $sessionEndTime = $session->end_time;
                    if ($currentTime < $sessionEndTime) {
                        $canChat = true;
                        $chatType = 'session';
                        $chatReason = 'Active session today';
                        $chatDetails = "You can chat with this mentor during your session today until {$sessionEndTime}.";
                        break;
                    }
                }
            }
        }
        
        // If no reason found at all
        if (empty($chatReason)) {
            $chatReason = 'No active enrollment';
            $chatType = 'none';
            $chatDetails = 'You need to enroll in a course or book a session to chat with this mentor';
        }
        
        return [
            'can_chat' => $canChat,
            'reason' => $chatReason,
            'type' => $chatType,
            'details' => $chatDetails,
            'session_end_time' => $sessionEndTime,
            'course_end_date' => $courseEndDate
        ];
    }

    /**
     * Check if user is active in conversation
     */
    private function isUserActiveInConversation($userId, $conversationId)
    {
        // Get the most recent message from the other person in this conversation
        $recentMessage = Message::where('conversation_id', $conversationId)
            ->where('sender_id', '!=', $userId)
            ->latest()
            ->first();

        if (!$recentMessage) {
            return false; // No messages to read, consider inactive
        }

        // Check if user has read the recent message within last 2 minutes
        // This means they're likely still active in the conversation
        return $recentMessage->read_at && $recentMessage->read_at->diffInMinutes(now()) < 2;
    }
}
