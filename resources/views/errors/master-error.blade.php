@extends('base')

@section('head')
    <link rel="stylesheet" href="/css/front/errors.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="/materialize/css/materialize.min.css"  media="screen,projection"/>
    @yield('head-content')
@endsection

@section('body')
    @yield('content')
@endsection