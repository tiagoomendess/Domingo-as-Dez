@extends('base')

@section('head')
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-138219681-1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'UA-138219681-1');
    </script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="/materialize/css/materialize.min.css"  media="screen,projection"/>
    <link type="text/css" rel="stylesheet" href="/css/front/default-page-style.css"  media="screen,projection"/>
    <link type="text/css" rel="stylesheet" href="/css/front/navbar-style.css"  media="screen,projection"/>
    <link type="text/css" rel="stylesheet" href="/css/front/footer-style.css"  media="screen,projection"/>
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400&amp;subset=latin-ext" rel="stylesheet">
    <!-- FAVICON-->
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#00aba9">
    <meta name="theme-color" content="#ffffff">
    <meta property="og:site_name" content="{{ config('app.name') }}" />
    <meta property="fb:app_id" content="153482715435840" />

    @yield('head-content')
@endsection

@section('body')

    @include('front.partial.navbar')

    @include('front.partial.sidenav')

    <main>
        @yield('content')
    </main>

    @include('front.partial.footer')

    <!-- End of page, load scripts -->
    <script type="text/javascript" src="/js/jquery-3.2.1.min.js"></script>
    <script type="text/javascript" src="/materialize/js/materialize.min.js"></script>
    <script type="text/javascript" src="/js/front/default-page-scripts.js"></script>

    @yield('scripts')

    @if(Session::has('popup_message'))
        @include('backoffice.partial.popup_message')
    @endif

@endsection
