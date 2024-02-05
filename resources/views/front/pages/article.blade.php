@extends('front.layouts.default-page')

@section('head-content')
    <title>{{ $article->title }}</title>
    <meta name="description" content="{{ $article->description }}">
    <meta name="keywords" content="Notícia, News, Notícias, {{ $article->tags }}">
    <link rel="stylesheet" href="/css/front/article-style.css">

    <!-- Open Graph data -->
    <meta property="og:title" content="{{ $article->title }}"/>
    <meta property="og:type" content="article"/>
    <meta property="og:url" content="{{ route('news.show', [
                            'year' => \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $article->date)->year,
                            'month' => \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $article->date)->month,
                            'day' => \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $article->date)->day,
                            'slug' => str_slug($article->title)
                        ]) }}"/>
    <meta property="og:image" content="{{ url($article->getThumbnail()) }}">
    <meta property="og:image:width" content="{{ $img_width }}">
    <meta property="og:image:height" content="{{ $img_height }}">
    <meta property="og:description" content="{{ $article->description }}"/>

    <!-- Schema.org markup for Google+ -->
    <meta itemprop="name" content="{{ $article->title }}">
    <meta itemprop="description" content="{{ $article->description }}">
    <meta itemprop="image" content="{{ url($article->getThumbnail()) }}">
@endsection

@section('content')
    <article data-id="{{ $article->id }}">

        @if($article->media)
            @if($article->media->media_type == 'image')
                <div class="parallax-container">
                    <div class="parallax">
                        <img src="{{ $article->media->url }}" alt="{{ $article->media->tags }}">
                    </div>

                    <div class="article-parallax-container">

                        <div class="col s12 article-title">
                            <div class="container">
                                <h1 class="light">{{ $article->title }}</h1>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif($article->media->media_type == 'youtube')
                <div class="article-video-media">
                    <div class="container">
                        <div class="embed-container">
                            <iframe id="media_youtube_video"
                                    src="{{ str_replace('watch?v=', 'embed/', $article->media->url) }}" frameborder='0'
                                    allowfullscreen></iframe>
                        </div>
                    </div>

                    <div class="col s12 article-title-video">
                        <div class="container">
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

                    <div class="col s12 article-title-video">
                        <div class="container">
                            <h1 class="light" style="">{{ $article->title }}</h1>
                        </div>
                    </div>
                </div>
            @endif
        @else

            <div class="parallax-container">
                <div class="parallax">
                    <img class="" src="{{ \App\Media::getPlaceholder('16:9', $article->id) }}" alt="">
                </div>

                <div class="article-parallax-container">

                    <div class="article-title col s12">
                        <div class="container">
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
            @if ($article->visible)
                @if(!has_permission('disable_ads'))
                    <div class="row no-margin-bottom">
                        <div class="vertical-spacer hide-on-med-and-down"></div>
                        <div class="col s12 m10 l8 offset-m1 offset-l2">
                            <script async
                                    src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-3518000096682897"
                                    crossorigin="anonymous"></script>
                            <!-- Article Description Horizontal -->
                            <ins class="adsbygoogle"
                                 style="display:block"
                                 data-ad-client="ca-pub-3518000096682897"
                                 data-ad-slot="7397948298"
                                 data-ad-format="auto"
                                 data-full-width-responsive="true"></ins>
                            <script>
                                (adsbygoogle = window.adsbygoogle || []).push({});
                            </script>
                        </div>
                    </div>
                @endif
            @endif
        </div>

        <div class="container hide" id="article_body">

            <section class="col s12 article-body">
                {!! $article->text !!}
            </section>

            <section class="col s12 article-signature">
                <p style="text-align: right">{{ trans('front.article_published', ['name' => $article->user->name, 'date' => \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $article->date)->format("d/m/Y")]) }}</p>
            </section>
        </div>

        <div id="main_loading" style="min-height: 70vh;">
            <div class="container" style="padding-top: 100px;">
                <div class="center">
                    <div class="preloader-wrapper big active">
                        <div class="spinner-layer spinner-blue-only">
                            <div class="circle-clipper left">
                                <div class="circle"></div>
                            </div>
                            <div class="gap-patch">
                                <div class="circle"></div>
                            </div>
                            <div class="circle-clipper right">
                                <div class="circle"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </article>

    <div class="container" id="bottom_ads">
        @if ($article->visible)
            @if(!has_permission('disable_ads'))
                <div class="row">
                    <div class="col col-xs-12 s12 m10 l8 offset-m1 offset-l2">
                        <script async
                                src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-3518000096682897"
                                crossorigin="anonymous"></script>
                        <!-- Article Top Comments -->
                        <ins class="adsbygoogle"
                             style="display:block"
                             data-ad-client="ca-pub-3518000096682897"
                             data-ad-slot="9842679623"
                             data-ad-format="horizontal"
                             data-full-width-responsive="true"></ins>
                        <script>
                            (adsbygoogle = window.adsbygoogle || []).push({});
                        </script>
                    </div>
                </div>
            @endif
        @endif
    </div>

    <div class="hide" id="comments_wrapper">
        @include('front.partial.article_comments')
    </div>

@endsection

@section('scripts')

    <script>
        $(document).ready(function () {
            $('.materialboxed').materialbox();

            $('.article-body p').each(function () {
                $(this).addClass('flow-text');
            });

            $('.article-body ul').each(function () {
                $(this).addClass('flow-text');
            });

            $('.article-body ol').each(function () {
                $(this).addClass('flow-text');
            });

            $(function () {
                var iframe = $('#media_youtube_video');
                var height = iframe.height();
                var width = height * 1.777;

                if (height < 350) {
                    iframe.attr('style', 'width: 100%;');
                    width = iframe.width();
                    height = width * 0.5624;
                    iframe.attr('style', 'width: ' + width + 'px!important; height: ' + height + 'px!important;');
                } else {
                    iframe.attr('style', 'width: ' + width + 'px!important;');
                }
            })

            $('#comments_wrapper').removeClass('hide');
        });

        jQuery(window).on("load", function () {
            console.log('Everything Loaded');
            $('#article_body').removeClass('hide');
            $('#main_loading').addClass('hide');
            setTimeout(() => {
                $('.parallax').parallax();
                $('#comments_wrapper').removeClass('hide');
            }, 1);
        });

    </script>

    <script src="/js/front/article-comments-scripts.js"></script>
@endsection