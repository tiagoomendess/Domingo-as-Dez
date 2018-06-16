@extends('front.layouts.no-container')

@section('head-content')
    <title>{{ trans('auth.register') }}</title>
@endsection

@section('content')
    <div class="valign-wrapper" style="height: 100%">

        <div style="width: 100%;">

            <div class="row" style="margin-bottom: 0px;">
                <div class="col xs12 s12 m8 l6 xl4 offset-m2 offset-l3 offset-xl4">

                    <h1 class="center hide-on-med-and-down" style="margin-top: 5px;">{{ trans('auth.register') }}</h1>

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
                                            <input class="filled-in checkbox-blue" type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }} required>
                                            <label for="remember">{{ trans('auth.terms_and_conditions') }}</label>
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
