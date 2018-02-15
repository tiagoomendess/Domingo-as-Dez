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
                <select id="club_id" name="club_id" class="browser-default" required>
                    <option disabled value="0" selected>{{ trans('general.choose_option') }}</option>
                    @foreach(App\Club::all() as $club)
                        <option onclick="updateTeamList( {{ $club->id }}, 'home_team_id')" value="{{ $club->id }}">{{ $club->name }}</option>
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
                <select id="club_id" name="club_id" class="browser-default" required>
                    <option disabled value="0" selected>{{ trans('general.choose_option') }}</option>
                    @foreach(App\Club::all() as $club)
                        <option onclick="updateTeamList({{ $club->id }} , 'away_team_id')" value="{{ $club->id }}">{{ $club->name }}</option>
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

            <div class="col s5 m3 l2">
                <label>{{ trans('models.competition') }}</label>
                <select id="competition_id" name="competition_id" class="browser-default" required>
                    <option disabled value="0" selected>{{ trans('general.choose_option') }}</option>
                    @foreach(App\Competition::all() as $competition)
                        <option onclick="updateSeasonList({{ $competition->id }})" value="{{ $competition->id }}">{{ $competition->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col s5 m3 l2">
                <label>{{ trans('models.season') }}</label>
                <select id="season_id" name="season_id" class="browser-default" disabled required>
                    <option value="0" disabled selected>{{ trans('general.choose_first', ['name' => trans('models.competition')]) }}</option>
                </select>
            </div>

            <div class="input-field col s2 m2 l2">
                <input type="number" name="round" id="round" required>
                <label for="round">{{ trans('general.round') }}</label>
            </div>

        </div>

        <div class="row">

            <div class="input-field col s6 m4 l3">
                <input id="date" name="date" type="text" class="datepicker" required>
                <label for="date">{{ trans('general.day') }}</label>
            </div>

            <div class="input-field col s6 m4 l3">
                <input id="hour" name="hour" type="text" class="timepicker" required>
                <label for="hour">{{ trans('general.hour') }}</label>
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
            <div class="col s6 m4 l3">
                <label>{{ trans('models.playground') }}</label>
                <select id="playground_id" name="playground_id" class="browser-default" required>
                    <option value="" selected>{{ trans('general.none') }}</option>
                    @foreach(App\Playground::all() as $playground)
                        <option value="{{ $playground->id }}">{{ $playground->name }}</option>
                    @endforeach
                </select>
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
    @include('backoffice.partial.update_team_list_js')
    @include('backoffice.partial.update_seasons_list_js')
    @include('backoffice.partial.pick_a_date_js')
    @include('backoffice.partial.pick_a_time_js')
@endsection