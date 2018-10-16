@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>{{ trans('general.add') }} {{ trans('models.game') }}</title>
@endsection

@section('content')

    <div class="row">
        <div class="col s12">
            <h1>{{ trans('general.add') }} {{ trans('models.game') }}</h1>
        </div>
    </div>

    @if(count($errors) > 0)
        <div class="row">
            <div class="col s12">
                @include('backoffice.partial.form_errors')
            </div>
        </div>
    @endif

    <form action="{{ route('games.store') }}" method="POST">

        {{ csrf_field() }}

        <div class="row">

            <div class="col s6 m4 l3">
                <label>{{ trans('general.home_club') }}</label>
                <select onchange="updateTeamList('home_club_id', 'home_team_id')" id="home_club_id" name="club_id" class="browser-default" required>
                    <option disabled value="0" selected>{{ trans('general.choose_option') }}</option>
                    @foreach(App\Club::all()->sortBy('name') as $club)
                        <option value="{{ $club->id }}">{{ $club->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col s6 m4 l3">
                <label>{{ trans('general.home_team') }}</label>
                <select id="home_team_id" name="home_team_id" class="browser-default" disabled required>
                    <option value="0" disabled selected>{{ trans('general.choose_first', ['name' => trans('general.home_club')]) }}</option>
                </select>
            </div>

        </div>

        <div class="row">

            <div class="col s6 m4 l3">
                <label>{{ trans('general.away_club') }}</label>
                <select onchange="updateTeamList('away_club_id', 'away_team_id')" id="away_club_id" name="club_id" class="browser-default" required>
                    <option disabled value="0" selected>{{ trans('general.choose_option') }}</option>
                    @foreach(App\Club::all()->sortBy('name') as $club)
                        <option value="{{ $club->id }}">{{ $club->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col s6 m4 l3">
                <label>{{ trans('general.away_team') }}</label>
                <select id="away_team_id" name="away_team_id" class="browser-default" disabled required>
                    <option value="0" disabled selected>{{ trans('general.choose_first', ['name' => trans('general.away_club')]) }}</option>
                </select>
            </div>

        </div>

        <div class="row">

            <div class="col s6 m4 l3">
                <label>{{ trans('models.competition') }}</label>
                <select onchange="updateSeasonList('competition_id', 'season_id')" id="competition_id" name="competition_id" class="browser-default" required>
                    <option disabled value="0" selected>{{ trans('general.choose_option') }}</option>
                    @foreach(App\Competition::all() as $competition)
                        <option value="{{ $competition->id }}">{{ $competition->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col s6 m4 l3">
                <label>{{ trans('models.season') }}</label>
                <select onchange="updateGameGroupsList('season_id', 'game_group_id')" id="season_id" name="season_id" class="browser-default" disabled required>
                    <option value="0" disabled selected>{{ trans('general.choose_first', ['name' => trans('models.competition')]) }}</option>
                </select>
            </div>

        </div>

        <div class="row">

            <div class="col s6 m4 l3">
                <label>{{ trans('models.game_group') }}</label>
                <select id="game_group_id" name="game_group_id" class="browser-default" disabled required>
                    <option value="0" disabled selected>{{ trans('general.choose_first', ['name' => trans('models.season')]) }}</option>
                </select>
            </div>

            <div class="input-field col s6 m4 l3">
                <input type="number" name="round" id="round" required value="{{ old('round') }}">
                <label for="round">{{ trans('general.round') }}</label>
            </div>
        </div>

        <div class="row">

            <div class="input-field col s4 m3 l2">
                <input id="date" name="date" type="text" class="datepicker" required>
                <label for="date">{{ trans('general.day') }}</label>
            </div>

            <div class="input-field col s4 m2 l2">
                <input id="hour" name="hour" type="text" class="timepicker" required>
                <label for="hour">{{ trans('general.hour') }}</label>
            </div>

            <div class="col s4 m3 l2">
                @include('backoffice.partial.select_timezone', ['timezone_name' => $user->profile->timezone, 'timezone_value' => $user->profile->timezone])
            </div>

        </div>

        <div class="row">
            <div class="input-field col s6 m4 l3">
                <input type="number" name="goals_home" id="goals_home">
                <label for="goals_home">{{ trans('general.goals_home') }}</label>
            </div>

            <div class="input-field col s6 m4 l3">
                <input type="number" name="goals_away" id="goals_away">
                <label for="goals_away">{{ trans('general.goals_away') }}</label>
            </div>
        </div>

        <div class="row">
            <div class="input-field col s6 m4 l3">
                <input type="number" name="penalties_home" id="penalties_home">
                <label for="penalties_home">{{ trans('general.penalties_home') }}</label>
            </div>

            <div class="input-field col s6 m4 l3">
                <input type="number" name="penalties_away" id="penalties_away">
                <label for="penalties_away">{{ trans('general.penalties_away') }}</label>
            </div>
        </div>

        <div class="row">

            <div class="col s12 m8 l6">
                <label>{{ trans('models.playground') }}</label>
                <select id="playground_id" name="playground_id" class="browser-default">
                    <option value="" selected>{{ trans('general.none') }}</option>
                    @foreach(App\Playground::all()->sortBy('name') as $playground)
                        <option value="{{ $playground->id }}">{{ $playground->name }}</option>
                    @endforeach
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
                        <input name="finished" type="checkbox" value="true">
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

        <div class="row">
            <div class="input-field col s12">
                @include('backoffice.partial.button', ['color' => 'green', 'icon' => 'send', 'text' => trans('general.create')])
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