<x-app>
    <x-slot:title>
        Create Player Account
    </x-slot:title>

    @session('success')
        <div
            class="max-w-md mx-auto mt-8 bg-green-400 border-4 border-black text-black px-6 py-4 rounded-xl shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transform -rotate-1 text-center">
            <strong class="font-black text-3xl block uppercase mb-1">AWESOME!</strong>
            <span class="font-bold text-lg uppercase tracking-wide">Player Account Created!</span>
        </div>
    @endsession

    <div
        class="max-w-md mx-auto bg-white p-8 border-4 border-black rounded-2xl shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] mt-8 relative">
        <div class="flex justify-between items-center mb-8 border-b-4 border-black pb-4">
            <h1 class="text-2xl sm:text-3xl font-black uppercase tracking-wide transform -rotate-1">Create Player</h1>
            <a href="/"
                class="bg-red-400 hover:bg-red-500 text-black border-2 border-black font-black uppercase py-1 px-3 rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] active:shadow-none active:translate-y-0.5 active:translate-x-0.5 transition-all text-sm transform rotate-2">
                &larr; Back
            </a>
        </div>

        <form action="{{ route('registration.save') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label for="name" class="block text-lg font-black mb-2 uppercase tracking-wide">Name:</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required
                    class="block w-full px-4 py-3 border-4 border-black rounded-xl text-lg font-bold shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] focus:outline-none focus:shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] focus:translate-y-0.5 focus:translate-x-0.5 focus:bg-yellow-50 transition-all">
                @error('name')
                    <div
                        class="mt-3 text-red-700 font-black uppercase tracking-wider bg-red-200 border-2 border-red-700 p-2 rounded-lg transform rotate-1">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-lg font-black mb-2 uppercase tracking-wide">Email:</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required
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

            <div>
                <label for="confirmPassword" class="block text-lg font-black mb-2 uppercase tracking-wide">Confirm
                    Password:</label>
                <input id="confirmPassword" type="password" name="password_confirmation" required
                    class="block w-full px-4 py-3 border-4 border-black rounded-xl text-lg font-bold shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] focus:outline-none focus:shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] focus:translate-y-0.5 focus:translate-x-0.5 focus:bg-yellow-50 transition-all">
            </div>

            <div class="pt-4">
                <button type="submit"
                    class="w-full py-4 px-4 bg-green-400 hover:bg-green-500 border-4 border-black rounded-xl shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] text-2xl font-black uppercase tracking-widest active:shadow-none active:translate-y-1.5 active:translate-x-1.5 transition-all transform rotate-1">
                    BAM! Sign Up!
                </button>
            </div>
        </form>
    </div>
</x-app>
