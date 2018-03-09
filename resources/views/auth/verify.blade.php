@extends('front.layouts.no-container')

@section('head-content')
    <title>{{ trans('auth.verify_email') }}</title>
@endsection

@section('content')
    <div class="valign-wrapper" style="height: 100%">

        <div style="width: 100%;">

            <div class="row" style="margin-bottom: 0px;">
                <div class="col xs12 s12 m8 l6 xl4 offset-m2 offset-l3 offset-xl4">

                    <h1 class="center" style="margin-top: 5px;">{{ trans('auth.verify_email') }}</h1>

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
