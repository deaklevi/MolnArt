<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // 1. Validáljuk a beérkező adatokat
        $credentials = $request->validate([
            'user_name' => ['required', 'string'],
            'password' => ['required'],
        ]);

        // 2. Megpróbáljuk beléptetni
        if (!Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'user_name' => ['A megadott adatok nem egyeznek.'],
            ]);
        }

        // 3. Munkamenet (Session) generálása
        $request->session()->regenerate();

        return response()->json([
            'message' => 'Sikeres bejelentkezés',
            'user' => Auth::user()
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return response()->json(['message' => 'Kijelentkezve']);
    }
}