@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>{{ trans('models.season') }}</title>
@endsection

@section('content')

    <div class="row">
        <div class="col s12">
            <h1>{{ trans('models.season') }}</h1>
        </div>
    </div>

    <div class="row">
        <div class="input-field col s6 m4 l3">
            <input disabled name="start_year" id="start_year" type="number" class="validate" value="{{ $season->start_year }}">
            <label for="start_year">{{ trans('models.start_year') }}</label>
        </div>

        <div class="input-field col s6 m4 l3">
            <input disabled name="end_year" id="end_year" type="number" class="validate" value="{{ $season->end_year }}">
            <label for="end_year">{{ trans('models.end_year') }}</label>
        </div>
    </div>

    <div class="row">
        <div class="input-field col s6 m4 l3">
            <input disabled name="promotes" id="promotes" type="number" class="validate" value="{{ $season->promotes }}">
            <label for="promotes">{{ trans('models.promotes') }}</label>
        </div>

        <div class="input-field col s6 m4 l3">
            <input disabled name="relegates" id="relegates" type="number" class="validate" value="{{ $season->relegates }}">
            <label for="relegates">{{ trans('models.relegates') }}</label>
        </div>
    </div>

    <div class="row">
        <div class="col s12 m4 l3">
            <label>{{ trans('models.competition') }}</label>
            <select name="competition" class="browser-default" disabled>
                <option value="none" disabled selected>{{ \App\Competition::find($season->competition_id)->name }}</option>
            </select>
        </div>

        <div class="col s12 m4 l3">
            <label>{{ trans('models.table_rules') }}</label>
            <select name="table_rules" class="browser-default" disabled>

                @if($season->table_rules)
                    <option selected value="{{ $season->table_rules }}">{{ trans('models.' . $season->table_rules) }}</option>
                @else
                    <option selected value="none">{{ trans('general.none') }}</option>
                @endif

            </select>
        </div>
    </div>

    <div class="row">
        <div class="input-field col s12 m8 l6">
            <textarea disabled id="obs" name="obs" class="materialize-textarea" rows="1">{{ $season->obs }}</textarea>
            <label for="obs">{{ $season->obs }}</label>
        </div>
    </div>

    <div class="row">
        <div class="col s12">
            <div class="switch">
                <label>
                    {{ trans('general.visible') }}
                    <input name="visible" type="hidden" value="false">
                    @if($season->visible)
                        <input disabled name="visible" type="checkbox" value="true" checked>
                    @else
                        <input disabled name="visible" type="checkbox" value="true">
                    @endif

                    <span class="lever"></span>
                </label>
            </div>
        </div>
    </div>

    @if(Auth::user()->haspermission('seasons.edit'))
        @include('backoffice.partial.model_options', [
            'delete_route' => route('seasons.destroy', ['season' => $season]),
            'edit_route' => route('seasons.edit', ['season' => $season])
        ])
    @endif

@endsection