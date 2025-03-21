@extends('base')

@section('head')
    @if(!has_permission('disable_ads') && \Config::get('custom.adsense_enabled'))
        <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-3518000096682897"
                crossorigin="anonymous"></script>
    @endif
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="/css/material-icons.css" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="/materialize/css/materialize.min.css"  media="screen,projection"/>
    <link type="text/css" rel="stylesheet" href="/css/front/default-page-style.css"  media="screen,projection"/>
    <link type="text/css" rel="stylesheet" href="/css/front/navbar-style.css"  media="screen,projection"/>
    <link type="text/css" rel="stylesheet" href="/css/front/footer-style.css"  media="screen,projection"/>
    <!-- <link href="https://fonts.googleapis.com/css?family=Roboto:300,400&amp;subset=latin-ext" rel="stylesheet"> -->
    <link href="/css/roboto-font.css" rel="stylesheet">
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

    @if(\Config::get('custom.google_analytics_enabled'))
        @include('front.partial.google_analytics')
    @endif

    @yield('head-content')
@endsection

@section('body')
    @include('front.partial.navbar')

    @include('front.partial.sidenav')

    <main>
        @yield('content')
    </main>

    @include('front.partial.footer')

    @if(!has_permission('disable_ads') && \Config::get('custom.adsense_enabled'))
        @include('front.partial.footer_ads')
    @endif

    <!-- End of page, load scripts -->
    <script type="text/javascript" src="/js/jquery-3.2.1.min.js"></script>
    <script type="text/javascript" src="/materialize/js/materialize.min.js"></script>
    <script type="text/javascript" src="/js/front/default-page-scripts.js"></script>
    @yield('scripts')

    @if(Session::has('popup_message'))
        @include('backoffice.partial.popup_message')
    @endif

@endsection
