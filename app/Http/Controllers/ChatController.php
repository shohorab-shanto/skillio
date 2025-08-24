<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
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

class ChatController extends Controller
{
    /**
     * Check if user can chat with mentor and return detailed status
     */
    private function getChatPermissionStatus($userId, $mentorId)
    {
        $now = Carbon::now();
        $today = $now->toDateString();
        $currentTime = $now->format('H:i:s');
        
        // Check session bookings for today
        $todaySessions = SessionBooking::where('user_id', $userId)
            ->where('mentor_id', $mentorId)
            ->where('status', 'booked')
            ->where('date', $today)
            ->get();
        
        foreach ($todaySessions as $session) {
            // Skip sessions without proper time information
            if (!$session->start_time || !$session->end_time) {
                continue;
            }
            
            if ($currentTime >= $session->start_time && $currentTime <= $session->end_time) {
                return [
                    'can_chat' => true,
                    'reason' => 'Session is currently active',
                    'type' => 'session',
                    'details' => "You can chat during your {$session->start_time} - {$session->end_time} session today",
                    'session_end_time' => $session->end_time
                ];
            }
        }
        
        // Check if there are upcoming sessions today
        $upcomingToday = $todaySessions->where('start_time', '>', $currentTime)->first();
        if ($upcomingToday) {
            $timeUntil = Carbon::parse("{$today} {$upcomingToday->start_time}")->diffForHumans();
            return [
                'can_chat' => false,
                'reason' => 'Session not started yet',
                'type' => 'session',
                'details' => "Your session starts {$timeUntil} (at {$upcomingToday->start_time})"
            ];
        }
        
        // Check if there were sessions earlier today
        $earlierToday = $todaySessions->where('end_time', '<', $currentTime)->first();
        if ($earlierToday) {
            return [
                'can_chat' => false,
                'reason' => 'Session has ended',
                'type' => 'session',
                'details' => "Your {$earlierToday->start_time} - {$earlierToday->end_time} session ended earlier today"
            ];
        }
        
        // Check course enrollments
        $activeCourses = UserEnrollment::where('user_id', $userId)
            ->where('enrollment_status', 'active')
            ->whereHas('enrollable', function($query) use ($mentorId) {
                $query->where('mentor_id', $mentorId);
            })
            ->with('enrollable')
            ->get();
        
        foreach ($activeCourses as $enrollment) {
            $course = $enrollment->enrollable;
            
            // Skip courses without proper date information
            if (!$course->start_date || !$course->end_date) {
                continue;
            }
            
            // Ensure dates are Carbon instances
            $startDate = $course->start_date instanceof Carbon ? $course->start_date : Carbon::parse($course->start_date);
            $endDate = $course->end_date instanceof Carbon ? $course->end_date : Carbon::parse($course->end_date);
            
            if ($now->between($startDate, $endDate)) {
                return [
                    'can_chat' => true,
                    'reason' => 'Course is currently active',
                    'type' => 'course',
                    'details' => "You can chat during your course period (until " . $endDate->format('M d, Y') . ")",
                    'course_end_date' => $endDate
                ];
            }
            
            if ($now < $startDate) {
                $timeUntil = $startDate->diffForHumans();
                return [
                    'can_chat' => false,
                    'reason' => 'Course not started yet',
                    'type' => 'course',
                    'details' => "Your course starts {$timeUntil} (on " . $startDate->format('M d, Y') . ")"
                ];
            }
            
            if ($now > $endDate) {
                $timeAgo = $endDate->diffForHumans();
                return [
                    'can_chat' => false,
                    'reason' => 'Course has ended',
                    'type' => 'course',
                    'details' => "Your course ended {$timeAgo} (on " . $endDate->format('M d, Y') . ")"
                ];
            }
        }
        
        // No active enrollments
        return [
            'can_chat' => false,
            'reason' => 'No active enrollment',
            'type' => 'none',
            'details' => 'You need to enroll in a course or book a session to chat with this mentor'
        ];
    }

    /**
     * Show the chat index page with all conversations.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get user's conversations with latest message and unread count
        $list_conversations = Conversation::where(function($query) use ($user) {
            // User is a student in conversation
            $query->where('user_id', $user->id);
        })->orWhereHas('mentor', function($query) use ($user) {
            // User is a mentor in conversation
            $query->where('user_id', $user->id);
        })
        ->with(['mentor.user', 'user', 'latestMessage'])
        ->orderBy('last_message_at', 'desc')
        ->get();

        return view('chat.index', compact('list_conversations'));
    }

    /**
     * Show a specific conversation.
     */
    public function show($code)
    {
        $conversation = Conversation::findByCode($code);
        
        if (!$conversation) {
            abort(404, 'Conversation not found.');
        }
        
        // Load the relationships after finding the conversation
        $conversation->load(['mentor.user', 'user']);
        
        // Debug: Log the loaded conversation data
        \Log::info('Loaded conversation data:', [
            'conversation_id' => $conversation->id,
            'mentor_id' => $conversation->mentor_id,
            'user_id' => $conversation->user_id,
            'mentor_loaded' => $conversation->relationLoaded('mentor'),
            'mentor_user_loaded' => $conversation->mentor && $conversation->mentor->relationLoaded('user'),
            'mentor_user_name' => $conversation->mentor && $conversation->mentor->user ? $conversation->mentor->user->name : 'null',
            'user_loaded' => $conversation->relationLoaded('user'),
            'user_name' => $conversation->user ? $conversation->user->name : 'null'
        ]);
        
        $user = Auth::user();
        
        // Check if user can access this conversation
        if (!$conversation->canAccess($user->id)) {
            // Get all conversations for the sidebar
            $list_conversations = Conversation::where(function($query) use ($user) {
                // User is a student in conversation
                $query->where('user_id', $user->id);
            })->orWhereHas('mentor', function($query) use ($user) {
                // User is a mentor in conversation
                $query->where('user_id', $user->id);
            })
            ->with(['mentor.user', 'user', 'latestMessage'])
            ->orderBy('last_message_at', 'desc')
            ->get();

            return view('chat.index', [
                'list_conversations' => $list_conversations,
                'error' => 'You do not have permission to access this conversation.',
                'errorType' => 'no_permission'
            ]);
        }

        // Get all conversations for the sidebar
        $list_conversations = Conversation::where(function($query) use ($user) {
            // User is a student in conversation
            $query->where('user_id', $user->id);
        })->orWhereHas('mentor', function($query) use ($user) {
            // User is a mentor in conversation
            $query->where('user_id', $user->id);
        })
        ->with(['mentor.user', 'user', 'latestMessage'])
        ->orderBy('last_message_at', 'desc')
        ->get();

        // Mark messages as read
        $conversation->messages()
            ->where('sender_id', '!=', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        // Load messages with sender
        $messages = $conversation->messages()->with('sender')->orderBy('created_at', 'asc')->get();

        // Get chat permission status for the current user
        $chatStatus = $this->getChatPermissionStatus($user->id, $conversation->mentor_id);

        return view('chat.index', compact('conversation', 'list_conversations', 'messages', 'chatStatus'));
    }

    /**
     * Send a new message.
     */
    public function sendMessage(Request $request, $code)
    {
        $conversation = Conversation::findByCode($code);
        
        if (!$conversation) {
            abort(404, 'Conversation not found.');
        }
        
        $user = Auth::user();
        
        // Check if user can access this conversation
        if (!$conversation->canAccess($user->id)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to send messages in this conversation.',
                ], 403);
            }
            return redirect()->route('chat.index')->with('error', 'You do not have permission to send messages in this conversation.');
        }

        // Check if user can chat based on time restrictions
        $chatStatus = $this->getChatPermissionStatus($user->id, $conversation->mentor_id);
        if (!$chatStatus['can_chat']) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $chatStatus['reason'],
                    'details' => $chatStatus['details'],
                    'type' => $chatStatus['type'],
                    'can_chat' => false
                ], 403);
            }
            return redirect()->back()->with('error', $chatStatus['reason'] . ': ' . $chatStatus['details']);
        }

        $request->validate([
            'content' => 'required_without:file|string|max:1000',
            'file' => 'nullable|file|max:10240', // 10MB max
        ]);

        $messageData = [
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'type' => 'text',
            'content' => $request->content, // Always store user's text message
        ];

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('chat_files', $fileName, 'public');
            
            $messageData['type'] = str_starts_with($file->getMimeType(), 'image/') ? 'image' : 'file';
            $messageData['file_path'] = $filePath; // Store file path in file_path field
            $messageData['file_name'] = $file->getClientOriginalName();
            $messageData['file_size'] = $file->getSize();
            $messageData['file_type'] = $file->getMimeType();
        }

        $message = Message::create($messageData);

        // Update conversation's last message time
        $conversation->update(['last_message_at' => now()]);

        // Load sender relationship for response
        $message->load('sender');

        // Broadcast message with Reverb
        broadcast(new MessageSent($message))->toOthers();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        }

        return redirect()->back()->with('success', 'Message sent successfully!');
    }

    /**
     * Create or get conversation between two users.
     */
    public function getOrCreateConversation(Request $request)
    {
        $request->validate([
            'other_user_id' => 'required|exists:users,id',
        ]);

        $user = Auth::user();
        $otherUserId = $request->other_user_id;

        // Prevent user from creating conversation with themselves
        if ($user->id == $otherUserId) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot start a conversation with yourself.',
            ], 400);
        }

        // Get other user and check if they exist
        $otherUser = User::find($otherUserId);
        if (!$otherUser) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        // Get mentor and student users
        $userMentor = $user->mentor;
        $otherUserMentor = $otherUser->mentor;

        // Determine who is mentor and who is student
        if ($userMentor && !$otherUserMentor) {
            // Current user is mentor, other is student
            $mentorId = $userMentor->id;
            $studentId = $otherUserId;
        } elseif (!$userMentor && $otherUserMentor) {
            // Other user is mentor, current is student  
            $mentorId = $otherUserMentor->id;
            $studentId = $user->id;
        } else {
            return response()->json([
                'success' => false,
                'message' => 'One user must be a mentor and one must be a student.',
            ], 400);
        }

        // Check if conversation already exists
        $conversation = Conversation::where('mentor_id', $mentorId)
                                  ->where('user_id', $studentId)
                                  ->first();

        if (!$conversation) {
            $conversation = Conversation::create([
                'mentor_id' => $mentorId,
                'user_id' => $studentId,
                'last_message_at' => now(),
            ]);
        }

        return response()->json([
            'success' => true,
            'conversation_code' => $conversation->unique_code,
        ]);
    }

    /**
     * Search for users to start a conversation with.
     */
    public function searchUsers(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:2',
        ]);

        $currentUser = Auth::user();
        $searchTerm = $request->q;

        // If current user is a mentor, search for students (users without mentor profiles)
        // If current user is a student, search for mentors (users with mentor profiles)
        if ($currentUser->mentor) {
            // Current user is a mentor, search for students
            $users = User::where(function($query) use ($searchTerm) {
                $query->where('name', 'like', '%' . $searchTerm . '%')
                      ->orWhere('email', 'like', '%' . $searchTerm . '%');
            })
            ->where('id', '!=', $currentUser->id)
            ->whereDoesntHave('mentor') // Users who are not mentors
            ->select('id', 'name', 'email')
            ->limit(10)
            ->get()
            ->map(function($user) {
                $user->role = 'student';
                $user->photo = null; // Students don't have photos in the users table
                return $user;
            });
        } else {
            // Current user is a student, search for mentors
            $users = User::where(function($query) use ($searchTerm) {
                $query->where('name', 'like', '%' . $searchTerm . '%')
                      ->orWhere('email', 'like', '%' . $searchTerm . '%');
            })
            ->where('id', '!=', $currentUser->id)
            ->whereHas('mentor') // Users who are mentors
            ->with('mentor:id,user_id,photo') // Load mentor photo
            ->select('id', 'name', 'email')
            ->limit(10)
            ->get()
            ->map(function($user) {
                $user->role = 'mentor';
                $user->photo = $user->mentor ? $user->mentor->photo : null;
                unset($user->mentor); // Remove the mentor relationship from response
                return $user;
            });
        }

        return response()->json($users);
    }
}
