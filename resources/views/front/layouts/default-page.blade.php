@extends('layouts.master')

@section('head')
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="/materialize/css/materialize.min.css"  media="screen,projection"/>
    <link rel="stylesheet" href="/font-awesome/css/font-awesome.min.css">
    <link type="text/css" rel="stylesheet" href="/css/front_style.css"  media="screen,projection"/>
    @yield('head')
@endsection

@section('body')

    @include('front.partial.navbar')
    @include('front.partial.sidenav')

    <div class="row">
        <div class="col l12">
            @yield('content')
        </div>
    </div>

    <!-- End of page, load scripts -->
    <script type="text/javascript" src="/js/jquery-3.2.1.min.js"></script>
    <script type="text/javascript" src="/materialize/js/materialize.min.js"></script>
    <script type="text/javascript">

        $('.button-collapse').sideNav({
                menuWidth: 300, // Default is 240
                edge: 'left', // Choose the horizontal origin
                closeOnClick: false, // Closes side-nav on <a> clicks, useful for Angular/Meteor
                draggable: true // Choose whether you can drag to open on touch screens
            }
        );

        $(document).ready(function(){
            // the "href" attribute of the modal trigger must specify the modal ID that wants to be triggered
            $('#modal_logout').modal();
        });
    </script>

@endsection
