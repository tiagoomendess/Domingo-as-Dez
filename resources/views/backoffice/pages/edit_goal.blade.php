@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>{{ trans('general.edit') }} {{ trans('models.goal') }}</title>
@endsection

@section('content')

    <div class="row">
        <div class="col s12">
            <h1>{{ trans('general.edit') }} {{ trans('models.goal') }}</h1>
        </div>
    </div>

    @if(count($errors) > 0)
        <div class="row">
            <div class="col s12">
                @include('backoffice.partial.form_errors')
            </div>
        </div>
    @endif

    <form action="{{ route('goals.update', ['goal' => $goal]) }}" method="POST">

        {{ csrf_field() }}

        {{ method_field('PUT') }}


        <div class="row">

            <div class="col s6 m4 l3">
                <label>{{ trans('models.competition') }}</label>
                <select id="competition_id" class="browser-default" onchange="updateSeasonList('competition_id', 'season_id')">

                    <option value="{{ $goal->game->game_group->season->competition->id }}" selected>{{ $goal->game->game_group->season->competition->name }}</option>

                    @foreach(\App\Competition::all() as $competition)
                        @if($competition->id != $goal->game->game_group->season->competition->id)
                            <option value="{{ $competition->id }}">
                                {{ $competition->name }}
                            </option>
                        @endif
                    @endforeach
                </select>
            </div>

            <div class="col s6 m4 l3">
                <label>{{ trans('models.season') }}</label>
                <select name="season_id" id="season_id" class="browser-default" onchange="updateGameGroupsList('season_id', 'game_group_id')">

                    <option value="{{ $goal->game->game_group->season->id }}" selected>
                        {{ $goal->game->game_group->season->getName() }}
                    </option>

                    @foreach($goal->game->game_group->season->competition->seasons as $season)
                        @if($season->id != $goal->game->game_group->season->id)
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
                    <option value="{{ $goal->game->game_group->id }}" selected>{{ $goal->game->game_group->name }}</option>

                    @foreach($goal->game->game_Group->season->game_groups as $game_group)
                        @if($game_group->id != $goal->game->game_group->id)
                            <option value="{{ $game_group->id }}">{{ $game_group->name }}</option>
                        @endif
                    @endforeach
                </select>
            </div>

            <div class="col s6 m4 l3">
                <label>{{ trans('models.game') }}</label>
                <select onchange="updateGameTeams('game_id', 'selected_team_id')" name="game_id" id="game_id" class="browser-default">
                    <option value="{{ $goal->game->id }}" selected>
                        {{ $goal->game->homeTeam->club->name }} vs {{ $goal->game->awayTeam->club->name }}
                    </option>

                    @foreach($goal->game->game_group->games as $other_game)
                        @if($other_game->id != $goal->game->id)
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
                <select name ="selected_team_id" id="selected_team_id" class="browser-default" onchange="updateGamePlayers('selected_team_id', 'player_id', 'game_id')">

                    @if($goal->team->id == $goal->game->homeTeam->id)

                        <option selected value="{{ $goal->game->homeTeam->id }}">
                            {{ $goal->game->homeTeam->club->name }}
                        </option>

                        <option value="{{ $goal->game->awayTeam->id }}">
                            {{ $goal->game->awayTeam->club->name }}
                        </option>

                    @else

                        <option value="{{ $goal->game->homeTeam->id }}">
                            {{ $goal->game->homeTeam->club->name }}
                        </option>

                        <option selected value="{{ $goal->game->awayTeam->id }}">
                            {{ $goal->game->awayTeam->club->name }}
                        </option>

                    @endif


                </select>

            </div>

            <div class="col s6 m4 l3">
                <label>{{ trans('models.player') }}</label>
                <select name="player_id" id="player_id" class="browser-default">


                    @if($goal->player)

                        @foreach($goal->team->getCurrentPlayers() as $player)

                            @if($player->id != $goal->player->id)
                                <option value="{{ $player->id }}"> {{ $player->displayName() }}</option>
                            @endif

                        @endforeach

                        <option value="{{ $goal->player->id }}" selected>{{  $goal->player->displayName() }}</option>

                        <option value="">
                            {{ trans('general.unknown') }}
                        </option>

                    @else
                        <option value="" selected>
                            {{ trans('general.unknown') }}
                        </option>

                        @foreach($goal->team->getCurrentPlayers() as $player)
                            <option value="{{ $player->id }}"> {{ $player->displayName() }}</option>
                        @endforeach

                    @endif
                </select>
            </div>
        </div>


        <div class="row">
            <div class="input-field col s2 m2 l1">
                <input id="minute" name="minute" type="number" value="{{ old('minute', $goal->minute) }}">
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
                            <input name="penalty" type="checkbox" value="true" checked>
                        @else
                            <input name="penalty" type="checkbox" value="true">
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
                            <input name="own_goal" type="checkbox" value="true" checked>
                        @else
                            <input name="own_goal" type="checkbox" value="true">
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
                            <input name="visible" type="checkbox" value="true" checked>
                        @else
                            <input name="visible" type="checkbox" value="true">
                        @endif
                        <span class="lever"></span>
                    </label>
                </div>
            </div>
        </div>

        @include('backoffice.partial.button', ['color' => 'green', 'icon' => 'save', 'text' => trans('general.save')])

    </form>
@endsection

@section('scripts')

    @include('backoffice.partial.update_seasons_list_js')
    @include('backoffice.partial.update_games_list_js')
    @include('backoffice.partial.update_game_teams_js')
    @include('backoffice.partial.update_game_players_js')

@endsection