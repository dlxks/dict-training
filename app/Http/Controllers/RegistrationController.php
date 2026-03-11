<?php

namespace App\Http\Controllers;

use App\Http\Requests\Registration\SaveRegistrationRequest;
use App\Models\User;
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

        User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => $validatedData['password'],
        ]);

        // Redirect with the success message
        return redirect()->route('registration.show')->with('success', 'true');
    }
}
