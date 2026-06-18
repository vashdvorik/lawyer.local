@extends('layouts.app')

@section('title', 'Главная - ' . config('app.name'))
@section('description', 'Онлайн-помощь студентам юридического факультета ПГУ')

@section('content')
<div class="back-wrapper">
    <div class="back-wrapper-inner">
        
        {{-- Banner Section --}}
        <div class="hero3__area p-relative pt-70">
            <div class="hero3__shape">
                <img class="hero3__shape-1" src="{{ asset('assets/images/banner2/shape/01.png') }}" alt="Banner shape image">
                <img class="hero3__shape-2" src="{{ asset('assets/images/banner2/shape/02.png') }}" alt="Banner shape image">
                <img class="hero3__shape-3" src="{{ asset('assets/images/banner2/shape/03.png') }}" alt="Banner shape image">
                <img class="hero3__shape-4" src="{{ asset('assets/images/banner2/shape/04.png') }}" alt="Banner shape image">
                <img class="hero3__shape-5" src="{{ asset('assets/images/banner2/shape/05.png') }}" alt="Banner shape image">
                <img class="hero3__shape-6" src="{{ asset('assets/images/banner2/shape/06.png') }}" alt="Banner shape image">
            </div>
            <div class="container hero3__width">
                <div class="row">
                    <div class="col-xxl-6 col-xl-6 col-lg-6 col-md-6 col-sm-12">
                        <div class="hero3__content pt-140 sm-pt-0">
                            <span>Юридический факультет ПГУ</span>
                            <h1 class="hero3__title">Онлайн-помощь<br>студентам</h1>
                            <p class="hero3__paragraph">Единая образовательная платформа для студентов и преподавателей<br>юридического факультета ПГУ</p>
                            <a href="{{ route('login') }}" class="hero3-btn">Войти</a>
                        </div>
                    </div>
                    <div class="col-xxl-6 col-xl-6 col-lg-6 col-md-6 col-sm-12">
                        <div class="hero3__image">
                            <img class="hero3__image-1" src="{{ asset('assets/images/banner2/normal-image/01.png') }}" alt="Учебные материалы">
                            <img class="hero3__image-2" src="{{ asset('assets/images/banner2/normal-image/02.png') }}" alt="Личный кабинет студента">
                            <div class="hero3__image-course">
                                <div class="hero3__image-course-1">
                                    <span>24/7</span>
                                </div>
                                <div class="hero3__image-course-2">
                                    <span>Доступ к материалам</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Top Services Section --}}
        <div class="category3__area pt-95 pb-95" id="services">
            <div class="container category3__width pt-40">
                <div class="row d-flex align-items-end">
                    <div class="col-lg-8">
                        <div class="category3__content pb-35 md-pb-0">
                            <span>Возможности платформы</span>
                            <h2 class="category3__title">Учебные материалы<br>удобно, быстро и в одном месте</h2>
                        </div>
                    </div>
                    <div class="col-lg-4 text-right pb-60">
                        <div class="category3__btn">
                            <a href="{{ route('signup') }}">Регистрация <i class="arrow_right"></i></a>
                        </div>
                    </div>
                    
                    <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6">
                        <div class="category3__wrapper mb-25">
                            <div class="category3__wrapper-1">
                                <img src="{{ asset('assets/images/category3/icon/01.svg') }}" alt="Иконка">
                            </div>
                            <div class="category3__wrapper-2">
                                <div class="category3__wrapper-2--one">
                                    <h4>Лекции</h4>
                                    <p>Материалы по дисциплинам</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6">
                        <div class="category3__wrapper mb-25">
                            <div class="category3__wrapper-1">
                                <img src="{{ asset('assets/images/category3/icon/02.svg') }}" alt="Иконка">
                            </div>
                            <div class="category3__wrapper-2">
                                <div class="category3__wrapper-2--one">
                                    <h4>Методички</h4>
                                    <p>Рекомендации преподавателей</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6">
                        <div class="category3__wrapper mb-25">
                            <div class="category3__wrapper-1">
                                <img src="{{ asset('assets/images/category3/icon/03.svg') }}" alt="Иконка">
                            </div>
                            <div class="category3__wrapper-2">
                                <div class="category3__wrapper-2--one">
                                    <h4>Задания</h4>
                                    <p>Учебные и практические работы</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6">
                        <div class="category3__wrapper mb-25">
                            <div class="category3__wrapper-1">
                                <img src="{{ asset('assets/images/category3/icon/04.svg') }}" alt="Иконка">
                            </div>
                            <div class="category3__wrapper-2">
                                <div class="category3__wrapper-2--one">
                                    <h4>Документы</h4>
                                    <p>Акты и правовые источники</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6">
                        <div class="category3__wrapper mb-25">
                            <div class="category3__wrapper-1">
                                <img src="{{ asset('assets/images/category3/icon/05.svg') }}" alt="Иконка">
                            </div>
                            <div class="category3__wrapper-2">
                                <div class="category3__wrapper-2--one">
                                    <h4>Полезные ссылки</h4>
                                    <p>Ресурсы для работы</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6">
                        <div class="category3__wrapper mb-25">
                            <div class="category3__wrapper-1">
                                <img src="{{ asset('assets/images/category3/icon/06.svg') }}" alt="Иконка">
                            </div>
                            <div class="category3__wrapper-2">
                                <div class="category3__wrapper-2--one">
                                    <h4>Личный кабинет</h4>
                                    <p>Доступ к своим дисциплинам</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6">
                        <div class="category3__wrapper mb-25">
                            <div class="category3__wrapper-1">
                                <img src="{{ asset('assets/images/category3/icon/07.svg') }}" alt="Иконка">
                            </div>
                            <div class="category3__wrapper-2">
                                <div class="category3__wrapper-2--one">
                                    <h4>Для преподавателей</h4>
                                    <p>Размещение учебных материалов</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6">
                        <div class="category3__wrapper mb-25">
                            <div class="category3__wrapper-1">
                                <img src="{{ asset('assets/images/category3/icon/08.svg') }}" alt="Иконка">
                            </div>
                            <div class="category3__wrapper-2">
                                <div class="category3__wrapper-2--one">
                                    <h4>Для студентов</h4>
                                    <p>Быстрый доступ к материалам.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6">
                        <div class="category3__wrapper mb-25">
                            <div class="category3__wrapper-1">
                                <img src="{{ asset('assets/images/category3/icon/09.svg') }}" alt="Иконка">
                            </div>
                            <div class="category3__wrapper-2">
                                <div class="category3__wrapper-2--one">
                                    <h4>Единая платформа</h4>
                                    <p>Учёба и материалы в одном месте</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
