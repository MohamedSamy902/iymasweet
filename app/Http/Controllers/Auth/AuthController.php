<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;

class AuthController extends Controller
{
    public function showLogin(): Response
    {
        return Inertia::render('Auth/Login');
    }

    public function login(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'phone' => ['required', 'string'],
            'password' => ['required', 'string'],
            'remember' => ['boolean'],
        ]);

        $phone = preg_replace('/\D+/', '', $data['phone']) ?? $data['phone'];

        if (! Auth::attempt(['phone' => $phone, 'password' => $data['password'], 'is_active' => true], $request->boolean('remember'))) {
            return back()->withErrors(['phone' => t('errors.invalid_phone', 'بيانات الدخول مش مظبوطة')])->onlyInput('phone');
        }

        $request->session()->regenerate();
        Auth::user()?->update(['last_login_at' => now()]);

        return redirect()->intended(route('account.index'));
    }

    public function showRegister(): Response
    {
        return Inertia::render('Auth/Register');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:20', 'unique:users,phone'],
            'email' => ['nullable', 'email', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $phone = preg_replace('/\D+/', '', $data['phone']) ?? $data['phone'];
        if (! preg_match('/^(01)[0-25]\d{8}$/', $phone)) {
            return back()->withErrors(['phone' => t('errors.invalid_phone', 'رقم الموبايل مش مظبوط')])->withInput();
        }

        $user = User::query()->create([
            'name' => $data['name'],
            'phone' => $phone,
            'email' => $data['email'] ?? null,
            'password' => Hash::make($data['password']),
            'is_active' => true,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('account.index')->with('success', t('home.welcome', 'أهلاً بيكي يا قمر 🍩'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
