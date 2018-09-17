@extends('front.layouts.default-page')

@section('head-content')
    <title>{{ $article->title }}</title>
    <link rel="stylesheet" href="/css/front/article-style.css">

    <!-- Open Graph data -->
    <meta property="og:title" content="{{ $article->title }}" />
    <meta property="og:type" content="article" />
    <meta property="og:url" content="{{ route('news.show', [
                            'year' => \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $article->date)->year,
                            'month' => \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $article->date)->month,
                            'day' => \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $article->date)->day,
                            'slug' => str_slug($article->title)
                        ]) }}" />
    <meta property="og:image" content="{{ $article->getThumbnail() }}">
    <meta property="og:description" content="{{ $article->description }}" />

    <!-- Schema.org markup for Google+ -->
    <meta itemprop="name" content="{{ $article->title }}">
    <meta itemprop="description" content="{{ $article->description }}">
    <meta itemprop="image" content="{{ $article->getThumbnail() }}">
@endsection

@section('content')
    <article>

        @if($article->media)

            @if($article->media->media_type == 'image')
                <div class="parallax-container">
                    <div class="parallax">
                        <img src="{{ $article->media->url }}">
                    </div>

                    <div class="article-parallax-container vertical-centered">
                        <div class="container">
                            <div class="col s12 article-title">
                                <h1 class="light">{{ $article->title }}</h1>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif($article->media->media_type == 'youtube')
                <div class="article-video-media">
                    <div class="container">
                        <div class="embed-container">
                            <iframe id="media_youtube_video" src="{{ str_replace('watch?v=', 'embed/', $article->media->url) }}" frameborder='0' allowfullscreen></iframe>
                        </div>
                    </div>

                    <div class="container">
                        <div class="col s12 article-title">
                            <h1 class="light">{{ $article->title }}</h1>
                        </div>
                    </div>
                </div>

            @elseif($article->media->media_type == 'video')

                <div class="">
                    <div class="container">
                        <video style="width: 100%" class="responsive-video" controls>
                            <source src="{{ $article->media->url }}" type="video/mp4">
                        </video>
                    </div>

                    <div class="container">
                        <div class="col s12 article-title">
                            <h1 class="light" style="">{{ $article->title }}</h1>
                        </div>
                    </div>

                </div>
            @endif

        @else

            <div class="parallax-container">
                <div class="parallax">
                    <img class="" src="{{ \App\Media::getPlaceholder('16:9', $article->id) }}">
                </div>

                <div class="article-parallax-container vertical-centered">
                    <div class="container">
                        <div class="article-title col s12">
                            <h1 class="light">{{ $article->title }}</h1>
                        </div>
                    </div>
                </div>
            </div>

        @endif

        <section class="section white">
            <div class="container">
                <div class="col s12 article-description">
                    <p class="flow-text">{{ $article->description }}</p>
                </div>
            </div>
        </section>

        <div class="container">

            <section class="col s12 article-body">
                {!! $article->text !!}
            </section>


            <section class="col s12 article-signature">
                <p class="right">{{ trans('front.article_published', ['name' => $article->user->name, 'date' => \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $article->date)->format("d/m/Y")]) }}</p>
            </section>


        </div>

    </article>

@endsection

@section('scripts')
    <script>

        $(document).ready(function(){
            $('.parallax').parallax();
            $('.materialboxed').materialbox();

            $('.article-body p').each(function () {
                $(this).addClass('flow-text');
            });

            $(function () {

                var iframe = $('#media_youtube_video');
                var height = iframe.height();
                console.log(height);
                var width = height * 1.777;

                if(height < 350) {

                    iframe.attr('style', 'width: 100%;');
                    width = iframe.width();
                    height = width * 0.5624;
                    iframe.attr('style', 'width: ' + width + 'px!important; height: ' + height + 'px!important;');

                } else {
                    iframe.attr('style', 'width: ' + width + 'px!important;');
                }


            })
        });

    </script>
@endsection