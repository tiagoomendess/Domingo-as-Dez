@extends('layouts.master')

@section('head')
    <title>@yield('title')</title>
@endsection

@section('body')

    @include('front.partial.navbar')
    @include('front.partial.sidenav')

    <div class="row">
        <div class="col l12">
            @yield('content')
        </div>
    </div>

@endsection
