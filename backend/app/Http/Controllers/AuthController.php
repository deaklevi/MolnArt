<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class AuthController extends Controller
{
    public function index() {
        // Betöltjük a szolgáltatásokat ÉS a foglalásokat az ügyfelekkel együtt
        $users = User::with([
            'services', 
            'appointments.customer',
            'reviews',
            'customers'
        ])->get();

        return UserResource::collection($users);
    }

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

    public function updateProfile(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'user_name'   => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
            'avatar'      => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ]);

        $user->user_name = $validated['user_name'];
        $user->description = $validated['description'];

        if ($request->hasFile('avatar')) {
            $disk = Storage::disk('public');

            // Itt javítottuk: profile_image-et használunk avatar helyett
            if ($user->profile_image && $disk->exists(str_replace('/storage/', '', $user->profile_image))) {
                $disk->delete(str_replace('/storage/', '', $user->profile_image));
            }

            $path = $request->file('avatar')->store('avatars', 'public');

            // FONTOS: Az adatbázis mezőnevedhez igazítjuk:
            $user->profile_image = '/storage/' . $path;
        }

        $user->save();

        return response()->json([
            'message' => 'Profil sikeresen frissítve!',
            'user'    => $user
        ]);
    }

    public function syncServices(Request $request) 
    {
        $request->validate([
            'service_ids' => 'present|array',
            'service_ids.*' => 'exists:services,id'
        ]);
    
        $user = $request->user();
        
        // A sync() törli a régi kapcsolatokat és csak azokat teszi be, amik a tömbben vannak
        $user->services()->sync($request->service_ids);
        
        return response()->json([
            'message' => 'Szolgáltatások sikeresen frissítve!',
            'services' => $user->services()->get()
        ]);
    }
}