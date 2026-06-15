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
                            <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : asset('assets/images/profile/1.png') }}" alt="profile">
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

                @if (isset($courses) && $courses->isNotEmpty())
                    <section class="student-courses">
                        <h2>Мои курсы</h2>
                
                        @foreach ($courses as $course)
                            <article class="student-course">
                                <h3>{{ $course->title }}</h3>
                
                                @foreach ($course->materials as $material)
                                    <div class="course-material">
                                        <h4>{{ $material->title }}</h4>
                                        <p class="course-material__description">{{ $material->description }}</p>
                
                                        @if ($material->external_url)
                                            <a
                                                href="{{ $material->external_url }}"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                            >
                                                Открыть материал
                                            </a>
                                        @endif
                
                                        @if ($material->file_path)
                                            <a
                                                href="{{ asset('storage/' . $material->file_path) }}"
                                                download="{{ $material->original_file_name }}"
                                            >
                                                Скачать документ
                                            </a>
                                        @endif
                                    </div>
                                @endforeach
                            </article>
                        @endforeach
                    </section>
                @endif
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

.student-courses {
    margin-top: 60px;
    border-top: 1px solid #eee;
    padding-top: 40px;
}

.student-courses h2 {
    font-size: 32px;
    margin-bottom: 30px;
}

.student-course {
    background: #fdfdfd;
    border: 1px solid #eee;
    border-radius: 10px;
    padding: 30px;
    margin-bottom: 30px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.02);
}

.student-course h3 {
    font-size: 24px;
    margin-bottom: 20px;
    color: #1a1a1a;
}

.course-material {
    background: #fff;
    border-left: 4px solid #f84e77;
    padding: 20px;
    margin-bottom: 15px;
    border-radius: 4px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.02);
}

.course-material h4 {
    font-size: 18px;
    margin-bottom: 10px;
}

.course-material__description {
    white-space: pre-line;
    color: #666;
    margin-bottom: 15px;
}

.course-material a {
    display: inline-block;
    margin-right: 15px;
    color: #fff;
    background: #f84e77;
    padding: 8px 20px;
    border-radius: 5px;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.course-material a:hover {
    background: #e03a63;
    color: #fff;
}
</style>
@endpush
@endsection
