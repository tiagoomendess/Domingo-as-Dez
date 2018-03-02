@extends('front.layouts.default-page')

@section('head-content')
    <title>{{ $article->title }}</title>
@endsection

@section('content')

    <div class="row">
        <div class="col xs12 s12 m10 l9 offset-m1">

            <h1>{{ $article->title }}</h1>
            <p class="flow-text">{{ $article->description }}</p>

        </div>

        <div class="col xs12 s12 m10 l9 offset-m1">

            @if($article->media_id)
                <img style="min-width: 100%;" class="responsive-img materialboxed" src="{{ $article->media->url }}">
            @else
                <?php
                $str = (string) $article->id;
                $arr = str_split($str); // convert string to an array
                ?>
                <img style="min-width: 100%;" class="responsive-img materialboxed" src="{{ "/images/16_9_placeholder_" . end($arr) . ".jpg" }}" alt="">
            @endif

        </div>

        <div class="col xs12 s12 m10 l9 offset-m1">

            <div class="card">
                <div class="card-content">
                    {!! $article->text !!}
                </div>
            </div>
        </div>
    </div>

@endsection