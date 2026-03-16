<?php

namespace App\Http\Controllers;

use App\Http\Requests\Registration\SaveRegistrationRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash; // Don't forget to import Hash!

class RegistrationController extends Controller
{
    public function show()
    {
        return view('registration.show');
    }

    public function save(SaveRegistrationRequest $request)
    {
        // Get the validated data as an array
        $validatedData = $request->validated();

        // Hash the password before saving!
        $validatedData['password'] = Hash::make($validatedData['password']);

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => $validatedData['password'],
        ]);

        Auth::login($user); // Temporary login to access verification features

        $user->sendEmailVerificationNotification();

        // Redirect to verification notice
        return redirect()->route('verification.notice')->with('success', 'Registration successful! Please check your email to verify your account.');
    }
}
