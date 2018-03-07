@extends('front.layouts.no-container')

@section('head-content')
    <title>{{ trans('auth.login') }} {{ config('custom.site_name') }}</title>
@endsection

@section('content')

    <div class="valign-wrapper" style="height: 100%">

        <div style="width: 100%;">
            <div class="row">
                <div class="col xs12 s12 m8 l6 xl4 offset-m2 offset-l3 offset-xl4">

                    <h1 class="center" style="margin-top: 0px;">{{ trans('auth.login') }}</h1>

                    <div class="card">
                        <div class="card-content">

                            <form class="form-horizontal" method="POST" action="{{ route('login') }}">

                                {{ csrf_field() }}
                                <div class="row">
                                    <div class="input-field col s12">
                                        <input name="email" id="email" type="email" required>
                                        <label for="email">{{ trans('front.email') }}</label>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="input-field col s12">
                                        <input name="password" id="password" type="password" required autocomplete="off">
                                        <label for="password">{{ trans('passwords.password') }}</label>
                                    </div>
                                </div>

                                <div class="row">

                                    <div class="col s6">
                                        <p>
                                            <input type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                                            <label for="remember">{{ trans('auth.remember_me') }}</label>
                                        </p>

                                    </div>

                                    <div class="col s6">
                                        <button type="submit" class="btn">
                                            {{ trans('auth.login') }}
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
