<?php

declare(strict_types=1);

namespace App\Http\Controllers;

class PageController extends Controller
{
    /**
     * Главная страница сайта
     */
    public function index()
    {
        return view('pages.index');
    }

    /**
     * Страница входа для пользователей
     */
    public function login()
    {
        return view('pages.login');
    }

    /**
     * Страница регистрации
     */
    public function signup()
    {
        return view('pages.signup');
    }
}
