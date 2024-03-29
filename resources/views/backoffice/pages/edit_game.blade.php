@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>{{ trans('general.edit') }} {{ trans('models.game') }}</title>
@endsection

@section('content')

    <div class="row">
        <div class="col s12">
            <h1>{{ trans('general.edit') }} {{ trans('models.game') }}</h1>
        </div>
    </div>

    @if(count($errors) > 0)
        <div class="row">
            <div class="col s12">
                @include('backoffice.partial.form_errors')
            </div>
        </div>
    @endif

    <form action="{{ route('games.update', ['game' => $game]) }}" method="POST">

        {{ csrf_field() }}

        {{ method_field('PUT') }}

        <div class="row">

            <div class="col s6 m4 l3">
                <label>{{ trans('general.home_club') }}</label>
                <select onchange="updateTeamList('home_club_id', 'home_team_id')" id="home_club_id" name="club_id" class="browser-default" required>
                    <option value="{{ $game->homeTeam->club->id }}" selected>{{ $game->homeTeam->club->name  }}</option>
                    @foreach(App\Club::all() as $club)

                        @if($club->id != $game->homeTeam->club->id)
                            <option value="{{ $club->id }}">{{ $club->name }}</option>
                        @endif

                    @endforeach
                </select>
            </div>

            <div class="col s6 m4 l3">
                <label>{{ trans('general.home_team') }}</label>
                <select id="home_team_id" name="home_team_id" class="browser-default" required>
                    <option value="{{ $game->homeTeam->id }}" selected>{{ $game->homeTeam->name }}</option>
                    @foreach($game->homeTeam->club->teams as $team)

                        @if ($game->id != $game->homeTeam->id)
                            <option value="{{ $team->id }}">{{ $team->name }}</option>
                        @endif

                    @endforeach
                </select>
            </div>

        </div>

        <div class="row">

            <div class="col s6 m4 l3">
                <label>{{ trans('general.away_club') }}</label>
                <select onchange="updateTeamList('away_club_id', 'away_team_id')" id="away_club_id" name="club_id" class="browser-default" required>
                    <option value="{{ $game->awayTeam->club->id }}" selected>{{ $game->awayTeam->club->name }}</option>
                    @foreach(App\Club::all() as $club)
                        @if($club->id != $game->awayTeam->club->id)
                            <option value="{{ $club->id }}">{{ $club->name }}</option>
                        @endif
                    @endforeach
                </select>
            </div>

            <div class="col s6 m4 l3">
                <label>{{ trans('general.away_team') }}</label>
                <select id="away_team_id" name="away_team_id" class="browser-default" required>
                    <option value="{{ $game->awayTeam->id }}" selected>{{ $game->awayTeam->name }}</option>

                    @foreach($game->awayTeam->club->teams as $team)

                        @if ($game->id != $game->awayTeam->id)
                            <option value="{{ $team->id }}">{{ $team->name }}</option>
                        @endif

                    @endforeach
                </select>
            </div>

        </div>

        <div class="row">

            <div class="col s6 m4 l3">
                <label>{{ trans('models.competition') }}</label>
                <select onchange="updateSeasonList('competition_id', 'season_id')" id="competition_id" name="competition_id" class="browser-default" required>
                    <option value="{{ $game->game_group->season->competition->id }}" selected>{{ $game->game_group->season->competition->name }}</option>
                    @foreach(App\Competition::all() as $competition)
                        @if ($competition->id != $game->game_group->season->competition->id)
                            <option value="{{ $competition->id }}">{{ $competition->name }}</option>
                        @endif

                    @endforeach
                </select>
            </div>

            <div class="col s6 m4 l3">
                <label>{{ trans('models.season') }}</label>
                <select onchange="updateGameGroupsList('season_id', 'game_group_id')" id="season_id" name="season_id" class="browser-default" required>

                    <option value="{{ $game->game_group->season->id }}" selected>{{ $game->game_group->season->getName() }}</option>


                    @foreach($game->game_group->season->competition->seasons as $season)

                        @if($season->id != $game->game_group->season->id)
                            <option value="{{ $season->id }}">{{ $season->getName() }}</option>
                        @endif

                    @endforeach
                </select>
            </div>

        </div>

        <div class="row">

            <div class="col s6 m4 l3">
                <label for="game_group_id">{{ trans('models.game_group') }}</label>
                <select id="game_group_id" name="game_group_id" class="browser-default" required>

                    @foreach($game->game_group->season->game_groups as $game_group)
                        @if($game_group->id != $game->game_group->id)
                            <option value="{{ $game_group->id }}">{{ $game_group->name }}</option>
                        @endif
                    @endforeach

                    <option value="{{ $game->game_group->id }}" selected>{{ $game->game_group->name }}</option>

                </select>
            </div>

            <div class="input-field col s6 m4 l3">
                <input type="number" name="round" id="round" required value="{{ old('round', $game->round) }}">
                <label for="round">{{ trans('general.round') }}</label>
            </div>

        </div>

        <div class="row">

            <?php
            $carbon = \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $game->date);
            $user = \Illuminate\Support\Facades\Auth::user();
            $carbon->timezone = $user->profile->timezone;
            ?>

            <div class="input-field col s4 m3 l2">
                <input id="date" name="date" type="text" class="datepicker" required value="{{$carbon->format("Y-m-d")}}">
                <label for="date">{{ trans('general.day') }}</label>
            </div>

            <div class="input-field col s4 m2 l2">
                <input id="hour" name="hour" type="text" class="timepicker" required value="{{ $carbon->format("H:i") }}">
                <label for="hour">{{ trans('general.hour') }}</label>
            </div>

            <div class="col s4 m3 l2">
                @include('backoffice.partial.select_timezone', ['timezone_name' => $user->profile->timezone, 'timezone_value' => $user->profile->timezone])
            </div>

        </div>

        <div class="row">
            <div class="input-field col s6 m4 l3">
                <input type="number" name="goals_home" id="goals_home" value="{{ $game->goals_home }}">
                <label for="goals_home">{{ trans('general.goals_home') }}</label>
            </div>

            <div class="input-field col s6 m4 l3">
                <input type="number" name="goals_away" id="goals_away" value="{{ $game->goals_away }}">
                <label for="goals_away">{{ trans('general.goals_away') }}</label>
            </div>
        </div>

        <div class="row">
            <div class="input-field col s6 m4 l3">
                <input type="number" name="penalties_home" id="penalties_home" value="{{ old('penalties_home', $game->penalties_home) }}">
                <label for="penalties_home">{{ trans('general.penalties_home') }}</label>
            </div>

            <div class="input-field col s6 m4 l3">
                <input type="number" name="penalties_away" id="penalties_away" value="{{ old('penalties_away', $game->penalties_away) }}">
                <label for="penalties_away">{{ trans('general.penalties_away') }}</label>
            </div>
        </div>

        <div class="row">

            <div class="col s12 m8 l6">
                <label for="playground_id">{{ trans('models.playground') }}</label>
                <select id="playground_id" name="playground_id" class="browser-default">
                    @if ($game->playground)

                        <option value="{{ $game->playground->id }}" selected>{{ $game->playground->name }}</option>

                        @foreach(App\Playground::all() as $playground)

                            @if ($playground->id != $game->playground->id)
                                <option value="{{ $playground->id }}">{{ $playground->name }}</option>
                            @endif

                        @endforeach

                    @else

                        <option value="" selected>{{ trans('general.none') }}</option>

                        @foreach(App\Playground::all() as $playground)
                            <option value="{{ $playground->id }}">{{ $playground->name }}</option>
                        @endforeach

                    @endif


                </select>
            </div>
        </div>

        <div class="row no-margin-bottom">
            <div id="game_referee_hidden" class="hide row">

                <div class="col s6 m4 l3">
                    <label>{{ trans('models.referee') }}</label>
                    <select id="i_referee_id" class="browser-default">
                        <option value="" disabled selected>{{ trans('general.choose_option') }}</option>
                        @foreach(\App\Referee::all() as $ref)
                            <option value="{{ $ref->id }}">{{ $ref->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col s5 m3 l2">
                    <label>{{ trans('general.type') }}</label>
                    <select id="i_type_id" class="browser-default">
                        <option value="" disabled selected>{{ trans('general.choose_option') }}</option>
                        @foreach(\App\RefereeType::all() as $ref_type)
                            <option value="{{ $ref_type->id }}">{{ trans('general.' . $ref_type->name) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col s1 m1 l1" style="min-height: 65px;">
                    <div class="right" style="min-height: 30px; margin-top: 30px;">
                        <a style="color: red; cursor: pointer;"><i class="material-icons left">close</i></a>
                    </div>
                </div>

            </div>

            <div id="game_referees">
                <?php $ref_i = 0; ?>
                @foreach($game->game_referees as $game_referee)

                    <div id="ref_{{ $ref_i }}" class="row">

                        <div class="col s6 m4 l3">
                            <label>{{ trans('models.referee') }}</label>
                            <select name="referees_id[]" class="browser-default">
                                <option value="{{ $game_referee->referee->id }}" selected>{{ $game_referee->referee->name }}</option>
                                @foreach(\App\Referee::all() as $ref)
                                    @if($ref->id != $game_referee->referee->id)
                                        <option value="{{ $ref->id }}">{{ $ref->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <div class="col s5 m3 l2">
                            <label>{{ trans('general.type') }}</label>
                            <select name="types_id[]" class="browser-default">
                                <option value="{{ $game_referee->referee_type->id }}" selected>{{ trans('general.' . $game_referee->referee_type->name) }}</option>
                                @foreach(\App\RefereeType::all() as $ref_type)
                                    @if($ref_type->id != $game_referee->referee_type->id)
                                        <option value="{{ $ref_type->id }}">{{ trans('general.' . $ref_type->name) }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <div class="col s1 m1 l1" style="min-height: 65px;">
                            <div class="right" style="min-height: 30px; margin-top: 30px;">
                                <a onclick="removeReferee({{ $ref_i }})" style="color: red; cursor: pointer;"><i class="material-icons left">close</i></a>
                            </div>
                        </div>

                    </div>
                    <?php $ref_i++; ?>
                @endforeach
            </div>

        </div>

        <div class="row">
            <div class="col s12 m8 l6">
                <a onclick="addReferee()" style="width: 100%;" class="waves-effect waves-light btn grey"><i class="material-icons left">add</i>{{ trans('general.add') }} {{ trans('models.referee') }}</a>
            </div>
        </div>

        <div class="row">
            <div class="col s12">
                <div class="switch">
                    <label>
                        {{ trans('general.finished') }}
                        <input name="finished" type="hidden" value="false">
                        @if($game->finished)
                            <input name="finished" type="checkbox" value="true" checked>
                        @else
                            <input name="finished" type="checkbox" value="true">
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
                        Adiado
                        <input name="postponed" type="hidden" value="false">
                        @if($game->postponed)
                            <input name="postponed" type="checkbox" value="true" checked>
                        @else
                            <input name="postponed" type="checkbox" value="true">
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
                        {{ trans('general.scoreboard_updates') }}
                        <input name="scoreboard_updates" type="hidden" value="false">
                        @if($game->scoreboard_updates)
                            <input name="scoreboard_updates" type="checkbox" value="true" checked>
                        @else
                            <input name="scoreboard_updates" type="checkbox" value="true">
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
                        @if($game->visible)
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

@section('scripts')
    @include('backoffice.partial.manage_refs_js')
    @include('backoffice.partial.update_team_list_js')
    @include('backoffice.partial.update_seasons_list_js')
    @include('backoffice.partial.update_game_groups_js')
    @include('backoffice.partial.pick_a_date_js')
    @include('backoffice.partial.pick_a_time_js')
@endsection