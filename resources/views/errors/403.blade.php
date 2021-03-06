@extends('errors.master-error')

@section('head-content')
    <title>{{trans('auth.permission_denied')}}</title>
@endsection

@section('content')
    <div class="error-wrapper">
        <div class="center-message">
            <i class="material-icons">error_outline</i>
            <h1>{{ trans('auth.permission_denied') }}</h1>
            <span class="details">{{ $exception->getMessage() }}</span>
            <div class="actions">
                <a class="btn-flat waves-effect waves-light" href="/">{{ trans('errors.back_to_index') }}</a>
            </div>
        </div>
    </div>
@endsection