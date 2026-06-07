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
                        <h5 class="footer-subtitle">{{ config('app.name') }} - профессиональные юридические услуги для вашего бизнеса</h5>
                        <ul class="footer-address">
                            <li class="back-address">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="30" viewBox="0 0 24 24" fill="none" stroke="#f84e77" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-map-pin">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                    <circle cx="12" cy="10" r="3"></circle>
                                </svg> 
                                г. Москва, ул. Примерная, д. 1<br>офис 101
                            </li>
                            <li>
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#f84e77" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-phone">
                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                                </svg>
                                <a href="tel:+74951234567">+7 (495) 123-45-67</a>
                            </li>
                            <li>
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#f84e77" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-mail">
                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                    <polyline points="22,6 12,13 2,6"></polyline>
                                </svg>
                                <a href="mailto:info@example.com">info@example.com</a>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <div class="col-lg-3 md-mb-30">
                    <div class="footer-widget footer-widget-2">
                        <h3 class="footer-title">Услуги</h3>
                        <div class="footer-menu">
                            <ul>
                                <li><a href="#">Корпоративное право</a></li>
                                <li><a href="#">Договоры</a></li>
                                <li><a href="#">Судебные споры</a></li>
                                <li><a href="#">Консультации</a></li>
                                <li><a href="#">Налоговое право</a></li>
                                <li><a href="#">Недвижимость</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 md-mb-30">
                    <div class="footer-widget footer-widget-2">
                        <h3 class="footer-title">Компания</h3>
                        <div class="footer-menu">
                            <ul>
                                <li><a href="#">О нас</a></li>
                                <li><a href="#">Наша команда</a></li>
                                <li><a href="#">Блог</a></li>
                                <li><a href="#">FAQ</a></li>
                                <li><a href="#">Карьера</a></li>
                                <li><a href="#">Контакты</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3">
                    <div class="footer-widget footer-widget-3">
                        <h3 class="footer-title">Подписка на новости</h3>
                        <div class="footer3__form">
                            <form action="#">
                                <input type="email" placeholder="Ваш email">
                                <button class="footer3__form-1">
                                    <i class="arrow_right"></i>
                                </button>
                            </form>
                        </div>
                        <h6 class="back-follow-us">Мы в соцсетях</h6>
                        <ul class="social-links">
                            <li><a href="#"><span aria-hidden="true" class="social_facebook"></span></a></li>
                            <li><a href="#"><span aria-hidden="true" class="social_twitter"></span></a></li>
                            <li><a href="#"><span aria-hidden="true" class="social_linkedin"></span></a></li>
                        </ul>
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
