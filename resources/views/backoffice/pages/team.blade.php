@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>{{ trans('models.team') }}</title>
@endsection

@section('content')

    <div class="row">
        <div class="col s12">
            <h1>{{ trans('models.team') }}</h1>
        </div>
    </div>

    @if(count($errors) > 0)
        <div class="row">
            <div class="col s12">
                @include('backoffice.partial.form_errors')
            </div>
        </div>
    @endif

    <div class="row">
        <div class="input-field col s12 m8 l6">
            <input disabled name="name" id="name" type="text" class="validate" value="{{ $team->name }}">
            <label for="name">{{ trans('models.name') }}</label>
        </div>
    </div>

    <div class="row">
        <div class="col s12 m8 l6">
            <label>{{ trans('models.club') }}</label>
            <select name="club_id" class="browser-default" disabled>
                <option disabled selected>{{ $team->club->name }}</option>
            </select>
        </div>
    </div>

    <div class="row">
        <div class="col s12">
            <div class="switch">
                <label>
                    {{ trans('general.visible') }}
                    <input name="visible" type="hidden" value="false">
                    @if($team->visible)
                        <input disabled name="visible" type="checkbox" value="true" checked>
                    @else
                        <input disabled name="visible" type="checkbox" value="true">
                    @endif
                    <span class="lever"></span>
                </label>
            </div>
        </div>
    </div>

    @if(Auth::user()->haspermission('teams.edit'))
        @include('backoffice.partial.model_options', [
            'delete_route' => route('teams.destroy', ['team' => $team]),
            'edit_route' => route('teams.edit', ['team' => $team])
        ])
    @endif


@endsection