@extends('backend.layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-6">Mentor Dashboard</h1>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Dashboard content goes here -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-2">Welcome to your dashboard</h3>
            <p class="text-gray-600">This is your Mentor dashboard.</p>
        </div>
    </div>
</div>
@endsection