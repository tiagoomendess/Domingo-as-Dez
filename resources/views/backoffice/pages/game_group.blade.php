@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>{{ trans('models.game_group') }}</title>
@endsection

@section('content')

    <div class="row">
        <div class="col s12">
            <h1>{{ trans('models.game_group') }}</h1>
        </div>
    </div>


    <div class="row">
        <div class="input-field col s12 m8 l6">
            <input disabled required name="name" id="name" type="text" class="validate" value="{{ $game_group->name }}">
            <label for="name">{{ trans('models.name') }}</label>
        </div>
    </div>

    <div class="row">

        <div class="col s6 m4 l3">
            <label>{{ trans('models.competition') }}</label>
            <select disabled id="competition_id" name="competition_id" class="browser-default" required>
                <option disabled value="0" selected>{{ $game_group->season->competition->name }}</option>
            </select>
        </div>

        <div class="col s6 m4 l3">
            <label>{{ trans('models.season') }}</label>
            <select id="season_id" name="season_id" class="browser-default" disabled required>
                <option value="0" disabled selected>{{ $game_group->season->getName() }}</option>
            </select>
        </div>

    </div>

    <div class="row">
        <div class="col s12 m4 l3">
            <label>{{ trans('models.group_rules') }}</label>
            <select disabled name="group_rules_id" class="browser-default" required>
                <option selected value="0">{{ $game_group->group_rules->name }}</option>
            </select>
        </div>

    </div>

    @if(Auth::user()->haspermission('game_groups.edit'))
        @include('backoffice.partial.model_options', [
            'delete_route' => route('gamegroups.destroy', ['game_group' => $game_group]),
            'edit_route' => route('gamegroups.edit', ['game_group' => $game_group])
        ])
    @endif


@endsection

@section('scripts')
    @include('backoffice.partial.update_seasons_list_js')
@endsection