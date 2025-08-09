@extends('frontend.layouts.app')

@section('title', 'Home')

@section('navbar-style')
    <style>
        #navbar {
            background: linear-gradient(to bottom right, #faf5ff, #fdf2f8) !important;
            border-bottom: none !important;
            box-shadow: none !important;
        }
        
    </style>
@endsection

@section('content')
@include('frontend.home.hero')
@include('frontend.home.works-and-offers')
@include('frontend.home.different')
@include('frontend.home.potential')
    <div class="container mx-auto">
        <h1 class="text-4xl font-bold">Welcome to Skillio</h1>
        <p class="mt-4">Your journey to skill mastery starts here.</p>
    </div>
@endsection