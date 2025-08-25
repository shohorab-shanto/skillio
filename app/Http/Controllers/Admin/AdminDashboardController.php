<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    /**
     * Show admin dashboard
     */
    public function index()
    {
        // Get basic statistics for admin dashboard
        $totalUsers = \App\Models\User::where('role', '!=', 'admin')->count();
        $totalMentors = \App\Models\User::where('role', 'mentor')->count();
        $totalCourses = \App\Models\Course::count();
        
        // Calculate admin commission from payment transactions
        $adminCommission = \App\Models\PaymentTransaction::where('transaction_status', 'completed')->sum('admin_amount');

        // Get recent users and courses
        $recentUsers = \App\Models\User::where('role', '!=', 'admin')
            ->latest()
            ->take(10)
            ->get();
            
        $recentCourses = \App\Models\Course::with('mentor.user')
            ->latest()
            ->take(10)
            ->get();

        return view('admin.dashboard.index', compact(
            'totalUsers', 
            'totalMentors', 
            'totalCourses', 
            'adminCommission',
            'recentUsers',
            'recentCourses'
        ));
    }
}
