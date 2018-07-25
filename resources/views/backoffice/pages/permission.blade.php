@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>{{ trans('models.permission') }}</title>
@endsection

@section('content')
    <div class="row">
        <div class="col s12">
            <h1>{{ trans('models.permission') }}</h1>
        </div>
    </div>

    <div class="row">
        <div class="col s12">
            <p class="flow-text">
                <b>{{ trans('general.id') }} : </b> {{ $permission->id }} <br>
                <b>{{ trans('general.name') }} : </b> {{ $permission->name }} <br>
                <b>{{ trans('general.created_at') }} : </b> {{ $permission->created_at }} <br>
                <b>{{ trans('general.updated_at') }} : </b> {{ $permission->updated_at }} <br>
            </p>
        </div>
    </div>

@endsection

@if(Auth::user()->hasPermission('articles.edit'))
    @include('backoffice.partial.model_options', ['edit_route' => route('permissions.edit', ['permission' => $permission]), 'delete_route' => route('permissions.destroy', ['permission' => $permission])])
@endif

@section('scripts')

@endsection