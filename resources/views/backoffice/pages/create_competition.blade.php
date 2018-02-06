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

    <form action="{{ route('competitions.store') }}" method="POST">

        {{ csrf_field() }}

        <div class="row">
            <div class="input-field col s12 l12">
                <input required name="name" id="name" type="text" class="validate" value="{{ old('name') }}">
                <label for="name">{{ trans('general.name') }}</label>
            </div>
        </div>

        <div class="row">
            <div class="col s12 l4">
                <label>Browser Select</label>
                <select class="browser-default">
                    <option value="" disabled selected>Choose your option</option>
                    <option value="1">Option 1</option>
                    <option value="2">Option 2</option>
                    <option value="3">Option 3</option>
                </select>
            </div>
        </div>

        <div class="row">
            <div class="file-field input-field col s12">
                <div class="btn">
                    <span>File</span>
                    <input type="file">
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