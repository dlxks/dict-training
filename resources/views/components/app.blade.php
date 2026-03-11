<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Hangman') }} - {{ $title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-sky-300 text-black font-sans antialiased min-h-screen selection:bg-yellow-300 relative">

    @auth
        <div class="max-w-4xl mx-auto flex justify-end items-center gap-4 pt-6 px-4 sm:px-6 lg:px-8 z-50">
            <span
                class="font-black uppercase tracking-wider bg-white px-3 py-1 border-2 border-black rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] transform rotate-1 hidden sm:inline-block">
                Hi, {{ auth()->user()->name }}!
            </span>
            <form action="{{ route('auth.logout') }}" method="POST">
                @csrf
                <button type="submit"
                    class="bg-red-400 hover:bg-red-500 text-black border-2 border-black font-black uppercase py-1 px-3 rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] active:shadow-none active:translate-y-0.5 active:translate-x-0.5 transition-all text-sm transform -rotate-2">
                    Logout
                </button>
            </form>
        </div>
    @endauth

    <div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8 mt-4 sm:mt-0">
        {{ $slot }}
    </div>
</body>

</html>
