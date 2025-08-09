<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Skillio') | Skillio</title>

    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Daisy UI + Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/daisyui@3.8.0/dist/full.css" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // Force light mode configuration
        tailwind.config = {
            theme: {
                extend: {}
            },
            daisyui: {
                themes: ["light"],
                darkTheme: "light"
            }
        }
        
        // Ensure light mode is always applied
        document.documentElement.setAttribute('data-theme', 'light');
    </script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

        <style>
        * {
            font-family: 'Poppins', sans-serif !important;

        }

        body {
            background-color: #f7f7f7;
        }
    </style>

    
    <!-- Custom Styles Stack -->
    @stack('styles')
    
    <!-- Navbar Styles -->
    @yield('navbar-style')
    
    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>

    <!-- Navigation Bar -->
    @include('frontend.layouts.nav-bar')
    
    @yield('content')

    @include('frontend.layouts.footer')

    <!-- Custom Scripts Stack -->
    @stack('scripts')

</body>

</html>
