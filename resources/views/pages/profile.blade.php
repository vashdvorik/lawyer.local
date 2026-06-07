@extends('layouts.app')

@section('title', 'Мой профиль - ' . config('app.name'))

@section('content')
<div class="back-wrapper">
    <div class="back-wrapper-inner course-archive-wrapper">
        
        <!--================= Profile Section Start Here ================= -->
        <div class="profile-top back__course__area pt-120 pb-120">
            <div class="container">
                @if(session('success'))
                    <div class="alert alert-success mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="row">
                    <div class="col-lg-4">
                        <div class="profile-avatar">
                            <img src="{{ asset('assets/images/profile/1.png') }}" alt="profile">
                        </div>
                    </div>
                    <div class="col-lg-8 pl-50 md-pl-15 md-mt-60">
                        <ul class="user-section">
                            <li class="user">
                                <span class="name">{{ $user->name }}</span>
                                <em>{{ $user->email }}</em>
                            </li>
                        </ul>
                        
                        <h3>Добро пожаловать!</h3>
                        <p>
                            Ваши курсы появятся здесь после того, как куратор откроет к ним доступ. На этой странице вы также можете управлять своим профилем.
                        </p>
                        
                        <div class="mt-4">
                            <a href="{{ route('profile.edit') }}" class="back-btn">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg>
                                Редактировать профиль
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--================= Profile Section End Here ================= -->

    </div>
</div>

@push('styles')
<style>
.profile-avatar img {
    width: 100%;
    border-radius: 10px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
}

.user-section {
    list-style: none;
    padding: 0;
    margin-bottom: 30px;
}

.user-section li {
    padding: 10px 0;
    border-bottom: 1px solid #eee;
}

.user-section li:last-child {
    border-bottom: none;
}

.user-section .user .name {
    display: block;
    font-size: 28px;
    font-weight: 700;
    color: #1a1a1a;
    margin-bottom: 5px;
}

.user-section .user em {
    display: block;
    color: #666;
    font-style: normal;
}

.user-section em {
    color: #f84e77;
    font-style: normal;
}

.back-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.back-btn svg {
    width: 16px;
    height: 16px;
}
</style>
@endpush
@endsection
