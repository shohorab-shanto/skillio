@extends('frontend.layouts.app')

@section('title', __('trans.home'))

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
    @include('frontend.home.perfect-mentor')
    @include('frontend.home.popular-courses')
    @include('frontend.home.new-and-featured-courses')
    @include('frontend.home.review')
    @include('frontend.home.question-answer')
@endsection