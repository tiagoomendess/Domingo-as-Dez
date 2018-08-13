@extends('front.layouts.default-page')

@section('head-content')
    <title>{{ config('custom.site_name') }}</title>
@endsection
@section('content')

    <div class="container">

        @if($live)
            <div class="row">
                <div id="live_matches"></div>
                @include('front.partial.live_matches_template')
            </div>
        @endif

        <div class="row">
            <div class="col s12 m12 l8">
                <div class="aspect-ratio">
                    <div class="aspect-ratio-inside">
                        @foreach($articles as $article)
                            <div class="news-thumbnail">
                                @if ($article->media)

                                    @if($article->media->media_type == 'image')
                                        <img src="{{ $article->media->url }}" alt="{{ $article->media->tags }}">
                                    @else
                                        <img src="{{ \App\Media::getPlaceholder('16:9', $article->id) }}" alt="">
                                    @endif

                                @else
                                    <img src="{{ \App\Media::getPlaceholder('16:9', $article->id) }}" alt="">
                                @endif
                            </div>
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
@endsection
