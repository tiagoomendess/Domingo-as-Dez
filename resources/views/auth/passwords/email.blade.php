@extends('front.layouts.default-page')

@section('head-content')
    <title>{{ trans('auth.request_password_reset') }}</title>
    <link type="text/css" rel="stylesheet" href="/css/front/auth-style.css"  media="screen,projection"/>
@endsection

@section('content')
    <div>

        <div>

            <div class="row">
                <div class="col xs12 s12 m8 l6 xl4 offset-m2 offset-l3 offset-xl4">

                    <h1 class="center">{{ trans('auth.request_password_reset') }}</h1>

                    <div class="card">
                        <div class="card-content">

                            @if($errors)

                                <div class="row no-margin-bottom">
                                    <div class="col xs12 s12 no-margin-bottom">
                                        @include('front.partial.form_errors')
                                    </div>
                                </div>

                            @endif

                            @if (session('status'))

                                <div class="row">
                                    <div class="col s12">
                                        <blockquote>
                                            <ul>
                                                <li>{{ session('status') }}</li>
                                            </ul>
                                        </blockquote>
                                    </div>
                                </div>
                            @else
                                <div class="row">
                                    <div class="col s12">
                                        <p class="flow-text">{{ trans('auth.request_password_reset_help') }}</p>
                                    </div>
                                </div>

                                    <form class="form-horizontal" method="POST" action="{{ route('password.email') }}">

                                        {{ csrf_field() }}

                                        <div class="row">
                                            <div class="input-field col s12">
                                                <i class="material-icons prefix">mail_outline</i>
                                                <input name="email" id="email" type="email" value="{{ old('email') }}" required>
                                                <label for="email">{{ trans('auth.email') }}</label>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col xs12 s12">
                                                {!! Recaptcha::render() !!}
                                            </div>
                                        </div>

                                        <div class="row">

                                            <div class="col s12">

                                                <button class="btn waves-effect waves-light green darken-1 right" type="submit" name="action">{{ trans('auth.request_password_reset') }}
                                                    <i class="material-icons right">send</i>
                                                </button>

                                            </div>

                                        </div>

                                    </form>
                            @endif


                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
@endsection
