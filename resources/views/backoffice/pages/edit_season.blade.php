@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>{{ trans('general.edit') }} {{ trans('models.season') }}</title>
@endsection

@section('content')

    <div class="row">
        <div class="col s12">
            <h1>{{ trans('general.edit') }} {{ trans('models.season') }}</h1>
        </div>
    </div>

    @if(count($errors) > 0)
        <div class="row">
            <div class="col s12">
                @include('backoffice.partial.form_errors')
            </div>
        </div>
    @endif

    <form action="{{ route('seasons.update', ['season' => $season]) }}" method="POST">

        {{ csrf_field() }}

        {{ method_field('PUT') }}

        <div class="row">
            <div class="input-field col s6 m4 l3">
                <input required name="start_year" id="start_year" type="number" class="validate" value="{{ old('start_year', $season->start_year) }}">
                <label for="start_year">{{ trans('models.start_year') }}</label>
            </div>

            <div class="input-field col s6 m4 l3">
                <input required name="end_year" id="end_year" type="number" class="validate" value="{{ old('end_year', $season->end_year) }}">
                <label for="end_year">{{ trans('models.end_year') }}</label>
            </div>
        </div>

        <div class="row">
            <div class="input-field col s6 m4 l3">
                <input required name="promotes" id="promotes" type="number" class="validate" value="{{ old('promotes', $season->promotes) }}">
                <label for="promotes">{{ trans('models.promotes') }}</label>
            </div>

            <div class="input-field col s6 m4 l3">
                <input required name="relegates" id="relegates" type="number" class="validate" value="{{ old('relegates', $season->relegates) }}">
                <label for="relegates">{{ trans('models.relegates') }}</label>
            </div>
        </div>

        <div class="row">
            <div class="col s12 m4 l3">
                <label>{{ trans('models.competition') }}</label>
                <select name="competition" class="browser-default" required>
                    <option value="{{ $season->competition_id }}" selected>{{ \App\Competition::find($season->competition_id)->name }}</option>

                    @foreach(\App\Competition::all() as $competition)

                        @if($competition->id != $season->competition_id)
                            <option value="{{ $competition->id }}">{{ $competition->name }}</option>
                        @endif

                    @endforeach

                </select>
            </div>

            <div class="col s12 m4 l3">
                <label>{{ trans('models.table_rules') }}</label>
                <select name="table_rules" class="browser-default">

                    @if($season->table_rules)
                        <option selected value="{{ $season->table_rules }}">{{ trans('models.' . $season->table_rules) }}</option>
                    @else
                        <option selected value="none">{{ trans('general.none') }}</option>
                    @endif

                    <option value="points_only">{{ trans('models.points_only') }}</option>
                    <option value="afpb_league">{{ trans('models.afpb_league') }}</option>
                    <option value="afpb_cup">{{ trans('models.afpb_cup') }}</option>
                    <option value="cup">{{ trans('models.cup') }}</option>
                    <option value="fpf_league">{{ trans('models.fpf_league') }}</option>
                    <option value="fpf_cup">{{ trans('models.fpf_cup') }}</option>
                    <option value="liga_portugal">{{ trans('models.liga_portugal') }}</option>

                </select>
            </div>

        </div>

        <div class="row">
            <div class="input-field col s12 m8 l6">
                <textarea id="obs" name="obs" class="materialize-textarea" rows="1">{{ old('obs', $season->obs) }}</textarea>
                <label for="obs">{{ trans('models.obs') }}</label>
            </div>
        </div>

        <div class="row">
            <div class="col s12">
                <div class="switch">
                    <label>
                        {{ trans('general.visible') }}
                        <input name="visible" type="hidden" value="false">
                        @if($season->visible)
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