<x-app>
    <x-slot:title>Choose Username - Google Sign In</x-slot:title>

    @if ($errors->any())
        <div
            class="max-w-xs mx-auto mb-4 bg-black border-2 border-white text-white p-2 text-center transform rotate-1 outline outline-black">
            <span class="font-black text-lg uppercase italic">Invalid Username!</span>
        </div>
    @endif

    <div class="max-w-xs mx-auto bg-white p-6 border-4 border-black shadow-[8px_8px_0_0_#000] mt-4">
        <h1 class="text-2xl font-black uppercase italic border-b-4 border-black mb-6">Google Sign In</h1>

        <div class="text-center mb-6 p-4 bg-gray-100 border-2 border-black">
            <p class="text-lg font-black">Signed in as:</p>
            <p class="text-lg font-black text-green-700 break-all px-2">{{ session('google_user.email') }}</p>
        </div>

        <form action="{{ route('auth.google.username') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-[10px] font-black uppercase">Choose Username (Required)</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                    class="block w-full px-3 py-2 border-[3px] border-black text-lg font-black outline-none focus:bg-yellow-50 @error('name') @enderror">
                @error('name')
                    <p class="mt-1 text-[10px] font-black text-red-700">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                class="w-full py-3 bg-white border-[3px] border-black font-black uppercase text-xl shadow-[4px_4px_0_0_#000] active:shadow-none active:translate-y-1">
                Continue to Games!
            </button>

            <div class="text-center pt-4">
                <a href="{{ route('login') }}" class="text-[10px] font-black uppercase hover:underline">
                    Back to Login
                </a>
            </div>
        </form>
    </div>
</x-app>
