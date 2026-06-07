@extends('layouts.app')

@section('title', 'Главная - ' . config('app.name'))
@section('description', 'Профессиональные юридические услуги')

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
                            <span>Добро пожаловать</span>
                            <h1 class="hero3__title">Профессиональные<br>юридические услуги</h1>
                            <p class="hero3__paragraph">Опытные юристы помогут решить любые правовые вопросы<br>для вашего бизнеса и личных дел</p>
                            <a href="#services" class="hero3-btn">Узнать больше</a>
                        </div>
                    </div>
                    <div class="col-xxl-6 col-xl-6 col-lg-6 col-md-6 col-sm-12">
                        <div class="hero3__image">
                            <img class="hero3__image-1" src="{{ asset('assets/images/banner2/normal-image/01.png') }}" alt="Юридические услуги">
                            <img class="hero3__image-2" src="{{ asset('assets/images/banner2/normal-image/02.png') }}" alt="Консультации">
                            <div class="hero3__image-course">
                                <div class="hero3__image-course-1">
                                    <span>15+</span>
                                </div>
                                <div class="hero3__image-course-2">
                                    <span>Лет опыта</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Services Form Section --}}
        <div class="back-home3-banner-form form__area p-relative" id="services">
            <div class="container form__width">
                <div class="form__wrapper">
                    <div class="form__wrapper-1 text-center">
                        <h3 class="form__wrapper-1--title">Найдите нужную услугу</h3>
                        <p class="form__wrapper-1--description">Мы предлагаем широкий спектр юридических услуг для вашего бизнеса</p>
                    </div>
                    <div class="form__wrapper-2 mt-10">
                        <div class="form__wrapper-2--container">
                            <select class="from-control">
                                <option>Выберите услугу</option>
                                <option>Корпоративное право</option>
                                <option>Договоры</option>
                                <option>Судебные споры</option>
                                <option>Консультации</option>
                            </select>
                        </div>
                        <div class="form__wrapper-2--container2">
                            <select class="from-control">
                                <option>Область права</option>
                                <option>Гражданское</option>
                                <option>Административное</option>
                                <option>Уголовное</option>
                                <option>Налоговое</option>
                            </select>
                        </div>
                        <div class="form__wrapper-2--container3">
                            <select class="from-control">
                                <option>Срочность</option>
                                <option>В течение дня</option>
                                <option>В течение недели</option>
                                <option>Плановая</option>
                            </select>
                        </div>
                        <div class="form__wrapper-2--container4">
                            <button>Найти услугу</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Top Services Section --}}
        <div class="category3__area pt-200 pb-95">
            <div class="container category3__width pt-40">
                <div class="row d-flex align-items-end">
                    <div class="col-lg-8">
                        <div class="category3__content pb-35 md-pb-0">
                            <span>Наши услуги</span>
                            <h2 class="category3__title">Добро пожаловать в центр<br>юридических услуг</h2>
                        </div>
                    </div>
                    <div class="col-lg-4 text-right pb-60">
                        <div class="category3__btn">
                            <a href="#">Все услуги <i class="arrow_right"></i></a>
                        </div>
                    </div>
                    
                    <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6">
                        <div class="category3__wrapper mb-25">
                            <div class="category3__wrapper-1">
                                <img src="{{ asset('assets/images/category3/icon/01.svg') }}" alt="Иконка">
                            </div>
                            <div class="category3__wrapper-2">
                                <div class="category3__wrapper-2--one">
                                    <h4><a href="#">Корпоративное право</a></h4>
                                    <p>Консультации по бизнесу</p>
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
                                    <h4><a href="#">Договоры</a></h4>
                                    <p>Составление и проверка</p>
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
                                    <h4><a href="#">Судебные споры</a></h4>
                                    <p>Представительство в суде</p>
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
                                    <h4><a href="#">Консультации</a></h4>
                                    <p>Правовая поддержка</p>
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
                                    <h4><a href="#">Налоговое право</a></h4>
                                    <p>Оптимизация налогов</p>
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
                                    <h4><a href="#">Недвижимость</a></h4>
                                    <p>Сделки с недвижимостью</p>
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
                                    <h4><a href="#">Семейное право</a></h4>
                                    <p>Раздел имущества, алименты</p>
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
                                    <h4><a href="#">Трудовое право</a></h4>
                                    <p>Защита прав работников</p>
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
                                    <h4><a href="#">Интеллектуальная собственность</a></h4>
                                    <p>Защита авторских прав</p>
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
