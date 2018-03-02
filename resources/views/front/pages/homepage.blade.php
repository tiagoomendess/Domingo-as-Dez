@extends('front.layouts.default-page')

@section('head-content')
    <title>{{ config('custom.site_name') }}</title>
@endsection
@section('content')

    <div class="row">

        @foreach($articles as $article)
            <div class="col xs12 s12 m6 l4 xl4">
                <div class="card medium hoverable">
                    <div class="card-image">
                        @if($article->media)
                            <img src="{{ $article->media->url }}">
                        @else
                            <?php
                                $str = (string) $article->id;
                                $arr = str_split($str); // convert string to an array
                            ?>
                            <img src="{{ "/images/16_9_placeholder_" . end($arr) . ".jpg" }}" alt="">
                        @endif
                        <span class="card-title" style="text-shadow: 1px 1px 3px #000000;">{{ $article->title }}</span>
                    </div>
                    <div class="card-content">
                        <p class="">{{ str_limit($article->description, 155) }}</p>
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
@endsection