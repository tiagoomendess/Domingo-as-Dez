@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>{{ trans('general.add') }} {{ trans('models.season') }}</title>
@endsection

@section('content')

    <div class="row">
        <div class="col s12">
            <h1>{{ trans('general.add') }} {{ trans('models.season') }}</h1>
        </div>
    </div>

    @if(count($errors) > 0)
        <div class="row">
            <div class="col s12">
                @include('backoffice.partial.form_errors')
            </div>
        </div>
    @endif

    <form action="{{ route('seasons.store') }}" method="POST">

        {{ csrf_field() }}

        <div class="row">
            <div class="input-field col s6 m4 l3">
                <input required name="start_year" id="start_year" type="number" class="validate" value="{{ old('start_year') }}">
                <label for="start_year">{{ trans('models.start_year') }}</label>
            </div>

            <div class="input-field col s6 m4 l3">
                <input required name="end_year" id="end_year" type="number" class="validate" value="{{ old('end_year') }}">
                <label for="end_year">{{ trans('models.end_year') }}</label>
            </div>
        </div>

        <div class="row">
            <div class="input-field col s6 m4 l3">
                <input required name="promotes" id="promotes" type="number" class="validate" value="{{ old('promotes') }}">
                <label for="promotes">{{ trans('models.promotes') }}</label>
            </div>

            <div class="input-field col s6 m4 l3">
                <input required name="relegates" id="relegates" type="number" class="validate" value="{{ old('relegates') }}">
                <label for="relegates">{{ trans('models.relegates') }}</label>
            </div>
        </div>

        <div class="row">
            <div class="col s12 m8 l6">
                <label>{{ trans('models.competition') }}</label>
                <select name="competition" class="browser-default" required>
                    <option value="none" disabled selected>{{ trans('general.choose_option') }}</option>

                    @foreach(\App\Competition::all() as $competition)
                        <option value="{{ $competition->id }}">{{ $competition->name }}</option>
                    @endforeach

                </select>
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