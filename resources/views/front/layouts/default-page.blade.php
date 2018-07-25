@extends('base')

@section('head')

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="/materialize/css/materialize.min.css"  media="screen,projection"/>
    <link type="text/css" rel="stylesheet" href="/css/front/default-page-style.css"  media="screen,projection"/>
    <link type="text/css" rel="stylesheet" href="/css/front/navbar-style.css"  media="screen,projection"/>
    <link type="text/css" rel="stylesheet" href="/css/front/footer-style.css"  media="screen,projection"/>
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400&amp;subset=latin-ext" rel="stylesheet">
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

@endsection
