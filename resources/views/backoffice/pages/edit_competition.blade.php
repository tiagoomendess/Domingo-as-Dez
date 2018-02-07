@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>{{ trans('general.edit') }} {{ trans('models.competition') }}</title>
@endsection

@section('content')

    <div class="row">
        <div class="col s12">
            <h1>{{ trans('general.edit') }} {{ trans('models.competition') }}</h1>
        </div>
    </div>

    <div class="row">
        <div class="col s12">
            @include('backoffice.partial.form_errors')
        </div>
    </div>

    <form action="{{ route('competitions.update', ['competition' => $competition]) }}" method="POST" enctype="multipart/form-data">

        {{ csrf_field() }}

        {{ method_field('PUT') }}

        <div class="row">
            <div class="input-field col s12 l12">
                <input required name="name" id="name" type="text" class="validate" value="{{ $competition->name }}">
                <label for="name">{{ trans('general.name') }}</label>
            </div>
        </div>

        <div class="row">
            <div class="col s12 l4">
                <label>{{ trans('models.competition_type') }}</label>
                <select name="competition_type" class="browser-default" disabled>
                    <option value="none" disabled selected>{{ trans('models.' . $competition->competition_type) }}</option>
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
                    <input class="file-path validate" type="text" value="{{ $competition->picture }}">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col s12">
                <div class="switch">
                    <label>
                        {{ trans('general.visible') }}
                        <input name="visible" type="hidden" value="false">
                        @if($competition->visible)
                            <input name="visible" type="checkbox" value="true" checked>
                            @else
                            <input name="visible" type="checkbox" value="true">
                        @endif

                        <span class="lever"></span>
                    </label>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="input-field col s12">
                @include('backoffice.partial.button', ['color' => 'green', 'icon' => 'save', 'text' => trans('general.save')])
            </div>
        </div>

    </form>
@endsection