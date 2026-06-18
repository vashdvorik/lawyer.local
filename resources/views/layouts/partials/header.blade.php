<header id="back-header" class="back-header back-header-three">
    <div class="menu-part">
        <div class="container">
            <div class="back-main-menu">
                <nav>
                    {{-- Menu Toggle btn --}}
                    <div class="menu-toggle">
                        <div class="logo">
                            <a href="{{ route('home') }}" class="logo-text">
                                <img src="{{ asset('assets/images/logo.png') }}" alt="logo">
                            </a>
                        </div>
                        <button type="button" id="menu-btn">
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                    </div>
                    
                    {{-- Menu Structure --}}
                    <div class="back-inner-menus">
                        <ul id="backmenu" class="back-menus back-sub-shadow">
                            <li class="{{ request()->routeIs('home') ? 'active' : '' }}">
                                <a href="{{ route('home') }}">Главная</a>
                            </li>
                        </ul>
                        
                        <div class="searchbar-part">
                            @guest
                                <div class="back-login">
                                    <a href="{{ route('login') }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-unlock">
                                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                            <path d="M7 11V7a5 5 0 0 1 9.9-1"></path>
                                        </svg>
                                        Вход
                                    </a>
                                </div>
                                <a href="{{ route('signup') }}" class="back-btn">Регистрация</a>
                            @else
                                <div class="back-login">
                                    <a href="{{ route('profile.show') }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user">
                                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                            <circle cx="12" cy="7" r="4"></circle>
                                        </svg>
                                        {{ Auth::user()->name }}
                                    </a>
                                </div>
                                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="back-btn" style="border: none; cursor: pointer;">Выход</button>
                                </form>
                            @endguest
                        </div>
                    </div>
                </nav>
            </div>
        </div>
    </div>
</header>
