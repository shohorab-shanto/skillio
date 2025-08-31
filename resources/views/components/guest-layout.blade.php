<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="grid grid-cols-1 md:grid-cols-2 min-h-screen bg-gradient-to-b from-pink-50 to-white">
            <!-- Left Panel -->
            <div class="flex flex-col justify-between px-8 py-12 md:px-24 bg-gradient-to-b from-pink-50 to-white">
                <!-- Logo header -->
                <a href="/"><img style="height:36px; width:112px;" src="{{ asset('assets/images/logo.png') }}" alt=""></a>
                
                <!-- Content -->
                <div class="max-w-md w-full mx-auto space-y-6">
                    {{ $slot }}
                </div>
                
                <!-- Footer -->
                @include('frontend.layouts.footer-onboard')
            </div>

            <!-- Right Panel -->
            <div class="hidden md:flex w-full h-full items-center justify-center p-2">
                <img src="{{ asset('assets/images/login-image-cloud.png') }}" alt="Login background" class="max-w-full max-h-full object-contain" />
            </div>
        </div>
    </body>
</html>
