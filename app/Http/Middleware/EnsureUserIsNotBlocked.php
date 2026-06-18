<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsNotBlocked
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->isBlocked()) {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $message = 'Ваш аккаунт заблокирован. Обратитесь к администратору.';

            if ($request->expectsJson()) {
                return response()->json(['message' => $message], 403);
            }

            $loginUrl = $request->is('admin*')
                ? url('/admin/login')
                : route('login');

            return redirect()->to($loginUrl)->with('error', $message);
        }

        return $next($request);
    }
}
