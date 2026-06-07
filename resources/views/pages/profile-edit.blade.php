@extends('layouts.app')

@section('title', 'Редактирование профиля - ' . config('app.name'))

@section('content')
<div class="back-wrapper">
    <div class="back-wrapper-inner pt-120 pb-120">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    
                    <div class="back-to-profile mb-4">
                        <a href="{{ route('profile.show') }}" class="btn btn-link">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                            Назад к профилю
                        </a>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- Форма редактирования профиля -->
                    <div class="profile-edit-box mb-4">
                        <h2 class="mb-4">Редактировать профиль</h2>
                        
                        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
                            <div class="form-group mb-3">
                                <label for="avatar" class="d-block mb-2">Фото профиля</label>
                                @if($user->avatar)
                                    <div class="mb-2">
                                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="avatar" style="max-height: 100px; border-radius: 5px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                                    </div>
                                @endif
                                <input type="file" 
                                       class="form-control @error('avatar') is-invalid @enderror" 
                                       id="avatar" 
                                       name="avatar" 
                                       accept="image/*"
                                       style="padding: 12px 20px; line-height: 1.5;">
                                @error('avatar')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="name">Имя</label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name', $user->name) }}" 
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="email">Email</label>
                                <input type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email', $user->email) }}" 
                                       required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @if(!$user->hasVerifiedEmail())
                                    <small class="form-text text-muted">
                                        Не забудьте подтвердить новый email, если измените его.
                                    </small>
                                @endif
                            </div>

                            <div class="text-end">
                                <button type="submit" class="back-btn">Сохранить изменения</button>
                            </div>
                        </form>
                    </div>

                    <!-- Форма изменения пароля -->
                    <div class="profile-edit-box">
                        <h2 class="mb-4">Изменить пароль</h2>
                        
                        <form method="POST" action="{{ route('profile.password') }}">
                            @csrf
                            @method('PUT')
                            
                            <div class="form-group mb-3">
                                <label for="current_password">Текущий пароль</label>
                                <input type="password" 
                                       class="form-control @error('current_password') is-invalid @enderror" 
                                       id="current_password" 
                                       name="current_password" 
                                       required>
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="password">Новый пароль</label>
                                <input type="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       id="password" 
                                       name="password" 
                                       required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="password_confirmation">Подтвердите новый пароль</label>
                                <input type="password" 
                                       class="form-control" 
                                       id="password_confirmation" 
                                       name="password_confirmation" 
                                       required>
                            </div>

                            <div class="text-end">
                                <button type="submit" class="back-btn">Изменить пароль</button>
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
.profile-edit-box {
    background: #fff;
    padding: 40px;
    border-radius: 10px;
    box-shadow: 0 0 20px rgba(0,0,0,0.1);
}

.profile-edit-box h2 {
    font-size: 24px;
    font-weight: 700;
    color: #1a1a1a;
}

.form-control {
    height: 50px;
    padding: 0 20px;
    font-size: 14px;
    border: 1px solid #e0e0e0;
    border-radius: 5px;
}

.form-control:focus {
    border-color: #f84e77;
    box-shadow: 0 0 0 0.2rem rgba(248, 78, 119, 0.25);
}

.back-to-profile a {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: #666;
    text-decoration: none;
}

.back-to-profile a:hover {
    color: #f84e77;
}

.back-to-profile svg {
    width: 16px;
    height: 16px;
}
</style>
@endpush
@endsection
