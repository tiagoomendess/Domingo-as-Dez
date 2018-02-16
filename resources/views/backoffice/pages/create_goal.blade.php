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
                    <select class="browser-default">
                        <option
                                onclick="updateSeasonList({{ $game->season->competition->id }})"
                                value="{{ $game->season->competition->id }}" selected>
                            {{ $game->season->competition->name }}
                        </option>

                        @foreach(\App\Competition::all() as $competition)
                            @if($competition->id != $game->season->competition->id)
                                <option
                                        value="{{ $competition->id }}"
                                        onclick="updateSeasonList({{ $competition->id }})">
                                    {{ $competition->name }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <div class="col s6 m4 l3">
                    <label>{{ trans('models.season') }}</label>
                    <select name ="season_id" id="season_id" class="browser-default">
                        <option
                                onclick="updateGamesList({{ $game->season->id }})"
                                value="{{ $game->season->id }}" selected>
                            {{ $game->season->start_year }}/{{ $game->season->end_year }}
                        </option>

                        @foreach($game->season->competition->seasons as $season)
                            @if($season->id != $game->season->id)
                                <option
                                        value="{{ $season->id }}"
                                        onclick="updateGamesList({{ $season->id }})">
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
                    <select name ="game_id" id="game_id" class="browser-default">
                        <option
                                value="{{ $game->id }}"
                                onclick="updateSelectTeam(
                                        '{{ $game->homeTeam->id }}',
                                        '{{ $game->homeTeam->club->name }}',
                                        '{{ $game->awayTeam->id }}',
                                        '{{ $game->awayTeam->club->name }}')"
                                selected>
                            {{ $game->homeTeam->club->name }} vs {{ $game->awayTeam->club->name }}
                        </option>

                        @foreach($game->season->games as $other_game)
                            @if($other_game->id != $game->id)
                                <option
                                        value="{{ $other_game->id }}"
                                        onclick="updateSelectTeam(
                                            '{{ $other_game->homeTeam->id }}',
                                            '{{ $other_game->homeTeam->club->name }}',
                                            '{{ $other_game->awayTeam->id }}',
                                            '{{ $other_game->awayTeam->club->name }}')">
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
                    <select name ="selected_team_id" id="selected_team_id" class="browser-default">
                        <option disabled
                                value="" selected>
                            {{ trans('general.choose_option')}}
                        </option>

                        <option
                                value="{{ $game->homeTeam->id }}"
                                onclick="updatePlayers('{{ $game->homeTeam->id }}', '{{ $game->awayTeam->id }}')">
                            {{ $game->homeTeam->club->name }}
                        </option>

                        <option
                                value="{{ $game->awayTeam->id }}"
                                onclick="updatePlayers('{{ $game->awayTeam->id }}', '{{ $game->homeTeam->id }}')">
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
                    <select class="browser-default">
                        <option value="" disabled selected>{{ trans('general.choose_option') }}</option>

                        @foreach(\App\Competition::all() as $competition)
                                <option
                                        value="{{ $competition->id }}"
                                        onclick="updateSeasonList({{ $competition->id }})">
                                    {{ $competition->name }}
                                </option>
                        @endforeach
                    </select>
                </div>

                <div class="col s6 m4 l3">
                    <label>{{ trans('models.season') }}</label>
                    <select name ="season_id" id="season_id" class="browser-default" disabled>
                        <option disabled
                                value="" selected>
                            {{ trans('general.choose_first', ['name' => trans('models.competition')])}}
                        </option>
                    </select>
                </div>

            </div>

            <div class="row">

                <div class="col s12 m8 l6">
                    <label>{{ trans('models.game') }}</label>
                    <select name ="game_id" id="game_id" class="browser-default" disabled>
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
                    <select name ="selected_team_id" id="selected_team_id" class="browser-default" disabled>
                        <option disabled
                                value="" selected>
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

    <script>

        //Substitui o include do update_seasons_list
        function updateSeasonList(id, element_id) {

            updateSelectTeam(null);

            if (element_id == null)
                element_id = "#season_id";
            else
                element_id = "#" + element_id;

            var season_dropdown = $(element_id);
            season_dropdown.prop('disabled', true);
            season_dropdown.empty();

            updateGamesList(0);

            //Disable 2nd dropdown
            if (id == 0) {

                var op = $("<option> Primeiro escolhe Clube</option>");
                op.attr('value', '0');
                op.appendTo(season_dropdown);
                season_dropdown.prop('disabled', true);
                return;
            }

            $.get("/competitions/" + id + "/seasons", function (data) {


                if(data.length == 0) {
                    var op = $("<option>Nenhuma</option>");
                    op.attr('value', 0);
                    op.appendTo(season_dropdown);
                } else {
                    var op = $("<option>" + 'Escolha uma opção' + "</option>");
                    op.prop('disabled', true);
                    op.prop('selected', true);
                    op.appendTo(season_dropdown);
                }

                for (i = 0; i < data.length; i++) {

                    if (data[i].start_year != data[i].end_year)
                        var op = $("<option>" + data[i].start_year + "/" + data[i].end_year + "</option>");
                    else
                        var op = $("<option>" + data[i].start_year + "</option>");


                    op.attr('value', data[i].id);
                    op.attr('onclick', "updateGamesList(" + data[i].id + ")");
                    op.appendTo(season_dropdown);

                }

                season_dropdown.prop('disabled', false);
            });
        }

        function updateGamesList(id, element_id) {

            updateSelectTeam(null);

            if (element_id == null)
                element_id = "#game_id";
            else
                element_id = "#" + element_id;

            var game_dropdown = $(element_id);
            game_dropdown.prop('disabled', true);
            game_dropdown.empty();

            //Disable 2nd dropdown
            if (id == 0) {

                var op = $("<option> Primeiro escolhe Epoca</option>");
                op.attr('value', '0');
                op.appendTo(game_dropdown);
                game_dropdown.prop('disabled', true);
                return;
            }

            $.get("/seasons/" + id + "/games", function (data) {


                if(data.length == 0) {
                    var op = $("<option>Nenhum</option>");
                    op.attr('value', 0);
                    op.attr('selected', true);
                    op.appendTo(game_dropdown);

                    return;
                }

                var op = $("<option> Escolha um jogo</option>");
                op.prop('disabled', true);
                op.prop('selected', true);
                op.appendTo(game_dropdown);

                for (i = 0; i < data.length; i++) {

                    var op = $("<option>" + data[i].home_team.club.name + " vs " + data[i].away_team.club.name + "</option>");

                    op.attr('value', data[i].id);
                    op.attr('onclick', 'updateSelectTeam(' +
                        data[i].home_team.id + ',"' +
                        data[i].home_team.club.name + '",' +
                        data[i].away_team.id + ',"' +
                        data[i].away_team.club.name + '")'
                    );

                    op.appendTo(game_dropdown);
                }

                game_dropdown.prop('disabled', false);

            });
        }

        function updateSelectTeam(home_team_id, home_club_name, away_team_id, away_club_name) {

            updatePlayers(null);

            var select_team = $('#selected_team_id');
            select_team.prop('disabled', true);
            select_team.empty();

            if(home_team_id == null || home_club_name == null || away_team_id == null || away_club_name == null) {

                var op1 = $("<option> Primeiro escolha Jogo</option>");
                op1.appendTo(select_team);
                return;

            }

            var op = $("<option> Escolha uma equipa</option>");
            op.prop('disabled', true);
            op.prop('selected', true);
            op.appendTo(select_team);

            var op1 = $("<option>" + home_club_name + "</option>");
            op1.attr('value', home_team_id);
            op1.attr('onclick', 'updatePlayers(' + home_team_id + ',' + away_team_id + ')');

            var op2 = $("<option>" + away_club_name + "</option>");
            op2.attr('value', away_team_id);
            op2.attr('onclick', 'updatePlayers(' + away_team_id + ',' + home_team_id + ')');

            op1.appendTo(select_team);
            op2.appendTo(select_team);

            select_team.prop('disabled', false);

        }

        function updatePlayers(id1, id2) {

            var select_player = $('#player_id');

            select_player.prop('disabled', true);
            select_player.empty();

            if (id1 == 0 || id1 == null || id2 == 0 || id2 == null) {
                var op = $("<option> Primeiro escolha Equipa </option>");
                op.prop('selected', true);
                op.appendTo(select_player);
                return;
            }

            $.get("/teams/" + id1 + "/current_players", function (data) {

                console.log(data);
                if (data.length == 0) {
                    var op = $("<option> Nenhum Jogador Encontrado </option>");
                    op.prop('selected', true);
                    return;
                }

                for (i = 0; i < data.length; i++) {
                    if (data[i].nickname != null)
                        var op = $("<option>" + data[i].name + " (" + data[i].nickname + ")" + "</option>");
                    else
                        var op = $("<option>" + data[i].name + "</option>");

                    op.attr('value', data[i].id);
                    op.appendTo(select_player);

                }
            });

            $.get("/teams/" + id2 + "/current_players", function (data) {

                console.log(data);
                if (data.length == 0) {
                    var op = $("<option> Nenhum Jogador Encontrado </option>");
                    op.prop('selected', true);
                    return;
                }

                for (i = 0; i < data.length; i++) {
                    if (data[i].nickname != null)
                        var op = $("<option>" + data[i].name + " (" + data[i].nickname + ")" + "</option>");
                    else
                        var op = $("<option>" + data[i].name + "</option>");

                    op.attr('value', data[i].id);
                    op.appendTo(select_player);

                }
            });


            select_player.prop('disabled', false);
        }

    </script>
@endsection