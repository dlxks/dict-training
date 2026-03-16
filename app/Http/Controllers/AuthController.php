<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\GoogleUsernameRequest;
use App\Http\Requests\Auth\LoginAuthRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function show()
    {
        return view('login.show');
    }

    public function login(LoginAuthRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->intended(route('lobby.index'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function googleRedirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function googleCallback()
    {
        $googleUser = Socialite::driver('google')->user();

        $user = User::where('google_id', $googleUser->id)->first();

        if ($user) {
            Auth::login($user);
            session()->regenerate();

            return redirect()->intended(route('lobby.index'));
        }

        // New Google user, store data and prompt for username
        session(['google_user' => [
            'id' => $googleUser->id,
            'email' => $googleUser->email,
            'name' => $googleUser->getName() ?? '',
        ]]);

        return redirect()->route('auth.google.username.show');
    }

    public function googleUsernameShow()
    {
        if (! session('google_user')) {
            return redirect()->route('login');
        }

        return view('auth.google-username');
    }

    public function googleUsernameStore(GoogleUsernameRequest $request)
    {
        $googleUser = session('google_user');

        if (! $googleUser) {
            return redirect()->route('login');
        }

        $user = User::create([
            'google_id' => $googleUser['id'],
            'email' => $googleUser['email'],
            'name' => $request->name,
            'password' => Hash::make('password123'),
        ]);

        Auth::login($user);
        session()->forget('google_user');
        session()->regenerate();

        return redirect()->intended(route('lobby.index'));
    }
}
