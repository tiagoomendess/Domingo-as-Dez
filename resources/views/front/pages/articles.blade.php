@extends('front.layouts.default-page')

@section('head-content')
    <title>{{ trans('general.news') }}</title>
@endsection

@section('content')

    <div class="row">
        <div class="col s12 m12 l8 xl8">
            @foreach($articles as $article)
                <div class="card large hoverable">
                    <div class="card-image">
                        <img src="{{ $article->media->url }}">
                        <span class="card-title" style="text-shadow: 1px 1px 3px #000000;">{{ $article->title }}</span>
                    </div>
                    <div class="card-content">
                        <p class="">{{ str_limit($article->description, 300) }}</p>
                    </div>
                    <div class="card-action">
                        <a href="#" class="right blue-text">{{ trans('front.read_more') }}</a>
                    </div>
                </div>

            @endforeach
        </div>

        <div class="col s12 m12 l4 xl4">

            <div class="card">

                <div class="card-image">
                    <img src="http://placehold.it/500x500">
                </div>

            </div>

            <div class="card">

                <div class="card-image">
                    <img src="http://placehold.it/500x500">
                </div>

            </div>

            <div class="card">

                <div class="card-image">
                    <img src="http://placehold.it/500x500">
                </div>

            </div>

        </div>
    </div>

    <div class="row">
        {{ $articles->links() }}
    </div>


@endsection