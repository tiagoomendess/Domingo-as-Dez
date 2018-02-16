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
                <select id="club_id" name="club_id" class="browser-default" required>
                    <option onclick="updateTeamList( {{ $game->homeTeam->club->id }}, 'home_team_id')" value="{{ $game->homeTeam->club->id }}" selected>{{ $game->homeTeam->club->name  }}</option>
                    @foreach(App\Club::all() as $club)

                        @if($club->id != $game->homeTeam->club->id)
                            <option onclick="updateTeamList( {{ $club->id }}, 'home_team_id')" value="{{ $club->id }}">{{ $club->name }}</option>
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
                <select id="club_id" name="club_id" class="browser-default" required>
                    <option onclick="updateTeamList( {{ $game->awayTeam->club->id }}, 'away_team_id')" value="{{ $game->awayTeam->club->id }}" selected>{{ $game->awayTeam->club->name }}</option>
                    @foreach(App\Club::all() as $club)
                        @if($club->id != $game->awayTeam->club->id)
                            <option onclick="updateTeamList( {{ $club->id }}, 'away_team_id')" value="{{ $club->id }}">{{ $club->name }}</option>
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

            <div class="col s5 m3 l2">
                <label>{{ trans('models.competition') }}</label>
                <select id="competition_id" name="competition_id" class="browser-default" required>
                    <option onclick="updateSeasonList({{ $game->season->competition->id }})" value="{{ $game->season->competition->id }}" selected>{{ $game->season->competition->name }}</option>
                    @foreach(App\Competition::all() as $competition)
                        @if ($competition->id != $game->season->competition->id)
                            <option onclick="updateSeasonList({{ $competition->id }})" value="{{ $competition->id }}">{{ $competition->name }}</option>
                        @endif

                    @endforeach
                </select>
            </div>

            <div class="col s5 m3 l2">
                <label>{{ trans('models.season') }}</label>
                <select id="season_id" name="season_id" class="browser-default" required>
                    @if($game->season->start_year != $game->season->end_year)
                        <option value="{{ $game->season_id }}" selected>{{ $game->season->start_year }}/{{ $game->season->end_year }}</option>
                    @else
                        <option value="{{ $game->season_id }}" selected>{{ $game->season->start_year }}</option>
                    @endif

                    @foreach($game->season->competition->seasons as $season)

                        @if($season->id != $game->season->id)

                            @if($season->start_year != $season->end_year)
                                <option value="{{ $season->id }}">{{ $season->start_year }}/{{ $season->end_year }}</option>
                            @else
                                <option value="{{ $season->id }}">{{ $season->start_year }}</option>
                            @endif

                        @endif

                    @endforeach
                </select>
            </div>

            <div class="input-field col s2 m2 l2">
                <input type="number" name="round" id="round" required value="{{ old('round', $game->round) }}">
                <label for="round">{{ trans('general.round') }}</label>
            </div>

        </div>

        <div class="row">

            <?php
                $carbon = \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $game->date);
            ?>
            <div class="input-field col s6 m4 l3">
                <input id="date" name="date" type="text" class="datepicker" required value="{{$carbon->format("Y-m-d")}}">
                <label for="date">{{ trans('general.day') }}</label>
            </div>

            <div class="input-field col s6 m4 l3">
                <input id="hour" name="hour" type="text" class="timepicker" required value="{{ $carbon->format("H:i") }}">
                <label for="hour">{{ trans('general.hour') }}</label>
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
            <div class="col s6 m4 l3">
                <label>{{ trans('models.playground') }}</label>
                <select id="playground_id" name="playground_id" class="browser-default" required>
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
    @include('backoffice.partial.update_team_list_js')
    @include('backoffice.partial.update_seasons_list_js')
    @include('backoffice.partial.pick_a_date_js')
    @include('backoffice.partial.pick_a_time_js')
@endsection