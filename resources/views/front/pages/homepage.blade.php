@extends('front.layouts.default-page')

@section('head-content')
    <title>{{ config('custom.site_name') }}</title>
@endsection
@section('content')

    <div class="row">

        @foreach($articles as $article)
            <div class="col xs12 s12 m6 l4 xl4">
                <div class="card small hoverable">
                    <div class="card-image">
                        @if($article->media)
                            @if($article->media->media_type == 'image')
                                <img src="{{ $article->media->url }}" alt="{{ $article->media->tags }}">
                            @elseif($article->media->media_type == 'youtube')
                                <div class="embed-container">
                                    <iframe id="media_youtube_video" style="width: 100%" src="{{ str_replace('watch?v=', 'embed/', $article->media->url . '&rel=0&amp;controls=0&amp;showinfo=0&amp;') }}" frameborder='0' allowfullscreen></iframe>
                                </div>
                            @else
                                <img src="{{ \App\Media::getPlaceholder('16:9', $article->id) }}" alt="">
                            @endif

                        @else
                            <img src="{{ \App\Media::getPlaceholder('16:9', $article->id) }}" alt="">
                        @endif
                        <span class="card-title" style="text-shadow: 1px 1px 3px #000000;">{{ $article->title }}</span>
                    </div>
                    <div class="card-content">
                        <p class="">{{ str_limit($article->description, 155) }}</p>
                    </div>
                    <div class="card-action">
                        <a href="{{ $article->getPublicUrl() }}" class="right blue-text">{{ trans('front.read_more') }}</a>
                    </div>
                </div>
            </div>
        @endforeach

    </div>
@endsection