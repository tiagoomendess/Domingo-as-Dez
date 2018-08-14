@extends('front.layouts.default-page')

@section('head-content')
    <title>{{ config('custom.site_name') }}</title>
    <link rel="stylesheet" href="/css/front/homepage-style.css">
@endsection
@section('content')

    <div class="container front-container">

        @if($live)
            <div class="row">
                <div id="live_matches"></div>
                @include('front.partial.live_matches_template')
            </div>
        @endif

        <div class="row">
            <div class="col s12 m12 l8">
                <div class="aspect-ratio-16by9">
                    <div class="aspect-ratio-inside" id="news_snippets">

                        <div class="news-snippet-progress" style="width: 0%"></div>

                        @foreach($articles as $index => $article)

                            <a href="{{ $article->getPublicUrl() }}" class="{{ $index > 0 ? 'hide': 'active' }}">

                                <div class="news-thumbnail">

                                    <div>
                                        @if ($article->media)

                                            <img src="{{ $article->media->thumbnail_url }}" alt="{{ $article->media->tags }}">

                                        @else
                                            <img src="{{ \App\Media::getPlaceholder('16:9', $article->id) }}" alt="">
                                        @endif
                                    </div>

                                    <span class="news-title">{{ $article->title }}</span>

                                </div>
                            </a>

                        @endforeach
                    </div>
                </div>
            </div>

            <div class="col s12 m12 l4">

            </div>
        </div>
    </div>

@endsection

@section("scripts")
    @if($live)
        <script src="/js/front/live_matches-scripts.js"></script>
    @endif
    <script src="/js/front/homepage-scripts.js"></script>
@endsection
