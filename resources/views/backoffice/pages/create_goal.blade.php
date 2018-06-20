@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>{{ trans('general.add') }} {{ trans('models.goal') }}</title>
@endsection

@section('content')

    <div class="row">
        <div class="col s12">
            <h1>{{ trans('general.add') }} {{ trans('models.goal') }}</h1>
        </div>
    </div>

    @if(count($errors) > 0)
        <div class="row">
            <div class="col s12">
                @include('backoffice.partial.form_errors')
            </div>
        </div>
    @endif

    <form action="{{ route('goals.store') }}" method="POST">

        {{ csrf_field() }}

        @if($game)
            <div class="row">

                <div class="col s6 m4 l3">
                    <label>{{ trans('models.competition') }}</label>
                    <select id="competition_id" onchange="updateSeasonList('competition_id', 'season_id')" class="browser-default">
                        <option value="{{ $game->game_group->season->competition->id }}" selected>
                            {{ $game->game_group->season->competition->name }}
                        </option>

                        @foreach(\App\Competition::all() as $competition)
                            @if($competition->id != $game->game_group->season->competition->id)
                                <option value="{{ $competition->id }}">
                                    {{ $competition->name }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <div class="col s6 m4 l3">
                    <label>{{ trans('models.season') }}</label>
                    <select onchange="updateGameGroupsList('season_id', 'game_group_id')" name ="season_id" id="season_id" class="browser-default">
                        <option value="{{ $game->game_group->season->id }}" selected>
                            {{ $game->game_group->season->getName() }}
                        </option>

                        @foreach($game->game_group->season->competition->seasons as $season)
                            @if($season->id != $game->game_group->season->id)
                                <option value="{{ $season->id }}">
                                    {{ $season->getName() }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>

            </div>

            <div class="row">

                <div class="col s6 m4 l3">
                    <label>{{ trans('models.game_group') }}</label>
                    <select onchange="updateGamesList('game_group_id', 'game_id')" id="game_group_id" name="game_group_id" class="browser-default" required>
                        <option value="{{ $game->game_group->id }}" selected>{{ $game->game_group->name }}</option>

                        @foreach($game->game_Group->season->game_groups as $game_group)
                            @if($game_group->id != $game->game_group->id)
                                <option value="{{ $game_group->id }}">{{ $game_group->name }}</option>
                            @endif
                        @endforeach

                    </select>
                </div>

                <div class="col s6 m4 l3">
                    <label>{{ trans('models.game') }}</label>
                    <select onchange="updateGameTeams('game_id', 'selected_team_id')" name ="game_id" id="game_id" class="browser-default">
                        <option value="{{ $game->id }}" selected>
                            {{ $game->homeTeam->club->name }} vs {{ $game->awayTeam->club->name }}
                        </option>

                        @foreach($game->game_group->games as $other_game)
                            @if($other_game->id != $game->id)
                                <option value="{{ $other_game->id }}">
                                    {{ $other_game->homeTeam->club->name }} vs {{ $other_game->awayTeam->club->name }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>

            </div>

            <div class="row">

                <div class="col s6 m4 l3">
                    <label>{{ trans('models.team') }}</label>
                    <select onchange="updateGamePlayers('selected_team_id', 'player_id', 'game_id')" name ="selected_team_id" id="selected_team_id" class="browser-default">
                        <option disabled
                                value="" selected>
                            {{ trans('general.choose_option')}}
                        </option>

                        <option value="{{ $game->homeTeam->id }}">
                            {{ $game->homeTeam->club->name }}
                        </option>

                        <option value="{{ $game->awayTeam->id }}">
                            {{ $game->awayTeam->club->name }}
                        </option>
                    </select>


                </div>

                <div class="col s6 m4 l3">
                    <label>{{ trans('models.player') }}</label>
                    <select name ="player_id" id="player_id" class="browser-default" disabled>
                        <option disabled
                                value="" selected>
                            {{ trans('general.choose_first', ['name' => trans('models.team')])}}
                        </option>
                    </select>
                </div>
            </div>

        @else

            <div class="row">

                <div class="col s6 m4 l3">
                    <label>{{ trans('models.competition') }}</label>
                    <select id="competition_id" onchange="updateSeasonList('competition_id', 'season_id')" class="browser-default">
                        <option value="" disabled selected>{{ trans('general.choose_option') }}</option>

                        @foreach(\App\Competition::all() as $competition)
                                <option value="{{ $competition->id }}">
                                    {{ $competition->name }}
                                </option>
                        @endforeach
                    </select>
                </div>

                <div class="col s6 m4 l3">
                    <label>{{ trans('models.season') }}</label>
                    <select onchange="updateGameGroupsList('season_id', 'game_group_id')" name ="season_id" id="season_id" class="browser-default" disabled>
                        <option disabled
                                value="" selected>
                            {{ trans('general.choose_first', ['name' => trans('models.competition')])}}
                        </option>
                    </select>
                </div>

            </div>

            <div class="row">

                <div class="col s6 m4 l3">
                    <label>{{ trans('models.game_group') }}</label>
                    <select onchange="updateGamesList('game_group_id', 'game_id')" id="game_group_id" name="game_group_id" class="browser-default" disabled required>
                        <option value="0" disabled selected>{{ trans('general.choose_first', ['name' => trans('models.season')]) }}</option>
                    </select>
                </div>

                <div class="col s6 m3 l3">
                    <label>{{ trans('models.game') }}</label>
                    <select onchange="updateGameTeams('game_id', 'selected_team_id')" name="game_id" id="game_id" class="browser-default" disabled>
                        <option disabled
                                value="" selected>
                            {{ trans('general.choose_first', ['name' => trans('models.season')])}}
                        </option>
                    </select>
                </div>

            </div>

            <div class="row">

                <div class="col s6 m4 l3">
                    <label>{{ trans('models.team') }}</label>
                    <select onchange="updateGamePlayers('selected_team_id', 'player_id', 'game_id')" name ="selected_team_id" id="selected_team_id" class="browser-default" disabled>
                        <option disabled
                                value="0" selected>
                            {{ trans('general.choose_first', ['name' => trans('models.game')])}}
                        </option>
                    </select>
                </div>

                <div class="col s6 m4 l3">
                    <label>{{ trans('models.player') }}</label>
                    <select name ="player_id" id="player_id" class="browser-default" disabled>
                        <option disabled
                                value="" selected>
                            {{ trans('general.choose_first', ['name' => trans('models.team')])}}
                        </option>

                        <option value="">{{ trans('general.unknown')}}</option>

                    </select>
                </div>
            </div>

        @endif

        <div class="row">
            <div class="input-field col s2 m2 l1">
                <input id="minute" name="minute" type="number">
                <label for="minute">{{ trans('general.minute') }}</label>
            </div>
        </div>

        <div class="row">
            <div class="col s12">
                <div class="switch">
                    <label>
                        {{ trans('models.penalty') }}
                        <input name="penalty" type="hidden" value="false">
                        <input name="penalty" type="checkbox" value="true">
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
                        <input name="own_goal" type="checkbox" value="true">
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
                        <input name="visible" type="checkbox" value="true" checked>
                        <span class="lever"></span>
                    </label>
                </div>
            </div>
        </div>

        @include('backoffice.partial.button', ['color' => 'green', 'icon' => 'send', 'text' => trans('general.create')])

    </form>
@endsection

@section('scripts')

    @include('backoffice.partial.update_seasons_list_js')
    @include('backoffice.partial.update_games_list_js')
    @include('backoffice.partial.update_game_teams_js')
    @include('backoffice.partial.update_game_groups_js')
    @include('backoffice.partial.update_game_players_js')


@endsection