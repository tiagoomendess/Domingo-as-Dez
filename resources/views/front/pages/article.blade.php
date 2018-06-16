@extends('front.layouts.no-container')

@section('head-content')
    <title>{{ $article->title }}</title>
@endsection

@section('content')
    <article>

        @if($article->media)

            @if($article->media->media_type == 'image')
                <div class="parallax-container">
                    <div class="parallax">
                        <img class="" src="{{ $article->media->url }}">
                    </div>

                    <div class="row article-parallax-container">
                        <div class="container" style="height: 100%;">
                            <div class="row">
                                <div class="col s12">
                                    <h1 class="article-title light" style="">{{ $article->title }}</h1>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            @elseif($article->media->media_type == 'youtube')
                <div class="section black">
                    <div class="container">
                        <div class="embed-container">
                            <iframe id="media_youtube_video" style="width: 100%" src="{{ str_replace('watch?v=', 'embed/', $article->media->url) }}" frameborder='0' allowfullscreen></iframe>
                        </div>
                    </div>

                    <div class="container">
                        <div class="row" style="margin-bottom: 0">
                            <div class="col s12">
                                <h1 class="article-title light" style="">{{ $article->title }}</h1>
                            </div>
                        </div>
                    </div>
                </div>

            @elseif($article->media->media_type == 'video')

                <div class="section black">
                    <div class="container">
                        <video style="width: 100%" class="responsive-video" controls>
                            <source src="{{ $article->media->url }}" type="video/mp4">
                        </video>
                    </div>

                    <div class="container">
                        <div class="row" style="margin-bottom: 0">
                            <div class="col s12">
                                <h1 class="article-title light" style="">{{ $article->title }}</h1>
                            </div>
                        </div>
                    </div>

                </div>
            @endif

        @else

            <div class="parallax-container">
                <div class="parallax">
                    <img class="" src="{{ \App\Media::getPlaceholder('16:9', $article->id) }}">
                </div>

                <div class="row article-parallax-container">
                    <div class="container" style="height: 100%;">
                        <div class="row">
                            <div class="col s12">
                                <h1 class="article-title light" style="">{{ $article->title }}</h1>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        @endif

        <div class="section white">
            <div class="container">
                <div class="row">
                    <div class="col s12 article-description">
                        <p class="flow-text">{{ $article->description }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="container">
            <div class="row">
                <div class="col s12 article-body">
                    {!! $article->text !!}
                </div>

            </div>

            <div class="row">

                <div class="col s12">
                    <p class="right article-published-by" style="">{{ trans('front.article_published', ['name' => $article->user->name, 'date' => \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $article->date)->format("d/m/Y")]) }}</p>
                </div>

            </div>
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