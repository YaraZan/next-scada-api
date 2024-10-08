<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AccountController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|min:5|max:255',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|max:20|min:10',
            'confirm_password' => 'required|max:20|min:10',
        ]);

        if ($request->password !== $request->confirm_password) {
            throw ValidationException::withMessages([
                'email' => ['The password field confirmation does not match.'],
            ]);
        }

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();

        return $user->createToken($request->device_name ?? 'default-device')->plainTextToken;
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        return $user->createToken($request->device_name)->plainTextToken;
    }
}
