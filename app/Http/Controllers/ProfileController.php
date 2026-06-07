<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password as PasswordRule;

class ProfileController extends Controller
{
    /**
     * Отображение профиля пользователя
     */
    public function show()
    {
        return view('pages.profile', [
            'user' => Auth::user(),
        ]);
    }

    /**
     * Отображение формы редактирования профиля
     */
    public function edit()
    {
        return view('pages.profile-edit', [
            'user' => Auth::user(),
        ]);
    }

    /**
     * Обновление профиля пользователя
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        return redirect()->route('profile.show')
            ->with('success', 'Профиль обновлён.');
    }

    /**
     * Обновление пароля пользователя
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', PasswordRule::defaults()],
        ]);

        $user = Auth::user();
        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('profile.show')
            ->with('success', 'Пароль изменён.');
    }
}
