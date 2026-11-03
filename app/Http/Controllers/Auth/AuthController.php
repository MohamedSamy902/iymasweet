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
        ], [
            'phone.required' => 'رقم الموبايل مطلوب',
            'password.required' => 'كلمة المرور مطلوبة',
        ], [
            'phone' => 'رقم الموبايل',
            'password' => 'كلمة المرور',
        ]);

        $phone = preg_replace('/\D+/', '', $data['phone']) ?? $data['phone'];

        if (! Auth::attempt(['phone' => $phone, 'password' => $data['password'], 'is_active' => true], $request->boolean('remember'))) {
            return back()->withErrors(['phone' => 'رقم الموبايل أو كلمة المرور غير صحيحة'])->onlyInput('phone');
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
        $request->merge([
            'email' => filled($request->input('email')) ? $request->input('email') : null,
            'phone' => preg_replace('/\D+/', '', (string) $request->input('phone')) ?: $request->input('phone'),
        ]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:20', 'unique:users,phone'],
            'email' => ['nullable', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8', Password::defaults()],
        ], [
            'name.required' => 'الاسم مطلوب',
            'phone.required' => 'رقم الموبايل مطلوب',
            'phone.unique' => 'رقم الموبايل مسجّل قبل كده',
            'email.email' => 'صيغة البريد الإلكتروني غير صحيحة',
            'email.unique' => 'البريد الإلكتروني مستخدم بالفعل',
            'password.required' => 'كلمة المرور مطلوبة',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق',
            'password.min' => 'كلمة المرور لازم تكون :min حروف على الأقل',
        ], [
            'name' => 'الاسم',
            'phone' => 'رقم الموبايل',
            'email' => 'البريد الإلكتروني',
            'password' => 'كلمة المرور',
        ]);

        $phone = (string) $data['phone'];
        if (! preg_match('/^(01)[0-25]\d{8}$/', $phone)) {
            return back()->withErrors(['phone' => t('errors.invalid_phone', 'ادخلي رقم موبايل مصري صحيح')])->withInput();
        }

        $user = User::query()->create([
            'name' => $data['name'],
            'phone' => $phone,
            'email' => $data['email'] ?: null,
            'password' => Hash::make($data['password']),
            'is_active' => true,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('account.index')->with('success', t('home.welcome', 'أهلاً بيك في Iyma Sweet'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
