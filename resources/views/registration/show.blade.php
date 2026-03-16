
<x-app>
    <x-slot:title>Create Account</x-slot:title>

    @session('success')
        <div class="max-w-xs mx-auto mb-4 bg-yellow-300 border-4 border-black p-3 text-center transform -rotate-1 shadow-[4px_4px_0_0_#000]">
            <strong class="font-black text-xl block uppercase">Success!</strong>
        </div>
    @endsession

    <div class="max-w-xs mx-auto bg-white p-6 border-4 border-black shadow-[8px_8px_0_0_#000] mt-2">
        <div class="flex justify-between items-center mb-6 border-b-4 border-black">
            <h1 class="text-xl font-black uppercase italic">New Player</h1>
            <a href="/" class="text-[10px] font-bold uppercase underline">Back</a>
        </div>

        <form action="{{ route('registration.save') }}" method="POST" class="space-y-3">
            @csrf
            <div>
                <label class="block text-[10px] font-black uppercase">Name</label>
                <input type="text" name="name" required class="block w-full px-2 py-1 border-2 border-black font-bold outline-none">
            </div>

            <div>
                <label class="block text-[10px] font-black uppercase">Email</label>
                <input type="email" name="email" required class="block w-full px-2 py-1 border-2 border-black font-bold outline-none">
            </div>

            <div>
                <label class="block text-[10px] font-black uppercase">Password</label>
                <input type="password" name="password" required class="block w-full px-2 py-1 border-2 border-black font-bold outline-none">
            </div>

            <div>
                <label class="block text-[10px] font-black uppercase">Confirm</label>
                <input type="password" name="password_confirmation" required class="block w-full px-2 py-1 border-2 border-black font-bold outline-none">
            </div>

            <button type="submit"
                class="w-full mt-4 py-3 bg-black text-white border-[3px] border-black font-black uppercase text-lg shadow-[4px_4px_0_0_#ccc] active:translate-y-1">
                Register!
            </button>
        </form>
    </div>
</x-app>