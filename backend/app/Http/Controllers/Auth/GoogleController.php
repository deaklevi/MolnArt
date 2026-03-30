<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function handleGoogleCallback()
    {
        $frontendUrl = rtrim(config('app.frontend_url', env('FRONTEND_URL')), '/');

        try {
            $user = Socialite::driver('google')->stateless()->user();

            $params = [
                'g_name'  => $user->getName(),
                'g_email' => $user->getEmail(),
                'g_auth'  => 'success'
            ];

            // Visszairányítás a Nuxt confirm oldalára
            return redirect($frontendUrl . '/booking/confirm?' . http_build_query($params));

        } catch (\Exception $e) {
            return redirect($frontendUrl . "/booking?error=auth_failed");
        }
    }
}