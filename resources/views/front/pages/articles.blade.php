@extends('front.layouts.default-page')

@section('head-content')
    <title>{{ trans('general.news') }}</title>
@endsection

@section('content')

    <div class="container">
        <div class="row">
            @foreach($articles as $article)
                <div class="col s12 m6 l4">

                    <div class="card medium hoverable">
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
                            <p class="">{{ str_limit($article->description, 300) }}</p>
                        </div>
                        <div class="card-action">
                            <a href="{{ route('news.show', [
                            'year' => \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $article->date)->year,
                            'month' => \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $article->date)->month,
                            'day' => \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $article->date)->day,
                            'slug' => str_slug($article->title)
                        ]) }}" class="right blue-text">{{ trans('front.read_more') }}</a>
                        </div>
                    </div>
                </div>

            @endforeach
        </div>
    </div>


    <div class="row">
        {{ $articles->links() }}
    </div>


@endsection