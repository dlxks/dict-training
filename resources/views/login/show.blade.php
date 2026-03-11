<x-app>
    <x-slot:title>
        Player Login
    </x-slot:title>

    @if (session('error') || $errors->has('login'))
        <div
            class="max-w-md mx-auto mt-8 bg-red-400 border-4 border-black text-black px-6 py-4 rounded-xl shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transform rotate-1 text-center">
            <strong class="font-black text-3xl block uppercase mb-1">BONK!</strong>
            <span class="font-bold text-lg uppercase tracking-wide">Invalid credentials!</span>
        </div>
    @endif

    <div
        class="max-w-md mx-auto bg-white p-8 border-4 border-black rounded-2xl shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] mt-8 relative">
        <div class="flex justify-between items-center mb-8 border-b-4 border-black pb-4">
            <h1 class="text-2xl sm:text-3xl font-black uppercase tracking-wide transform -rotate-1">Login</h1>
        </div>

        <form action="{{ route('auth.login') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label for="email" class="block text-lg font-black mb-2 uppercase tracking-wide">Email:</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="block w-full px-4 py-3 border-4 border-black rounded-xl text-lg font-bold shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] focus:outline-none focus:shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] focus:translate-y-0.5 focus:translate-x-0.5 focus:bg-yellow-50 transition-all">
                @error('email')
                    <div
                        class="mt-3 text-red-700 font-black uppercase tracking-wider bg-red-200 border-2 border-red-700 p-2 rounded-lg transform rotate-1">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-lg font-black mb-2 uppercase tracking-wide">Password:</label>
                <input id="password" type="password" name="password" required
                    class="block w-full px-4 py-3 border-4 border-black rounded-xl text-lg font-bold shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] focus:outline-none focus:shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] focus:translate-y-0.5 focus:translate-x-0.5 focus:bg-yellow-50 transition-all">
                @error('password')
                    <div
                        class="mt-3 text-red-700 font-black uppercase tracking-wider bg-red-200 border-2 border-red-700 p-2 rounded-lg transform -rotate-1">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="pt-4">
                <button type="submit"
                    class="w-full py-4 px-4 bg-blue-400 hover:bg-blue-500 border-4 border-black rounded-xl shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] text-2xl font-black uppercase tracking-widest active:shadow-none active:translate-y-1.5 active:translate-x-1.5 transition-all transform -rotate-1">
                    ZAP! Log In!
                </button>
            </div>

            <div class="text-center mt-6 pt-6 border-t-4 border-black border-dashed">
                <p class="font-bold text-lg uppercase tracking-wide mb-2 transform rotate-1">Don't have an account?</p>
                <a href="{{ route('registration.show') }}"
                    class="inline-block font-black uppercase text-xl text-green-600 hover:text-green-500 hover:underline transform -rotate-1 transition-all">
                    Create Player &rarr;
                </a>
            </div>
        </form>
    </div>
</x-app>
