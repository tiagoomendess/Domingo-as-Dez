@extends('front.layouts.default-page')

@section('head-content')
    <title>{{ trans('front.live_matches') }}</title>
    <link rel="stylesheet" href="/css/front/live_matches-style.css">
    <!-- Open Graph data -->
    <meta property="og:title" content="{{ trans('front.live_matches') }}"/>
    <meta property="og:type" content="website"/>
    <meta property="og:image" content="{{ url('/images/live_games_thumb.jpg') }}">
    <meta property="og:description" content="{{ trans('front.live_matches_desc') }}"/>
    <meta property="og:site_name" content="{{ config('app.name') }}"/>
@endsection

@section('content')
    <div class="container">
        <h1 class="hide-on-med-and-down">{{ trans('front.live_matches') }}</h1>

        <div class="row hide-on-large-only">
            <div class="container">
                <div class="col s12">
                    <div style="margin-top: .5rem">
                        <!-- Live Matches Horizontal -->
                        <ins class="adsbygoogle"
                             style="display:block"
                             data-ad-client="ca-pub-3518000096682897"
                             data-ad-slot="3091605842"
                             data-ad-format="horizontal"
                             data-full-width-responsive="true"></ins>
                        <script>
                            (adsbygoogle = window.adsbygoogle || []).push({});
                        </script>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col s12 m12 l9">
                <div id="live_matches"></div>
                @include('front.partial.live_matches_template')
            </div>

            <div class="col l3 hide-on-med-and-down">
                <div style="margin-top: 1rem">
                    <!-- Live Matches Sidebar -->
                    <ins class="adsbygoogle"
                         style="display:block"
                         data-ad-client="ca-pub-3518000096682897"
                         data-ad-slot="9341798357"
                         data-ad-format="vertical"
                         data-full-width-responsive="true"></ins>
                    <script>
                        (adsbygoogle = window.adsbygoogle || []).push({});
                    </script>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="/js/front/live_matches-scripts.js"></script>
@endsection
