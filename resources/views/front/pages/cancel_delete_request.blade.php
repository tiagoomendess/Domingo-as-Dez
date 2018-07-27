@extends('front.layouts.default-page')

@section('head-content')
    <title>{{ trans('front.cancel_delete_request') }}</title>
    <link rel="stylesheet" href="/css/front/delete_request-style.css">
@endsection

@section('content')
    <div class="all-centered v-align-middle">

        <div class="container">

            <div class="col s12 hide-on-med-and-down">
                <h1 class="center">{{ trans('front.cancel_delete_request') }}</h1>
            </div>

            <div class="row">
                <div class="col s12 m12 l8 x6 offset-l2 offset-xl2">
                    <div class="card">
                        <div class="card-content">

                            <div class="row">
                                <p class="col s12 flow-text center">{{ trans('front.cancel_delete_request_intro') }}</p>
                            </div>

                             @include('front.partial.form_errors')

                            <form action="{{ route('front.userprofile.delete.cancel.store') }}" method="POST">

                                {{ csrf_field() }}

                                <div class="row">
                                    <div class="center">
                                        <button type="submit" class="button btn green darken-2 btn-large">{{ trans('front.cancel_delete_request') }}</button>
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