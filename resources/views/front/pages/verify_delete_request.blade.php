@extends('front.layouts.default-page')

@section('head-content')
    <title>{{ trans('front.verify_delete_code_title') }}</title>
    <link rel="stylesheet" href="/css/front/delete_request-style.css">
@endsection

@section('content')
    <div class="all-centered v-align-middle">

        <div class="container">
            <div class="row">
                <div class="s12 hide-on-med-and-down">
                    <h1 class="center">{{ trans('front.verify_delete_code_title') }}</h1>
                </div>

                <div class="col s12 m12 l8 x6 offset-l2 offset-xl2">

                    <div class="card">
                        <div class="card-content">
                            <div class="row">
                                <p class="col s12 flow-text">
                                    {{ trans('front.verify_delete_code_intro') }}
                                </p>
                            </div>

                            @include('front.partial.form_errors')

                            <form action="{{ route('front.userprofile.delete.verify.store') }}" method="POST">
                                {{ csrf_field() }}

                                <div class="row">
                                    <div class="input-field col s12">
                                        <input id="codigo" type="text" name="codigo" required>
                                        <label for="codigo">{{ trans('general.code') }}</label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col s12">
                                        <button class="button btn red">{{ trans('front.delete_account') }}</button>
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

@section('scripts')
    <script src="/js/front/delete_request-scripts.js"></script>
@endsection