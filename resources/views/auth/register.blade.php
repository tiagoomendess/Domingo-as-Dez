@extends('front.layouts.default-page')

@section('head-content')
    <title>{{ trans('auth.register') }}</title>
    <link type="text/css" rel="stylesheet" href="/css/front/auth-style.css"  media="screen,projection"/>
@endsection

@section('content')

    <div class="vertical-centered">
        <div class="container">
            <div class="row">
                <div class="col xs12 s12 m10 l8 xl6 offset-m1 offset-l2 offset-xl3">

                    <h1 class="center hide-on-med-and-down">{{ trans('auth.register') }}</h1>

                    <div class="card">
                        <div class="card-content">

                            @if($errors)
                                <div class="row no-margin-bottom">
                                    <div class="col xs12 s12 no-margin-bottom">
                                        @include('front.partial.form_errors')
                                    </div>
                                </div>

                            @endif

                            <form class="form-horizontal" method="POST" action="{{ route('register') }}">

                                {{ csrf_field() }}

                                <div class="row">
                                    <div class="input-field col s12">
                                        <i class="material-icons prefix">person_outline</i>
                                        <input name="name" id="name" type="text" value="{{ old('name') }}" required>
                                        <label for="name">{{ trans('auth.name') }}</label>
                                    </div>
                                </div>

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
                                    <div class="input-field col s12">
                                        <i class="material-icons prefix">vpn_key</i>
                                        <input name="password_confirmation" id="password_confirmation" type="password" required autocomplete="off">
                                        <label for="password_confirmation">{{ trans('auth.confirm_password') }}</label>
                                    </div>
                                </div>

                                <div class="row">

                                    <div class="col s12">
                                        <p>
                                            <input class="filled-in checkbox-blue" type="checkbox" id="terms_and_conditions" name="terms_and_conditions" {{ old('terms_and_conditions') ? 'checked' : '' }} required>
                                            <label for="terms_and_conditions">{{ trans('auth.terms_and_conditions') }}</label>
                                        </p>
                                    </div>

                                </div>

                                <div class="row">
                                    <div class="col s12">
                                        <p>
                                            <input class="filled-in checkbox-blue" type="checkbox" id="rgpd_disclaimer" name="rgpd_disclaimer" {{ old('rgpd_disclaimer') ? 'checked' : '' }} required>
                                            <label for="rgpd_disclaimer">{{ trans('front.rgpd_account_data_disclaimer') }}</label>
                                        </p>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col xs12 s12">
                                        {!! Recaptcha::render() !!}
                                    </div>
                                </div>

                                <div class="row">

                                    <div class="col s12">

                                        <button class="btn waves-effect waves-light green darken-1 right" type="submit" name="action">{{ trans('auth.register') }}
                                            <i class="material-icons right">send</i>
                                        </button>

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
