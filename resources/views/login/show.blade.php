<x-app>
    <x-slot:title>Player Login</x-slot:title>

    @if (session('error') || $errors->has('login'))
        <div
            class="max-w-xs mx-auto mb-4 bg-black border-2 border-white text-white p-2 text-center transform rotate-1 outline outline-black">
            <span class="font-black text-lg uppercase italic">Access Denied!</span>
        </div>
    @endif

    <div class="max-w-xs mx-auto bg-white p-6 border-4 border-black shadow-[8px_8px_0_0_#000] mt-4">
        <h1 class="text-2xl font-black uppercase italic border-b-4 border-black mb-6">User Login</h1>

        <form action="{{ route('auth.login') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-[10px] font-black uppercase">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                    class="block w-full px-3 py-2 border-[3px] border-black text-lg font-black outline-none focus:bg-yellow-50">
            </div>

            <div>
                <label class="block text-[10px] font-black uppercase">Passcode</label>
                <input type="password" name="password" required
                    class="block w-full px-3 py-2 border-[3px] border-black text-lg font-black outline-none focus:bg-yellow-50">
            </div>

            <button type="submit"
                class="w-full py-3 bg-white border-[3px] border-black font-black uppercase text-xl shadow-[4px_4px_0_0_#000] active:shadow-none active:translate-y-1">
                Enter!
            </button>

            <span class="flex justify-center w-full font-black text-gray-500">OR</span>

            <a href="{{ route('auth.google') }}"
                class="block w-full py-3 bg-red-500 border-[3px] border-black text-white font-black uppercase text-xl shadow-[4px_4px_0_0_#000] hover:bg-red-600 active:shadow-none active:translate-y-1 text-center">
                Sign in with Google!
            </a>

            <div class="text-center pt-4 border-t-2 border-black border-dotted">
                <a href="{{ route('registration.show') }}" class="text-[10px] font-black uppercase hover:underline">
                    New Player? Create Profile &rarr;
                </a>
            </div>
        </form>
    </div>
</x-app>
