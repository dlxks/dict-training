<x-app>
    <x-slot:title>Verify Email</x-slot:title>

    <div class="max-w-md mx-auto space-y-6">
        @if (session('status') == 'Verification link sent!')
            <div class="bg-green-200 border-4 border-green-700 p-4 rounded shadow-lg text-center">
                <h2 class="text-xl font-black uppercase mb-2">Link Sent!</h2>
                <p class="font-bold">A new verification link has been sent to your email address.</p>
            </div>
        @endif

        <div class="bg-white border-4 border-black p-8 shadow-[8px_8px_0_0_#000] text-center">
            <h1 class="text-2xl font-black uppercase italic border-b-4 border-black mb-6 pb-2">Verify Email</h1>

            <p class="text-lg font-bold mb-6">
                Thanks for signing up! Before getting started, could you verify your email address by clicking on the
                link we just emailed to you?
            </p>

            @if (session('success'))
                <div class="bg-yellow-300 border-4 border-black p-4 mb-6 transform -rotate-1 shadow-[4px_4px_0_0_#000]">
                    <strong class="font-black text-xl block uppercase">{{ session('success') }}</strong>
                </div>
            @endif

            <form method="POST" action="{{ route('verification.send') }}" class="space-y-4">
                @csrf
                <button type="submit"
                    class="w-full py-3 bg-black text-white border-[3px] border-black font-black uppercase text-xl shadow-[4px_4px_0_0_#ccc] hover:bg-gray-800 active:translate-y-1">
                    Resend Verification Email
                </button>
            </form>

            <div class="pt-6 border-t-2 border-black text-center">
                <a href="{{ route('auth.logout') }}"
                    class="text-[12px] font-black uppercase hover:underline block mt-2">
                    Re-send to different email? Log out
                </a>
            </div>
        </div>
    </div>
</x-app>
