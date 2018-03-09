@extends('front.layouts.no-container')

@section('head-content')
    <title>{{ trans('auth.login') }} {{ config('custom.site_name') }}</title>
@endsection

@section('content')

    <div class="valign-wrapper" style="height: 100%">

        <div style="width: 100%;">

            <div class="row" style="margin-bottom: 0px;">
                <div class="col xs12 s12 m8 l6 xl4 offset-m2 offset-l3 offset-xl4">

                    <h1 class="center" style="margin-top: 5px;">{{ trans('auth.login') }}</h1>

                    <div class="card">
                        <div class="card-content">

                            @if($errors)
                                <div class="row no-margin-bottom">
                                    <div class="col xs12 s12 no-margin-bottom">
                                        @include('front.partial.form_errors')
                                    </div>
                                </div>

                            @endif

                            <form class="form-horizontal" method="POST" action="{{ route('login') }}">

                                {{ csrf_field() }}
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
                                        <label for="password">{{ trans('auth.password') }}</label>
                                    </div>
                                </div>

                                <div class="row">

                                    <div class="col s6">
                                        <p>
                                            <input class="filled-in checkbox-blue" type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                                            <label for="remember">{{ trans('auth.remember_me') }}</label>
                                        </p>

                                    </div>

                                    <div class="col s6">

                                        <button class="btn waves-effect waves-light green darken-1 right" type="submit" name="action">{{ trans('auth.login') }}
                                            <i class="material-icons right">send</i>
                                        </button>

                                    </div>

                                </div>

                                <div class="row no-margin-bottom">

                                    <div class="col xs7 s7">
                                        <a href="{{ route('password.request') }}">
                                            {{ trans('auth.forgot_password') }}
                                        </a>
                                    </div>

                                    <div class="col xs5 s5">
                                        <a href="{{ route('register') }}" class="right">
                                            {{ trans('auth.create_account') }}
                                        </a>
                                    </div>

                                </div>

                            </form>

                        </div>
                    </div>
                </div>
            </div>

            @if(config('custom.social_logins') == true)
                @include('auth.social')
            @endif

        </div>

    </div>

@endsection
