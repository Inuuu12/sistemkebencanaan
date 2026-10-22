<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard Admin')</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="font-sans antialiased bg-slate-50 dark:bg-slate-950">
    <div id="app">
        <!-- Navbar -->
        @include('layouts.partials.navbar')

        <!-- Sidebar -->
        @include('layouts.partials.sidebar')

        <!-- Main Content -->
        <main class="main-content relative overflow-hidden transition-all duration-300 ease-in-out">
            @yield('content')
        </main>

        <!-- Footer -->
        @include('layouts.partials.footer')
    </div>
    @stack('scripts')
</body>
</html>
