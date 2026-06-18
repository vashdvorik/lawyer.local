@extends('layouts.app')

@section('title', 'Подтверждение email - ' . config('app.name'))

@section('content')
<div class="back-wrapper">
    <div class="back-wrapper-inner pt-120 pb-120">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="verify-email-box">
                        <div class="text-center mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#f84e77" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-mail">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                <polyline points="22,6 12,13 2,6"></polyline>
                            </svg>
                        </div>
                        
                        <h2 class="text-center mb-4">Подтвердите ваш email</h2>

                        <div class="alert alert-info">
                            <p class="mb-0">
                                Мы отправили письмо для подтверждения на ваш email. Если не получили, нажмите кнопку ниже. Также попробуйте проверить папку "Спам" или "Промоакции".
                            </p>
                        </div>

                        <form method="POST" action="{{ route('verification.send') }}" class="mt-4">
                            @csrf
                            <div class="text-center d-flex justify-content-center gap-3">
                                <button type="submit" class="back-btn">
                                    Отправить письмо повторно
                                </button>
                                <a href="{{ route('profile.show') }}" class="back-btn" style="text-decoration:none;">
                                    Войти в кабинет
                                </a>
                            </div>
                        </form>

                        <div class="text-center mt-4">
                            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-link">Выйти</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.verify-email-box {
    background: #fff;
    padding: 40px;
    border-radius: 10px;
    box-shadow: 0 0 20px rgba(0,0,0,0.1);
}
</style>
@endpush
@endsection
