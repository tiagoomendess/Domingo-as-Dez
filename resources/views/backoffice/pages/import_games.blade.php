@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>{{ trans('general.import_games') }}</title>
@endsection

@section('content')

    <div class="row">
        <div class="col s12">
            <h1>{{ trans('general.import_games') }}</h1>
        </div>
    </div>

    @if(count($errors) > 0)
        <div class="row">
            <div class="col s12">
                @include('backoffice.partial.form_errors')
            </div>
        </div>
    @endif

    <div class="row">
        <div class="col s12">
            <ul>
                <li>Suporta CSV</li>
                <li>Delimitador deve ser ponto e vírgula</li>
                <li>A ordem tem de ser: Ronda; Data e Hora; Clube Visitado; Golos Visitado; Clube Visitante; Golos Visitante; É Eliminatoria; Grupo</li>
                <li>Como por exemplo: 1;2018-01-21;Carapeços;1;Silva;1;false;null</li>
                <li>Máximo 1000 registos</li>
            </ul>
        </div>
    </div>

    <form action="{{ route("games.import_games") }}" method="POST" enctype="multipart/form-data">

        {{csrf_field()}}

        <div class="row">

            <div class="col s12 m4 l3">
                <label>{{ trans('models.competition') }}</label>
                <select onchange="updateSeasonList('competition_id', 'season_id')" id="competition_id" name="competition_id" class="browser-default" required>
                    <option disabled value="0" selected>{{ trans('general.choose_option') }}</option>
                    @foreach(App\Competition::all() as $competition)
                        <option value="{{ $competition->id }}">{{ $competition->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col s12 m4 l3">
                <label>{{ trans('models.season') }}</label>
                <select onchange="updateGameGroupsList('season_id', 'game_group_id')" id="season_id" name="season_id" class="browser-default" disabled required>
                    <option value="0" disabled selected>{{ trans('general.choose_first', ['name' => trans('models.competition')]) }}</option>
                </select>
            </div>

        </div>

        <div class="row">

            <div class="col s12 m4 l3">
                <label>{{ trans('models.game_group') }}</label>
                <select id="game_group_id" name="game_group_id" class="browser-default" disabled required>
                    <option value="0" disabled selected>{{ trans('general.choose_first', ['name' => trans('models.season')]) }}</option>
                </select>
            </div>

            <div class="col s12 m4 l3 input-field">
                <input type="text" name="team_name" required>
                <label for="team_name">{{trans('models.team_name')}}</label>
            </div>
        </div>

        <div class="row">
            <div class="col s12 m8 l6 file-field input-field">
                <div class="btn">
                    <span>{{ trans('general.file') }}</span>
                    <input type="file" name="import_file" id="import_file" required>
                </div>
                <div class="file-path-wrapper">
                    <input class="file-path validate" type="text">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col s12">
                @include('backoffice.partial.button', ['color' => 'green', 'text' => trans('general.import'), 'icon' => 'send'])
            </div>
        </div>

    </form>

@endsection

@section('scripts')
    @include('backoffice.partial.update_seasons_list_js')
    @include('backoffice.partial.update_game_groups_js')
@endsection