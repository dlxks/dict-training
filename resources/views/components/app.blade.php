<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Hangman') }} - {{ $title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .manga-bg {
            background-color: #e5e7eb;
            background-image: radial-gradient(#000 0.5px, transparent 0.5px);
            background-size: 10px 10px;
        }
    </style>
</head>

<body class="manga-bg text-black font-sans antialiased min-h-screen selection:bg-yellow-300">
    @auth
        <div class="max-w-md mx-auto flex justify-between items-center gap-2 pt-2 px-2 z-50">
            <span
                class="font-black uppercase text-[10px] bg-white px-2 py-0.5 border-2 border-black rounded shadow-[2px_2px_0_0_#000]">
                ID: {{ auth()->user()->name }}
            </span>
            <form action="{{ route('auth.logout') }}" method="POST">
                @csrf
                <button type="submit"
                    class="bg-white border-2 border-black font-black uppercase py-0.5 px-2 rounded text-[10px] shadow-[2px_2px_0_0_#000] active:translate-y-0.5">
                    EXIT
                </button>
            </form>
        </div>
    @endauth

    <div class="max-w-md mx-auto py-4 px-2">
        {{ $slot }}
    </div>
</body>

</html>
