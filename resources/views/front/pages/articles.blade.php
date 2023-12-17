@extends('front.layouts.default-page')

@section('head-content')
    <title>{{ trans('general.news') }}</title>
    <link rel="stylesheet" href="/css/front/articles-style.css">

    <meta property="og:title" content="{{ trans('general.news') . ' ' . config('app.name') }}" />
    <meta property="og:type" content="website" />
    <meta property="og:description" content="{{ trans('front.footer_desc') }}" />

@endsection

@section('content')

    <div class="container">
        <div class="row no-margin-bottom hide-on-med-and-down">
            <div class="col s12">
                <h1>{{ trans('general.news') }}</h1>
            </div>
        </div>

        <div class="row no-margin-bottom">
            @foreach($articles as $index => $article)
                @if($index === 4)
                    <div class="col xs12 s12 m6 l6 xl4">
                        <div style="">
                            <div style="">
                                <!-- News Page Card -->
                                <ins class="adsbygoogle"
                                     style="display:block"
                                     data-ad-client="ca-pub-3518000096682897"
                                     data-ad-slot="3616731365"
                                     data-ad-format="auto"
                                     data-full-width-responsive="true"></ins>
                                <script>
                                    (adsbygoogle = window.adsbygoogle || []).push({});
                                </script>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="col xs12 s12 m6 l6 xl4">
                        <div class="card medium hoverable">
                            <div class="card-image">
                                <div class="article-thumb-fill">
                                    @if($article->media)
                                        <img src="{{ $article->media->thumbnail_url ? $article->media->thumbnail_url : \App\Media::getPlaceholder('16:9', $article->id) }}" alt="{{ $article->media->tags }}">
                                    @else
                                        <img src="{{ \App\Media::getPlaceholder('16:9', $article->id) }}" alt="">
                                    @endif
                                    <span class="card-title">{{ $article->title }}</span>
                                </div>
                            </div>
                            <div class="card-content">
                                <p class="">{{ \Illuminate\Support\Str::limit($article->description, 300) }}</p>
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

@section('scripts')
@endsection