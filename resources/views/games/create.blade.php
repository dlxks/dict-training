<x-app>
    <x-slot:title>Create New Game</x-slot:title>

    <div class="card bg-base-100 shadow-xl max-w-md mx-auto mt-8">
        <div class="card-body">
            <h2 class="card-title text-2xl mb-4">Start a New Game</h2>
            
            <form method="POST" action="{{ route('games.store') }}">
                @csrf
                <div class="form-control w-full">
                    <label class="label" for="name">
                        <span class="label-text font-semibold">Game Name</span>
                    </label>
                    <input type="text" name="name" id="name" required placeholder="e.g., Session 1" 
                           class="input input-bordered w-full focus:input-primary" />
                    @error('name')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                </div>

                <div class="card-actions justify-end mt-8">
                    <a href="{{ route('games.index') }}" class="btn btn-ghost">Cancel</a>
                    <button type="submit" class="btn btn-primary">Create Game</button>
                </div>
            </form>
        </div>
    </div>
</x-app>