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

                    <option value="{{ $goal->game->season->competition->id }}" selected>{{ $goal->game->season->competition->name }}</option>

                    @foreach(\App\Competition::all() as $competition)
                        @if($competition->id != $goal->game->season->competition->id)
                            <option value="{{ $competition->id }}">
                                {{ $competition->name }}
                            </option>
                        @endif
                    @endforeach
                </select>
            </div>

            <div class="col s6 m4 l3">
                <label>{{ trans('models.season') }}</label>
                <select name="season_id" id="season_id" class="browser-default" onchange="updateGamesList('season_id', 'game_id')">

                    <option value="{{ $goal->game->season->id }}" selected>
                        {{ $goal->game->season->start_year }}/{{ $goal->game->season->end_year }}
                    </option>

                    @foreach($goal->game->season->competition->seasons as $season)
                        @if($season->id != $goal->game->season->id)
                            <option value="{{ $season->id }}">
                                {{ $season->start_year }}/{{ $season->end_year }}
                            </option>
                        @endif
                    @endforeach
                </select>
            </div>

        </div>

        <div class="row">

            <div class="col s12 m8 l6">
                <label>{{ trans('models.game') }}</label>
                <select onchange="updateGameTeams('game_id', 'selected_team_id')" name="game_id" id="game_id" class="browser-default">
                    <option value="{{ $goal->game->id }}" selected>
                        {{ $goal->game->homeTeam->club->name }} vs {{ $goal->game->awayTeam->club->name }}
                    </option>

                    @foreach($goal->game->season->games as $other_game)
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

                    <option value="{{ $goal->player->id }}" selected>
                        @if($goal->player->nickname)
                            {{ $goal->player->name }} ({{ $goal->player->nickname }})
                        @else
                            {{ $goal->player->name }}
                        @endif
                    </option>
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