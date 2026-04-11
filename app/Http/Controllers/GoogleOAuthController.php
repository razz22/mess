<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleOAuthController extends Controller
{
    private function configureGoogle(): bool
    {
        $settings = SystemSetting::instance();

        if (! $settings->google_client_id || ! $settings->google_client_secret) {
            return false;
        }

        config([
            'services.google.client_id'     => $settings->google_client_id,
            'services.google.client_secret' => $settings->google_client_secret,
            'services.google.redirect'      => route('auth.google.callback'),
        ]);

        return true;
    }

    public function redirect()
    {
        if (! $this->configureGoogle()) {
            return redirect('signin')->withErrors('Google login is not configured. Please contact the administrator.');
        }

        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        if (! $this->configureGoogle()) {
            return redirect('signin')->withErrors('Google login is not configured.');
        }

        try {
            $googleUser = Socialite::driver('google')
                ->setHttpClient(new \GuzzleHttp\Client(['verify' => false]))
                ->stateless()
                ->user();
        } catch (\Exception $e) {
            return redirect('signin')->withErrors('Google login failed: ' . $e->getMessage());
        }

        // Find or create the user
        $user = User::firstOrCreate(
            ['email' => $googleUser->getEmail()],
            [
                'name'              => $googleUser->getName(),
                'google_id'         => $googleUser->getId(),
                'password'          => bcrypt(str()->random(32)),
                'email_verified_at' => now(),
            ]
        );

        // If user existed but didn't have google_id, set it
        if (! $user->google_id) {
            $user->update(['google_id' => $googleUser->getId()]);
        }

        Auth::login($user, true);

        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => request()->ip(),
        ]);

        if ($user->is_super_admin) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('mess.index');
    }
}
