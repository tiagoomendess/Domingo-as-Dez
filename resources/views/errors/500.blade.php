@extends('errors.master-error')

@section('head-content')
    <title>{{trans('errors.http_500_title')}}</title>
@endsection

@section('content')
    <div class="error-wrapper">
        <div class="center-message">
            <i class="material-icons">error_outline</i>
            <h1>{{ trans('errors.http_500') }}</h1>
            <span class="details">{{ trans('errors.http_500_details') }}</span>
            <div class="actions">
                <a class="btn-flat waves-effect waves-light" href="/">{{ trans('errors.back_to_index') }}</a>
            </div>
        </div>
    </div>
@endsection
