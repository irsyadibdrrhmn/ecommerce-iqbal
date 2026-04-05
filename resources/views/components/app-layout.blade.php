<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            <!-- Navigation -->
            <nav class="bg-white border-b border-gray-100">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <div class="flex items-center">
                                <a href="{{ route('home.index') }}" class="text-lg font-semibold">
                                    {{ config('app.name', 'Laravel') }}
                                </a>
                            </div>
                        </div>
                        <div class="flex items-center space-x-4">
                            @auth
                                <span>{{ Auth::user()->name }}</span>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                        Logout
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('login.form') }}" class="text-blue-600 hover:text-blue-900">Login</a>
                                <a href="{{ route('register.form') }}" class="text-blue-600 hover:text-blue-900">Register</a>
                            @endauth
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Content -->
            <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
