@extends('front.layouts.default-page')

@section('head-content')
    <title>{{ $article->title }}</title>
@endsection

@section('content')

    <div class="row">
        <div class="col xs12 s12 m10 offset-m1 l8">
            <div class="card">
                <div class="card-image">
                    <img src="images/sample-1.jpg">
                    <span class="card-title">Card Title</span>
                </div>
                <div class="card-content">
                    <p>I am a very simple card. I am good at containing small bits of information.
                        I am convenient because I require little markup to use effectively.</p>
                </div>
                <div class="card-action">
                    <a href="#">This is a link</a>
                </div>
            </div>
        </div>
    </div>

@endsection