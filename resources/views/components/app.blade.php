<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Hangman' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Comic+Neue:wght@700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Comic Neue', cursive; }
    </style>
</head>
<body class="min-h-screen bg-yellow-300 text-black antialiased flex flex-col items-center selection:bg-pink-400 selection:text-black">
    
    <header class="w-full border-b-4 border-black bg-white mb-8 flex justify-center shadow-[0_4px_0_0_rgba(0,0,0,1)] relative z-10">
        <div class="w-full max-w-3xl flex justify-center items-center p-4">
            <a href="{{ route('games.index') }}" class="text-4xl font-black uppercase tracking-wider text-black hover:text-red-500 transition-colors transform hover:rotate-2">
                💥 HANGMAN! 💥
            </a>
        </div>
    </header>

    <main class="w-full max-w-3xl px-4 pb-16">
        {{ $slot }}
    </main>
</body>
</html>