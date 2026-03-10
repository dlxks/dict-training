<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="winter">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Hangman' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-base-200 text-base-content antialiased flex flex-col items-center">
    
    <div class="navbar bg-base-100 shadow-sm mb-8 w-full flex justify-center">
        <div class="w-full max-w-3xl flex justify-between px-4">
            <a href="{{ route('games.index') }}" class="btn btn-ghost normal-case text-xl">🎯 Hangman</a>
        </div>
    </div>

    <main class="w-full max-w-3xl px-4 pb-12">
        {{ $slot }}
    </main>
</body>
</html>