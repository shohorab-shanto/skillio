<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title') | {{ config('app.name') }}</title>
    <meta name="title" content="@yield('title') | {{ config('app.name') }}">
    <meta name="description" content="@yield('meta_description', 'Welcome to ' . config('app.name'))">
    <meta name="keywords" content="@yield('meta_keywords', 'skills, learning, education, ' . config('app.name'))">
    <meta name="author" content="{{ config('app.name') }}">
    <meta name="robots" content="index, follow">

    <!-- Daisy UI CDN Link -->
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">

    <script src="{{ asset('assets/js/custom.js') }}"></script>


    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>

<body class="bg-white min-h-screen font-sans">
   
    <main>
        @yield('content')
    </main>
    
    
</body>

</html>
