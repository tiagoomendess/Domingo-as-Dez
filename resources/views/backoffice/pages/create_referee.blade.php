@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>{{ trans('general.add') }} {{ trans('models.referee') }}</title>
@endsection

@section('content')

    <div class="row">
        <div class="col s12">
            <h1>{{ trans('general.add') }} {{ trans('models.referee') }}</h1>
        </div>
    </div>

    @if(count($errors) > 0)
        <div class="row">
            <div class="col s12">
                @include('backoffice.partial.form_errors')
            </div>
        </div>
    @endif

    <form action="{{ route('referees.store') }}" method="POST" enctype="multipart/form-data">

        {{ csrf_field() }}

        <div class="row">

            <div class="input-field col s12 m4 l3">
                <input name="name" id="name" type="text" class="validate" value="{{ old('name') }}" required>
                <label for="name">{{ trans('general.name') }}</label>
            </div>

            <div class="input-field col s12 m4 l3">
                <input name="association" id="association" type="text" class="validate" value="{{ old('association') }}" required>
                <label for="association">{{ trans('general.association') }}</label>
            </div>

        </div>

        <div class="row">
            <div class="file-field input-field col s12 m8 l6">
                <div class="btn">
                    <span>{{ trans('models.picture') }}</span>
                    <input name="picture" type="file">
                </div>
                <div class="file-path-wrapper">
                    <input class="file-path validate" type="text">
                </div>
            </div>

        </div>

        <div class="row">
            <div class="col s12 m8 l6">
                <p class="flow-text center">
                    {{ trans('general.or') }}
                </p>
            </div>
        </div>

        <div class="row">
            <div class="input-field col s12 m8 l6">
                <input name="picture_url" id="picture_url" type="text" class="validate" value="{{ old('picture_url') }}">
                <label for="picture_url">{{ trans('general.url') }} {{ trans('models.picture') }}</label>

            </div>
        </div>

        <div class="row">
            <div class="input-field col s12 m8 l6">
                <textarea id="obs" name="obs" class="materialize-textarea" rows="1">{{ old('obs') }}</textarea>
                <label for="obs">{{ trans('models.obs') }}</label>
            </div>
        </div>

        <div class="row">
            <div class="col s12">
                <div class="switch">
                    <label>
                        {{ trans('general.visible') }}
                        <input name="visible" type="hidden" value="false">
                        <input name="visible" type="checkbox" value="true" checked>
                        <span class="lever"></span>
                    </label>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="input-field col s12">
                @include('backoffice.partial.button', ['color' => 'green', 'icon' => 'send', 'text' => trans('general.create')])
            </div>
        </div>

    </form>
@endsection

@section('scripts')
    @include('backoffice.partial.update_team_list_js')
@endsection