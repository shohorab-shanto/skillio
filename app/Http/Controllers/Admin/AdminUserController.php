<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'user');

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }



        // Date filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $users = $query->latest()->paginate(15);

        return view('admin.users.index', compact('users'));
    }



    public function enrollments(User $user)
    {
        if ($user->role != 'user') {
            return redirect()->back()->with('error', 'Invalid user type');
        }

        // Get user enrollments with related data
        $enrollments = $user->enrollments()
            ->with(['enrollable', 'enrollable.mentor.user'])
            ->latest()
            ->paginate(15);

        return view('admin.users.enrollments', compact('user', 'enrollments'));
    }

    public function destroy(User $user)
    {
        if ($user->role != 'user') {
            return redirect()->back()->with('error', 'Invalid user type');
        }

        $user->delete();
        return redirect()->back()->with('success', 'User deleted successfully');
    }
}
