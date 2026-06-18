<footer id="back-footer" class="back-footer back-footer-dark back-footer-dark2">
    <div class="footer-top">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 md-mb-30">
                    <div class="footer-widget footer-widget-1">
                        <div class="footer-logo white">
                            <a href="{{ route('home') }}" class="logo-text">
                                <img src="{{ asset('assets/images/logo-light.png') }}" alt="logo">
                            </a>
                        </div>
                        <h5 class="footer-subtitle">{{ config('app.name') }} - единая образовательная платформа для студентов и преподавателей юридического факультета ПГУ</h5>
                        <ul class="footer-address">
                            <li class="back-address">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="30" viewBox="0 0 24 24" fill="none" stroke="#f84e77" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-map-pin">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                    <circle cx="12" cy="10" r="3"></circle>
                                </svg>
                                Юридический факультет ПГУ
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="col-lg-3 md-mb-30">
                    <div class="footer-widget footer-widget-2">
                        <h3 class="footer-title">Материалы</h3>
                        <div class="footer-menu">
                            <ul>
                                <li><span>Лекции</span></li>
                                <li><span>Методические материалы</span></li>
                                <li><span>Задания</span></li>
                                <li><span>Нормативные документы</span></li>
                                <li><span>Полезные ссылки</span></li>
                                <li><span>Материалы курса</span></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 md-mb-30">
                    <div class="footer-widget footer-widget-2">
                        <h3 class="footer-title">Платформа</h3>
                        <div class="footer-menu">
                            <ul>
                                <li><span>Для студентов</span></li>
                                <li><span>Для преподавателей</span></li>
                                <li><span>Личный кабинет</span></li>
                                <li><span>Дисциплины</span></li>
                                <li><span>Учебные группы</span></li>
                                <li><span>Быстрый доступ</span></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3">
                    <div class="footer-widget footer-widget-3">
                        <h3 class="footer-title">Доступ</h3>
                        <div class="footer-menu">
                            <ul>
                                @guest
                                    <li><a href="{{ route('login') }}">Вход</a></li>
                                    <li><a href="{{ route('signup') }}">Регистрация</a></li>
                                @else
                                    <li><a href="{{ route('profile.show') }}">Личный кабинет</a></li>
                                @endguest
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="copyright">
        <div class="container">
            <div class="back-copy-left">
                &copy; {{ date('Y') }} {{ config('app.name') }}. Все права защищены.
            </div>
        </div>
    </div>
</footer>
