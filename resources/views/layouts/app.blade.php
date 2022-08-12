<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" style="height:100%;">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="{{ config('APP_COMPANY') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{config('app.name')}}</title>
    <link rel="stylesheet" type="text/css" href="{{mix('dist/css/app.css')}}">
    @yield('css')
</head>
@php
    $userName = (Auth::check())?Auth::user()->name:'';
@endphp
<body class="hold-transition sidebar-mini h-100">
    @if(Auth::check())
    <div class="wrapper h-100" id="app">
        <nav class="main-header navbar navbar-expand bg-white navbar-light border-bottom">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
                </li>
            </ul>
        </nav>
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <a href="/" class="brand-link">
                <img src="https://via.placeholder.com/150x150" alt="{{config('app.name')}}" class="brand-image elevation-3" style="opacity: .8">
                <span class="brand-text font-weight-light">{{config('app.name')}}</span>
            </a>

            <div class="sidebar">
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="image">
                        <span class="fa-stack text-primary">
                            <i class="fas fa-circle fa-stack-2x fa-inverse"></i>
                            <i class="fas fa-user fa-stack-1x"></i>
                        </span>
                        <!-- <img src="https://via.placeholder.com/160x160" class="img-circle elevation-2" alt="Usuario"> -->
                    </div>
                    <div class="info">
                        <a href="#" class="d-block">{{$userName}}</a>
                    </div>
                </div>
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar nav-child-indent flex-column" data-widget="treeview" role="menu" data-accordion="false">
                        {!! \Csgt\Utils\Menu::menu() !!}
                        <li class="nav-header">USUARIO</li>
                        <li class="nav-item">
                            <a href="/profile" class="nav-link">
                                <i class="nav-icon fa fa-user"></i>
                                <p>Perfil</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <form id="logout-form" action="{{ route('logout') }}" method="POST">
                                @csrf
                                <a href='#' onclick='this.parentNode.submit(); return false;' class="nav-link">
                                    <i class="nav-icon fa fa-sign-out-alt"></i>
                                    <p>Cerrar sesión</p>
                                </a>
                            </form>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>
        <div class="content-wrapper" style="min-height: 395px;">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0 text-dark">@yield('title')</h1>
                        </div>
                        <div class="col-sm-6">
                            @yield('breadcrumb')
                        </div>
                    </div>
                </div>
            </div>
            <section class="content">
                <div class="container-fluid">
                    @yield('content')
                </div>
            </section>
        </div>

        <footer class="main-footer">
            <strong>Copyright &copy; {{date('Y')==2022?'2022':'2022-' . date('Y')}} <a href="{{env('APP_COMPANY_URL')}}">{{env('APP_COMPANY')}}</a>.</strong>
            Todos los derechos reservados.
            <div class="float-right d-none d-sm-inline-block">
                <b>Versión</b> {{ENV('APP_VERSION')}}
            </div>
        </footer>
    </div>
    @else
    <style>
        html, body {
            height: 100% !important;
        }
    </style>
    <div class="row h-100 justify-content-center align-items-center" id="app">
        @yield('content')
    </div>
    @endif

    <script src="{{ mix('dist/js/app.js') }}"></script>
    <script src="{{ mix('dist/js/manifest.js') }}"></script>
    <script src="{{ mix('dist/js/vendor.js') }}"></script>
    @yield('javascript')
    <script>
        $(document).ready(function() {
            var navParent = $('.nav-item .active').closest('.has-treeview');
            navParent.addClass('menu-open');
            navParent.children('.nav-link').addClass('active');
            @if(session()->has('message'))
                @if(session()->get('type') == 'danger')
                toastr.error('{{ session()->get('message') }}')
                @else
                toastr.{{ session()->get('type')}}(' {{ session()->get('message') }} ')
                @endif
            @endif
        });
    </script>
</body>
</html>
