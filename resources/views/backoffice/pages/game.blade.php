@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>{{ trans('models.game') }}</title>
@endsection

@section('content')

    <div class="row">
        <div class="col s12">
            <h1>{{ trans('models.game') }}</h1>
        </div>
    </div>

    <div class="row">

        <div class="col s6 m4 l3">
            <label>{{ trans('general.home_club') }}</label>
            <select id="club_id" name="club_id" class="browser-default" disabled>
                <option disabled value="0" selected>{{ $game->homeTeam->club->name }}</option>
            </select>
        </div>

        <div class="col s6 m4 l3">
            <label>{{ trans('general.home_team') }}</label>
            <select id="home_team_id" name="home_team_id" class="browser-default" disabled disabled>
                <option value="0" disabled selected>{{ $game->homeTeam->name }}</option>
            </select>
        </div>

    </div>

    <div class="row">

        <div class="col s6 m4 l3">
            <label>{{ trans('general.away_club') }}</label>
            <select id="club_id" name="club_id" class="browser-default" disabled>
                <option disabled value="0" selected>{{ $game->awayTeam->club->name }}</option>
            </select>
        </div>

        <div class="col s6 m4 l3">
            <label>{{ trans('general.away_team') }}</label>
            <select id="away_team_id" name="away_team_id" class="browser-default" disabled>
                <option value="0" disabled selected>{{ $game->awayTeam->name }}</option>
            </select>
        </div>

    </div>

    <div class="row">

        <div class="col s5 m3 l2">
            <label>{{ trans('models.competition') }}</label>
            <select id="competition_id" name="competition_id" class="browser-default" disabled>
                <option disabled value="0" selected>{{ $game->season->competition->name }}</option>
            </select>
        </div>

        <div class="col s5 m3 l2">
            <label>{{ trans('models.season') }}</label>
            <select id="season_id" name="season_id" class="browser-default" disabled>

                @if($game->season->start_year != $game->season->end_year)
                    <option value="0" disabled selected>{{ $game->season->start_year }}/{{ $game->season->end_year }}</option>
                @else
                    <option value="0" disabled selected>{{ $game->season->start_year }}</option>
                @endif

            </select>
        </div>

        <div class="input-field col s2 m2 l2">
            <input type="number" name="round" id="round" disabled value="{{ $game->round }}">
            <label for="round">{{ trans('general.round') }}</label>
        </div>

    </div>

    <div class="row">

        <?php
        $carbon = \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $game->date);
        ?>
        <div class="input-field col s6 m4 l3">
            <input id="date" name="date" type="text" class="datepicker" disabled value="{{$carbon->format("Y-m-d")}}">
            <label for="date">{{ trans('general.day') }}</label>
        </div>

        <div class="input-field col s6 m4 l3">
            <input id="hour" name="hour" type="text" class="timepicker" disabled value="{{ $carbon->format("H:i") }}">
            <label for="hour">{{ trans('general.hour') }}</label>
        </div>
    </div>

    <div class="row">
        <div class="input-field col s6 m4 l3">
            <input type="number" name="goals_home" id="goals_home" value="{{ $game->goals_home }}" disabled>
            <label for="goals_home">{{ trans('general.goals_home') }}</label>
        </div>

        <div class="input-field col s6 m4 l3">
            <input type="number" name="goals_away" id="goals_away" value="{{ $game->goals_away }}" disabled>
            <label for="goals_away">{{trans('general.goals_away')}}</label>
        </div>
    </div>

    <div class="row">

        <div class="input-field col s6 m4 l3">
            <input type="text" name="table_group" value="{{ $game->table_group }}" disabled>
            <label for="table_group">{{ trans('models.group') }}</label>
        </div>

        <div class="col s6 m4 l3">
            <label>{{ trans('models.playground') }}</label>
            <select id="playground_id" name="playground_id" class="browser-default" disabled>
                @if($game->playground)
                    <option value="" selected>{{ $game->playground->name }}</option>
                @else
                    <option value="" selected>{{ trans('general.none') }}</option>
                @endif

            </select>
        </div>
    </div>

    <div class="row no-margin-bottom">

        <div id="game_referees">
            @foreach($game->game_referees as $game_referee)
                <div id="game_referee_hidden" class="row">

                    <div class="col s6 m4 l3">
                        <label>{{ trans('models.referee') }}</label>
                        <select id="i_referee_id" class="browser-default" disabled>
                            <option value="" disabled selected>{{ $game_referee->referee->name }}</option>
                        </select>
                    </div>

                    <div class="col s5 m3 l2">
                        <label>{{ trans('general.type') }}</label>
                        <select id="i_type_id" class="browser-default" disabled>
                            <option value="" disabled selected>{{ trans('general.' . $game_referee->referee_type->name) }}</option>
                        </select>
                    </div>

                    <div class="col s1 m1 l1" style="min-height: 65px;">
                        <div class="right" style="min-height: 30px; margin-top: 30px;">
                            <a style="color: gray; cursor: default;"><i class="material-icons left">close</i></a>
                        </div>
                    </div>

                </div>
            @endforeach
        </div>

    </div>

    <div class="row">
        <div class="col s12 m8 l6">
            <a disabled style="width: 100%;" class="waves-effect waves-light btn grey"><i class="material-icons left">add</i>{{ trans('general.add') }} {{ trans('models.referee') }}</a>
        </div>
    </div>

    <div class="row">
        <div class="col s12">
            <div class="switch">
                <label>
                    {{ trans('models.tie') }}
                    <input name="finished" type="hidden" value="false">
                    @if($game->tie)
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
                    {{ trans('general.finished') }}
                    <input name="finished" type="hidden" value="false">
                    @if($game->finished)
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
                    @if($game->visible)
                        <input disabled name="visible" type="checkbox" value="true" checked>
                    @else
                        <input disabled name="visible" type="checkbox" value="true">
                    @endif
                    <span class="lever"></span>
                </label>
            </div>
        </div>
    </div>

    @if(Auth::user()->haspermission('games.edit'))
        @include('backoffice.partial.model_options', [
            'delete_route' => route('games.destroy', ['game' => $game]),
            'edit_route' => route('games.edit', ['game' => $game])
        ])
    @endif

@endsection
