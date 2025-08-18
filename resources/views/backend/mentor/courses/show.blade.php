@extends('backend.layouts.app')

@section('title', 'Course Details')

@section('header')
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <a href="{{ route('mentor.courses.index') }}" class="text-gray-500 hover:text-gray-700">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Course Details</h1>
                <p class="text-sm text-gray-500 mt-1">View your course information</p>
            </div>
        </div>
        @if($course->needs_reapproval)
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-700 border border-orange-300 shadow-sm ml-4">
            <i class="fa-solid fa-circle-exclamation mr-2 text-orange-500"></i>
            Re-Approval Required
            </span>
        @endif
        
        <!-- Edit Button -->
        <div class="flex items-center space-x-3">
            <a href="{{ route('mentor.courses.edit', $course) }}" 
               class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                <i class="fa-solid fa-edit mr-2"></i>
                Edit Course
            </a>
        </div>
    </div>
@endsection

@section('content')
    @include('shared.course-details', ['course' => $course, 'showEditButton' => true, 'showEarningTab' => true])
@endsection
