@extends('base')

@section('head')
    <title>{{trans('auth.permission_denied')}}</title>
@endsection

@section('body')
    <div>
        <h1>{{ trans('auth.permission_denied') }}</h1>
        <p>{{ $exception->getMessage() }}</p>
    </div>
@endsection