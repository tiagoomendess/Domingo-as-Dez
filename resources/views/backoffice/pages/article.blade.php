@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>{{ trans('models.article') }}</title>
@endsection

@section('content')
    <div class="row">
        <div class="col s12">
            <h1>{{ trans('models.media') }}</h1>
        </div>
    </div>

    <div class="row">
        <div class="col s12">

        </div>
    </div>

@endsection

@if(Auth::user()->hasPermission('media.edit'))
    @include('backoffice.partial.model_options', ['edit_route' => route('articles.edit', ['media' => $article]), 'delete_route' => route('articles.destroy', ['media' => $article])])
@endif