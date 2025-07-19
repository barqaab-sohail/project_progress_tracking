<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    
    <!-- Themify Icons for the menu toggle -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lykmapipo/themify-icons@0.1.2/css/themify-icons.css">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    
    <style>
        body {
            overflow-x: hidden;
        }
        #app {
            display: flex;
            min-height: 100vh;
        }
        .main-content {
            flex: 1;
            transition: margin-left 0.3s;
        }
        .sidebar {
            width: 250px;
            min-height: 100vh;
            background: #343a40;
            color: white;
            transition: all 0.3s;
            position: relative;
            z-index: 1;
        }
        .sidebar.collapsed {
            margin-left: -250px;
        }
        .sidebar-nav {
            padding: 20px 0;
        }
        .sidebar-nav .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 10px 15px;
            margin: 5px 0;
        }
        .sidebar-nav .nav-link:hover {
            color: white;
            background: rgba(255, 255, 255, 0.1);
        }
        .sidebar-nav .nav-link i {
            margin-right: 10px;
        }
        .navbar {
            box-shadow: 0 2px 4px rgba(0,0,0,.1);
        }
        .user-avatar {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            margin-right: 10px;
            object-fit: cover;
        }
        /* Adjust main content when sidebar is hidden */
        .auth-page .main-content {
            margin-left: 0 !important;
        }

        .ti-menu {
            font-size: 1.5rem;
            vertical-align: middle;
        }
        #sidebarToggle {
            border: none;
            background: transparent;
            outline: none;
            cursor: pointer;
        }
        #sidebarToggle:hover {
            opacity: 0.8;
        }
    </style>
</head>
<body class="@if(Route::is('login') || Route::is('register')) auth-page @endif">
    <div id="app">
        @auth
        @include('layouts.left_nav')
        @endauth

        <!-- Main Content -->
        <div class="main-content" id="main-content">
            <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
                <div class="container-fluid">
                    @auth
                    <button class="btn btn-link text-dark p-0 mr-3" id="sidebarToggle">
                        <i class="ti-menu" style="font-size: 1.5rem; margin-right: 20px;"></i>
                    </button>
                    @endauth
                    <a class="navbar-brand" href="{{ url('/home') }}">
                        {{ config('app.name', 'Laravel') }}
                    </a>
                    
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav ms-auto">
                            @guest
                                @if (Route::has('login'))
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                    </li>
                                @endif

                                @if (Route::has('register'))
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                    </li>
                                @endif
                            @else
                                <li class="nav-item dropdown">
                                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                        <img src="https://via.placeholder.com/30" class="user-avatar" alt="User Avatar">
                                        {{ Auth::user()->name }}
                                    </a>

                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                        <a class="dropdown-item" href="{{ route('logout') }}"
                                           onclick="event.preventDefault();
                                                         document.getElementById('logout-form').submit();">
                                            {{ __('Logout') }}
                                        </a>

                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                            @csrf
                                        </form>
                                    </div>
                                </li>
                            @endguest
                        </ul>
                    </div>
                </div>
            </nav>

            <main class="py-4 container-fluid">
                @yield('content')
            </main>
        </div>
    </div>

    @auth
    <script>
        // Toggle sidebar (only for authenticated users)
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            
            sidebar.classList.toggle('collapsed');
            
            if (sidebar.classList.contains('collapsed')) {
                mainContent.style.marginLeft = '0';
            } else {
                mainContent.style.marginLeft = '0px';
            }
        });

        // Handle AJAX page loading for sidebar links
        document.querySelectorAll('.sidebar-nav .nav-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const page = this.getAttribute('data-page');
                
                // You would typically make an AJAX call here to load the content
                console.log(`Loading page: ${page} via AJAX`);
                // Example AJAX call:
                /*
                fetch(`/load-page/${page}`)
                    .then(response => response.text())
                    .then(html => {
                        document.querySelector('main').innerHTML = html;
                    });
                */
            });
        });
    </script>
    @endauth
</body>
</html>