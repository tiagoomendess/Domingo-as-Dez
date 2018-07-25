@extends('front.layouts.default-page')

@section('head-content')
    <title>{{ trans('auth.reset_password') }}</title>
    <link type="text/css" rel="stylesheet" href="/css/front/auth-style.css"  media="screen,projection"/>
@endsection

@section('content')
    <div>
        <div>
            <div class="row">
                <div class="col xs12 s12 m8 l6 xl4 offset-m2 offset-l3 offset-xl4">
                    <h1 class="center">{{ trans('auth.reset_password') }}</h1>
                    <div class="card">
                        <div class="card-content">
                            @if($errors)
                                <div class="row no-margin-bottom">
                                    <div class="col xs12 s12 no-margin-bottom">
                                        @include('front.partial.form_errors')
                                    </div>
                                </div>
                            @endif
                                <form class="form-horizontal" method="POST" action="{{ route('password.request') }}">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="token" value="{{ $token }}">

                                    <div class="row">
                                        <div class="input-field col s12">
                                            <i class="material-icons prefix">mail_outline</i>
                                            <input name="email" id="email" type="email" value="{{ old('email') }}" required>
                                            <label for="email">{{ trans('auth.email') }}</label>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="input-field col s12">
                                            <i class="material-icons prefix">vpn_key</i>
                                            <input name="password" id="password" type="password" required autocomplete="off">
                                            <label for="password">{{ trans('auth.new_password') }}</label>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="input-field col s12">
                                            <i class="material-icons prefix">vpn_key</i>
                                            <input name="password_confirmation" id="password_confirmation" type="password" required autocomplete="off">
                                            <label for="password_confirmation">{{ trans('auth.confirm_new_password') }}</label>
                                        </div>
                                    </div>

                                    <div class="row">

                                        <div class="col s12">

                                            <button class="btn waves-effect waves-light green darken-1 right" type="submit" name="action">{{ trans('auth.reset_password') }}
                                                <i class="material-icons right">send</i>
                                            </button>

                                        </div>

                                    </div>

                                </form>

                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
@endsection