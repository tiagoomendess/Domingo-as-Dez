@extends('front.layouts.default-page')

@section('head-content')
    <title>{{ trans('auth.verify_email') }}</title>
    <link type="text/css" rel="stylesheet" href="/css/front/auth-style.css"  media="screen,projection"/>
@endsection

@section('content')
    <div class="valign-wrapper">

        <div>

            <div class="row">
                <div class="col xs12 s12 m8 l6 xl4 offset-m2 offset-l3 offset-xl4">

                    <h1 class="center">{{ trans('auth.verify_email') }}</h1>

                    <div class="card">
                        <div class="card-content">
                            <p class="flow-text">{{ trans('auth.verify_email_help', ['email' => $email]) }}</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
@endsection
