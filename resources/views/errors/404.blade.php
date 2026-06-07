@extends('layouts.app')

@section('title', 'Страница не найдена - 404')

@section('content')
<div class="back-wrapper">
    <div class="back-wrapper-inner pt-120 pb-120">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6 text-center">
                    <h1 class="display-1">404</h1>
                    <h2 class="mb-4">Страница не найдена</h2>
                    <p class="mb-4">К сожалению, запрашиваемая страница не существует.</p>
                    <a href="{{ route('home') }}" class="back-btn">Вернуться на главную</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
