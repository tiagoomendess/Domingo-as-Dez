@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>{{ trans('models.goal') }}</title>
@endsection

@section('content')

    <div class="row">
        <div class="col s12">
            <h1>{{ trans('models.goal') }}</h1>
        </div>
    </div>

            <div class="row">

                <div class="col s6 m4 l3">
                    <label>{{ trans('models.competition') }}</label>
                    <select disabled class="browser-default">

                        <option selected>{{ $goal->game->game_group->season->competition->name }}</option>

                    </select>

                </div>

                <div class="col s6 m4 l3">
                    <label>{{ trans('models.season') }}</label>
                    <select disabled name ="season_id" id="season_id" class="browser-default">
                        <option selected>{{ $goal->game->game_group->season->getName() }}</option>
                    </select>
                </div>

            </div>

            <div class="row">

                <div class="col s6 m4 l3">
                    <label>{{ trans('models.game_group') }}</label>
                    <select id="game_group_id" name="game_group_id" class="browser-default" disabled required>
                        <option value="0" disabled selected>{{ $goal->game->game_group->name }}</option>
                    </select>
                </div>

                <div class="col s6 m4 l3">
                    <label>{{ trans('models.game') }}</label>
                    <select disabled name ="game_id" id="game_id" class="browser-default">
                        <option selected>
                            {{ $goal->game->homeTeam->club->name }} vs {{ $goal->game->awayTeam->club->name }}
                        </option>
                    </select>
                </div>

            </div>

            <div class="row">

                <div class="col s6 m4 l3">
                    <label>{{ trans('models.team') }}</label>
                    <select disabled name ="selected_team_id" id="selected_team_id" class="browser-default">

                        <option selected disabled>
                            {{ $goal->team->club->name }}
                        </option>

                    </select>

                </div>

                <div class="col s6 m4 l3">
                    <label>{{ trans('models.player') }}</label>
                    <select name ="player_id" id="player_id" class="browser-default" disabled>
                        @if($goal->getPlayerNickname())
                            <option disabled selected>{{ $goal->getPlayerName() }} ({{ $goal->getPlayerNickname() }})</option>
                        @else
                            <option disabled selected>{{ $goal->getPlayerName() }}</option>
                        @endif
                    </select>
                </div>
            </div>

        <div class="row">
            <div class="input-field col s2 m2 l1">
                <input disabled id="minute" name="minute" type="number" value="{{ $goal->minute }}">
                <label for="minute">{{ trans('general.minute') }}</label>
            </div>
        </div>

        <div class="row">
            <div class="col s12">
                <div class="switch">
                    <label>
                        {{ trans('models.penalty') }}
                        <input name="penalty" type="hidden" value="false">
                        @if($goal->penalty)
                            <input disabled name="visible" type="checkbox" value="true" checked>
                        @else
                            <input disabled name="visible" type="checkbox" value="true">
                        @endif
                        <span class="lever"></span>
                    </label>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col s12">
                <div class="switch">
                    <label>
                        {{ trans('models.own_goal') }}
                        <input name="own_goal" type="hidden" value="false">
                        @if($goal->own_goal)
                            <input disabled name="visible" type="checkbox" value="true" checked>
                        @else
                            <input disabled name="visible" type="checkbox" value="true">
                        @endif
                        <span class="lever"></span>
                    </label>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col s12">
                <div class="switch">
                    <label>
                        {{ trans('general.visible') }}
                        <input name="visible" type="hidden" value="false">
                        @if($goal->visible)
                            <input disabled name="visible" type="checkbox" value="true" checked>
                        @else
                            <input disabled name="visible" type="checkbox" value="true">
                        @endif
                        <span class="lever"></span>
                    </label>
                </div>
            </div>
        </div>

    @if(Auth::user()->haspermission('goals.edit'))
        @include('backoffice.partial.model_options', [
            'delete_route' => route('goals.destroy', ['goal' => $goal]),
            'edit_route' => route('goals.edit', ['goal' => $goal])
        ])
    @endif

@endsection