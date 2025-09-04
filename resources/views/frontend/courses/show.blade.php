@extends('frontend.layouts.app')

@section('title', $course->title)

@section('navbar-style')
    <style>
        #navbar {
            background-color: #f9fafb !important; /* Tailwind's bg-gray-50 */
            border-bottom: none !important;
            box-shadow: none !important;
        }
    </style>
@endsection

@section('content')
    <!-- Course Details Section -->
    <section class="pt-20 pb-8 bg-gray-50">
        <div class="max-w-[1400px] mx-auto px-6">
            @include('shared.course-details', ['course' => $course, 'showEditButton' => false, 'showEarningTab' => false, 'isEnrolled' => $isEnrolled])
        </div>
    </section>
@endsection
