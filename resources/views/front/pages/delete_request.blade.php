@extends('front.layouts.default-page')

@section('head-content')
    <title>{{ trans('front.delete_account') }}</title>
    <link rel="stylesheet" href="/css/front/delete_request-style.css">
@endsection

@section('content')

    <div class="all-centered v-align-middle">

        <div class="container">
            <div class="row">
                <div class="col s12 m12 l8 x6 offset-l2 offset-xl2">

                    <h1 class="center hide-on-med-and-down">{{ trans('front.delete_account') }}</h1>
                    <div class="card">
                        <div class="card-content">

                            <div class="row">
                                <p class="flow-text col s12">{{ trans('front.delete_account_warning_1') }}</p><br>
                            </div>

                            @include('front.partial.form_errors')

                            <form action="{{ route('front.userprofile.delete.store') }}" method="POST" id="delete_request_form">

                                {{csrf_field()}}

                                <div class="row">
                                    <div class="input-field col s12">
                                        <textarea id="motivo" class="materialize-textarea" name="motivo" placeholder="{{ trans('front.motive_palceholder') }}">{{ old('motive') }}</textarea>
                                        <label for="motivo">{{trans('general.motive')}}</label>
                                    </div>
                                </div>

                                <div class="row">
                                    <p class="col s12">
                                        <input name="understand" required type="checkbox" class="filled-in checkbox-blue" id="filled-in-box" />
                                        <label for="filled-in-box">{{ trans('front.delete_account_understand') }}</label>
                                    </p>
                                </div>

                                <div class="row">
                                    <div class="input-field col s12">
                                        <button id="delete_request_form_submit" type="submit" disabled class="btn button red">{{ trans('front.delete_account') }}</button>
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