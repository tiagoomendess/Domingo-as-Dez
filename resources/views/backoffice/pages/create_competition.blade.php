@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>{{ trans('general.add') }} {{ trans('models.competition') }}</title>
@endsection

@section('content')

    <div class="row">
        <div class="col s12">
            <h1>{{ trans('general.add') }} {{ trans('models.competition') }}</h1>
        </div>
    </div>

    <div class="row">
        <div class="col s12">
            @include('backoffice.partial.form_errors')
        </div>
    </div>

    <form action="{{ route('competitions.store') }}" method="POST" enctype="multipart/form-data">

        {{ csrf_field() }}

        <div class="row">
            <div class="input-field col s12 l12">
                <input required name="name" id="name" type="text" class="validate" value="{{ old('name') }}">
                <label for="name">{{ trans('general.name') }}</label>
            </div>
        </div>

        <div class="row">
            <div class="col s12 l4">
                <label>{{ trans('models.competition_type') }}</label>
                <select name="competition_type" class="browser-default" required>
                    <option value="none" disabled selected>{{ trans('general.choose_option') }}</option>
                    <option value="friendly">{{ trans('models.friendly') }}</option>
                    <option value="cup">{{ trans('models.cup') }}</option>
                    <option value="league">{{ trans('models.league') }}</option>
                    <option value="tournament">{{ trans('models.tournament') }}</option>
                </select>
            </div>
        </div>

        <div class="row">
            <div class="file-field input-field col s12">
                <div class="btn">
                    <span>{{ trans('general.file') }}</span>
                    <input name="file" type="file">
                </div>
                <div class="file-path-wrapper">
                    <input class="file-path validate" type="text">
                </div>
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