<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Hash;
use Session;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class CustomAuthController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            return redirect()->route('mess.index');
        }
        return view('signin');
    }

    public function customSignin(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            Auth::user()->update([
                'last_login_at' => now(),
                'last_login_ip' => $request->ip(),
            ]);
            // Super admin goes to admin dashboard
            if (Auth::user()->is_super_admin) {
                return redirect()->route('admin.dashboard')->withSuccess('Welcome, Super Admin.');
            }
            return redirect()->intended(route('mess.index'))->withSuccess('Signed in');
        }

        return redirect('signin')->withErrors('These credentials do not match our records.');
    }

    public function registration()
    {
        if (Auth::check()) {
            return redirect()->route('mess.index');
        }
        return view('register');
    }

    public function customRegister(Request $request)
    {
        $request->validate([
            'name'     => 'required|min:3',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'phone'    => 'nullable|string|max:20',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'phone'    => $request->phone,
        ]);

        Auth::login($user);

        return redirect()->route('mess.index');
    }

    public function dashboard()
    {
        if (Auth::check()) {
            return redirect()->route('mess.index');
        }
        return redirect('signin')->withErrors('You are not allowed to access');
    }

    public function signOut()
    {
        Session::flush();
        Auth::logout();
        return redirect('signin');
    }
}
