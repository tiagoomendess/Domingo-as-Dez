@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>{{ trans('models.referee') }}</title>
@endsection

@section('content')

    <div class="row">
        <div class="col s12">
            <h1>{{ trans('models.referee') }}</h1>
        </div>
    </div>

    <div class="row">

        <div class="input-field col s12 m4 l3">
            <input name="name" id="name" type="text" class="validate" value="{{ $referee->name }}" disabled>
            <label for="name">{{ trans('general.name') }}</label>
        </div>

        <div class="input-field col s12 m4 l3">
            <input name="association" id="association" type="text" class="validate" value="{{ $referee->association }}" disabled>
            <label for="association">{{ trans('general.association') }}</label>
        </div>

    </div>

    <div class="row">

        <div class="col s12 m4 l3">
            @if($referee->picture)
                <img width="100%" class="materialboxed" src="{{ $referee->picture }}">
            @else
                <img width="100%" class="materialboxed"  src="{{ config('custom.default_profile_pic') }}">
            @endif
        </div>

    </div>

    <div class="row">
        <div class="input-field col s12 m8 l6">
            <textarea disabled id="obs" name="obs" class="materialize-textarea" rows="1">{{ $referee->obs }}</textarea>
            <label for="obs">{{ trans('models.obs') }}</label>
        </div>
    </div>

    <div class="row">
        <div class="col s12">
            <div class="switch">
                <label>
                    {{ trans('general.visible') }}
                    <input name="visible" type="hidden" value="false">
                    @if($referee->visible)
                        <input name="visible" type="checkbox" value="true" checked disabled>
                    @else
                        <input name="visible" type="checkbox" value="true" disabled>
                    @endif
                    <span class="lever"></span>
                </label>
            </div>
        </div>
    </div>

    @if(Auth::user()->haspermission('referees.edit'))
        @include('backoffice.partial.model_options', [
            'delete_route' => route('referees.destroy', ['referee' => $referee]),
            'edit_route' => route('referees.edit', ['referee' => $referee])
        ])
    @endif


@endsection