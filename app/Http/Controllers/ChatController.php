<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ChatController extends Controller
{
    /**
     * Display the chat interface.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get user's conversations with latest message and unread count
        $conversations = Conversation::where(function($query) use ($user) {
            // User is a student in conversation
            $query->where('user_id', $user->id);
        })->orWhereHas('mentor', function($query) use ($user) {
            // User is a mentor in conversation
            $query->where('user_id', $user->id);
        })
        ->with(['mentor.user', 'user', 'latestMessage'])
        ->orderBy('last_message_at', 'desc')
        ->get();

        return view('chat.index', compact('conversations'));
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
        
        $user = Auth::user();
        
        // Check if user can access this conversation
        if (!$conversation->canAccess($user->id)) {
            // Get all conversations for the sidebar
            $conversations = Conversation::where(function($query) use ($user) {
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
                'conversations' => $conversations,
                'error' => 'You do not have permission to access this conversation.',
                'errorType' => 'no_permission'
            ]);
        }

        // Get all conversations for the sidebar
        $conversations = Conversation::where(function($query) use ($user) {
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

        return view('chat.index', compact('conversation', 'conversations', 'messages'));
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
