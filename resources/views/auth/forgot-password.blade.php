@extends('layouts.app')

@section('title', 'Восстановление пароля - ' . config('app.name'))

@section('content')
<div class="back-wrapper">
    <div class="back-wrapper-inner pt-120 pb-120">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="forgot-password-box">
                        <h2 class="text-center mb-4">Восстановление пароля</h2>
                        
                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        <p class="text-center mb-4">
                            Забыли пароль? Не проблема! Введите ваш email, и мы отправим вам ссылку для сброса пароля.
                        </p>

                        <form method="POST" action="{{ route('password.email') }}">
                            @csrf
                            
                            <div class="form-group mb-3">
                                <label for="email">Email</label>
                                <input type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email') }}" 
                                       required 
                                       autofocus>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <button type="submit" class="back-btn w-100">
                                    Отправить ссылку для сброса пароля
                                </button>
                            </div>

                            <div class="text-center">
                                <p>Вспомнили пароль? <a href="{{ route('login') }}">Войти</a></p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.forgot-password-box {
    background: #fff;
    padding: 40px;
    border-radius: 10px;
    box-shadow: 0 0 20px rgba(0,0,0,0.1);
}
</style>
@endpush
@endsection
