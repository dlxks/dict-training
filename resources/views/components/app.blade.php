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
        @if (!auth()->user()->hasVerifiedEmail())
            <div
                class="max-w-md mx-auto bg-yellow-300 border-4 border-black p-3 text-center transform -rotate-2 shadow-[4px_4px_0_0_#000] mb-4 mx-4 z-50">
                <span class="block font-black uppercase text-lg mb-1">Email Not Verified</span>
                <span class="text-sm font-bold">Verify your email to play games!</span>
                <form method="POST" action="{{ route('verification.send') }}" class="mt-2 inline">
                    @csrf
                    <button type="submit"
                        class="bg-black text-white px-4 py-1 border-2 border-black font-black text-xs uppercase shadow-[2px_2px] hover:bg-gray-800 ml-2">
                        Resend Link
                    </button>
                </form>
                <a href="{{ route('verification.notice') }}"
                    class="text-xs font-black uppercase underline ml-2 hover:no-underline">Go to Verify</a>
            </div>
        @endif
        <div class="max-w-md mx-auto flex justify-between items-center gap-2 pt-2 px-2 z-50">
            <span
                class="font-black uppercase text-[10px] bg-white px-2 py-0.5 border-2 border-black rounded shadow-[2px_2px_0_0_#000]">
                ID: {{ auth()->user()->name }} {{ auth()->user()->hasVerifiedEmail() ? '(Verified)' : '(Pending)' }}
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
        @if (session('success'))
            <div
                class="bg-green-500 text-white border-2 border-black p-2 mb-2 font-black text-xs uppercase shadow-[2px_2px_0_0_#000]">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div
                class="bg-red-500 text-white border-2 border-black p-2 mb-2 font-black text-xs uppercase shadow-[2px_2px_0_0_#000]">
                {{ session('error') }}
            </div>
        @endif
        {{ $slot }}
    </div>
</body>

</html>
