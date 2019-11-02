@extends('front.layouts.default-page')

@section('head-content')
    <title>{{ trans('auth.verify_email') }}</title>
    <link type="text/css" rel="stylesheet" href="/css/front/auth-style.css" media="screen,projection"/>
@endsection

@section('content')

    <div class="container" style="height: 100%">
        <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center">
            <div class="col s12 m8 l6">

                <h1 class="center">{{ trans('auth.verify_email') }}</h1>

                <div class="card">
                    <div class="card-content">
                        @if(count($errors) > 0)
                            <div style="width: 100%; position: relative">
                                <p class="center flow-text text-bold">{{ trans('general.error') }}</p>
                                <div style="width: 100%">
                                    @include('front.partial.form_errors')
                                </div>
                            </div>
                        @else
                            <p class="flow-text">{{ trans('auth.verify_email_help', ['email' => $email]) }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>


@endsection
