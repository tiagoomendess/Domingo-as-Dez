@extends('front.layouts.default-page')

@section('head-content')
    <title>{{ trans('front.live_matches') }}</title>
    <link rel="stylesheet" href="/css/front/live_matches-style.css">
@endsection

@section('content')
    <div class="container">
        <h1>{{ trans('front.live_matches') }}</h1>

        <div id="live_matches"></div>
        @include('front.partial.live_matches_template')

    </div>
@endsection

@section('scripts')
    <script src="/js/front/live_matches-scripts.js"></script>
@endsection
