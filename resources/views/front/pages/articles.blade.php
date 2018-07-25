@extends('front.layouts.default-page')

@section('head-content')
    <title>{{ trans('general.news') }}</title>
    <link rel="stylesheet" href="/css/front/articles-style.css">
@endsection

@section('content')

    <div class="container">
        <div class="row no-margin-bottom hide-on-med-and-down">
            <div class="col s12">
                <h1>{{ trans('general.news') }}</h1>
            </div>
        </div>

        <div class="row no-margin-bottom">
            @foreach($articles as $article)
                <div class="col xs12 s12 m6 l6 xl4">

                    <div class="card medium hoverable">
                        <div class="card-image">
                            <div class="article-thumb-fill">
                                @if($article->media)
                                    @if($article->media->media_type == 'image')
                                        <img src="{{ $article->media->url }}" alt="{{ $article->media->tags }}">
                                    @else
                                        <img src="{{ \App\Media::getPlaceholder('16:9', $article->id) }}" alt="">
                                    @endif

                                @else
                                    <img src="{{ \App\Media::getPlaceholder('16:9', $article->id) }}" alt="">
                                @endif
                                <span class="card-title">{{ $article->title }}</span>
                            </div>
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