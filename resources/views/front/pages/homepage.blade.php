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
                        <img src="{{ $article->media->url }}">
                        <span class="card-title" style="text-shadow: #000000, 1px 1px 1px">{{ $article->title }}</span>
                    </div>
                    <div class="card-content">
                        <p class="">{{ str_limit($article->description, 155) }}</p>
                    </div>
                    <div class="card-action">
                        <a href="#" class="right blue-text">{{ trans('front.read_more') }}</a>
                    </div>
                </div>
            </div>
        @endforeach

    </div>
@endsection