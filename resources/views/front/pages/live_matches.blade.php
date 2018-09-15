@extends('front.layouts.default-page')

@section('head-content')
    <title>{{ trans('front.live_matches') }}</title>
    <link rel="stylesheet" href="/css/front/live_matches-style.css">
    <!-- Open Graph data -->
    <meta property="og:title" content="{{ trans('front.live_matches') }}" />
    <meta property="og:type" content="website" />
    <meta itemprop="image" content="/images/live_games_thumb.jpg">
    <meta property="og:description" content="{{ trans('front.live_matches_desc') }}" />
    <meta property="og:site_name" content="{{ config('app.name') }}" />
@endsection

@section('content')
    <div class="container">
        <h1 class="hide-on-med-and-down">{{ trans('front.live_matches') }}</h1>

        <div id="live_matches"></div>
        @include('front.partial.live_matches_template')

    </div>
@endsection

@section('scripts')
    <script src="/js/front/live_matches-scripts.js"></script>
@endsection
