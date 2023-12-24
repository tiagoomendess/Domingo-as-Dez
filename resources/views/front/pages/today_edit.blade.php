@extends('front.layouts.default-page')

@section('head-content')
    <title>Editar Resultados de Hoje</title>
@endsection

@section('content')
    <div class="container">
        <h1 class="hide-on-med-and-down">Editar Resultados de Hoje</h1>

        <div class="row">
            <div class="col s12 m8 l6">


                @if (count($games) < 1)
                    <p class="flow-text text-center">
                        Não existem jogos marcados para hoje.
                    </p>
                @else
                    <ul class="list-a">
                        @foreach($games as $game)
                            <div class="card" style="margin: 6px 0">
                                <div class="card-content" style="padding: 10px 10px 0 10px">
                                    <div class="row no-margin-bottom">
                                        <form method="POST" class="col s12"
                                              action="{{ route('games.today_update_score') }}"
                                              id="game_{{ $game->id }}">
                                            {{ csrf_field() }}
                                            <div class="row center no-margin-bottom">
                                                <input type="hidden" name="game_id" value="{{ $game->id }}"/>
                                                <div class="col s3">
                                                    <img style="width: 100%; margin-top: 5px"
                                                         src="{{ $game->home_team->club->getEmblem() }}" alt="">
                                                </div>
                                                <div class="col s6">
                                                    <h2 id="h2_score_{{ $game->id }}"
                                                        style="font-weight: 300; margin: 0">
                                                        {{ $game->getHomeScore() }} - {{ $game->getAwayScore() }}
                                                    </h2>

                                                    @if(!empty($score_reports[$game->id]))
                                                        <small><a href="{{ route('front.games.show_score_reports', ['game' => $game]) }}">detalhes</a></small>
                                                    @endif

                                                    <input class="center" type="hidden" name="goals_home"
                                                           value="{{ $game->getHomeScore() }}"/>

                                                    <input class="center" type="hidden" name="goals_away"
                                                           value="{{ $game->getAwayScore() }}"/>

                                                </div>
                                                <div class="col s3">
                                                    <img style="width: 100%; margin-top: 5px"
                                                         src="{{ $game->away_team->club->getEmblem() }}" alt="">
                                                </div>
                                            </div>

                                            <div class="row no-margin-bottom">
                                                <div class="col s12 center">
                                                    @if(!empty($score_reports[$game->id]))
                                                        @foreach($score_reports[$game->id] as $key => $value)
                                                            <small style="color: #5d5d5d">&nbsp;{{ $key }}({{ $value }})&nbsp;</small>
                                                        @endforeach
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="row no-margin-bottom">
                                                <div id="pre_loader_placeholder_{{ $game->id }}" style="height: 4px; width: 100%"></div>
                                                <div id="pre_loader_{{ $game->id }}"
                                                     class="progress blue lighten-4 hide" style="margin: 0">
                                                    <div class="indeterminate blue"></div>
                                                </div>
                                                <div
                                                    style="display: flex;
                                                    justify-content: space-between;
                                                    align-items: center;
                                                    padding: 10px;
                                                    background-color: #eaeaea;">
                                                    <a data-game-id="{{ $game->id }}" style="padding: 0; width: 36px"
                                                       class="waves-effect waves-light btn red center home-down">
                                                        <i class="material-icons">arrow_downward</i>
                                                    </a>

                                                    <a data-game-id="{{ $game->id }}" style="padding: 0; width: 36px"
                                                       class="waves-effect waves-light btn green center home-up">
                                                        <i class="material-icons">arrow_upward</i>
                                                    </a>

                                                    <div style="width: 36px; height: 36px;">
                                                        <div style="margin: 7px 10px">
                                                            @if($game->finished)
                                                                <input data-game-id="{{ $game->id }}" name="finished"
                                                                       type="checkbox" class="filled-in checkbox-blue"
                                                                       id="finished_checkbox_{{ $game->id }}" value="1"
                                                                       checked="checked"/>
                                                            @else
                                                                <input data-game-id="{{ $game->id }}" name="finished"
                                                                       type="checkbox" class="filled-in checkbox-blue"
                                                                       value="1"
                                                                       id="finished_checkbox_{{ $game->id }}"/>
                                                            @endif
                                                            <label for="finished_checkbox_{{ $game->id }}"
                                                                   style="padding: 0">
                                                            </label>
                                                        </div>
                                                    </div>

                                                    <a data-game-id="{{ $game->id }}" style="padding: 0; width: 36px"
                                                       class="waves-effect waves-light btn red center away-down">
                                                        <i class="material-icons">arrow_downward</i>
                                                    </a>

                                                    <a data-game-id="{{ $game->id }}" style="padding: 0; width: 36px"
                                                       class="waves-effect waves-light btn green center away-up">
                                                        <i class="material-icons">arrow_upward</i>
                                                    </a>

                                                    <button id="submit_button_{{ $game->id }}"
                                                            disabled type="submit"
                                                            style="padding: 0; width: 36px"
                                                            class="waves-effect waves-light btn blue center"
                                                            onClick="showLoadingAnimation({{ $game->id }})">
                                                        <i class="material-icons">send</i>
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        const disableOthers = (gameId) => {
            $('form').each((index, form) => {
                if ($(form).attr('id') !== `game_${gameId}`) {
                    $(form).attr('style', `opacity: 33%`)
                    $(form).find('a').each((i, a) => {
                        $(a).attr('disabled', 'disabled')
                    })
                    $(form).find('.filled-in').attr('disabled', 'disabled')
                }
            })
        }

        const updateDisplayScore = (gameId) => {
            const form = $(`#game_${gameId}`)
            const homeScoreElement = $(form).find('input[name="goals_home"]')
            const homeScore = parseInt($(homeScoreElement).attr('value'))
            const awayScoreElement = $(form).find('input[name="goals_away"]')
            const awayScore = parseInt($(awayScoreElement).attr('value'))
            $(`#h2_score_${gameId}`).html(`${homeScore} - ${awayScore}`)
        }

        const showLoadingAnimation = (game_id) => {
            $(`#pre_loader_placeholder_${game_id}`).addClass('hide')
            $(`#pre_loader_${game_id}`).removeClass('hide')
            $(`#submit_button_${game_id}`).attr('disabled', 'disabled')
        }

        $(document).ready(function () {
            $('.home-down').on('click', (e) => {
                let element = $(e.target)
                if (!element.attr('data-game-id'))
                    element = element.parent()

                let gameId = element.attr('data-game-id')
                let form = $(`#game_${gameId}`)
                let homeScoreElement = $(form).find('input[name="goals_home"]')
                let score = parseInt($(homeScoreElement).attr('value'))
                score--

                if (score >= 0) {
                    $(form).find('button').removeAttr('disabled')
                    disableOthers(gameId)
                }

                $(homeScoreElement).attr('value', score >= 0 ? score : 0)
                updateDisplayScore(gameId)
            })

            $('.home-up').on('click', (e) => {
                let element = $(e.target)
                if (!element.attr('data-game-id'))
                    element = element.parent()

                let gameId = element.attr('data-game-id')
                let form = $(`#game_${gameId}`)
                let homeScoreElement = $(form).find('input[name="goals_home"]')
                let score = parseInt($(homeScoreElement).attr('value'))
                score++
                $(homeScoreElement).attr('value', score >= 0 ? score : 0)
                $(form).find('button').removeAttr('disabled')
                disableOthers(gameId)
                updateDisplayScore(gameId)
            })

            $('.away-down').on('click', (e) => {
                let element = $(e.target)
                if (!element.attr('data-game-id'))
                    element = element.parent()

                let gameId = element.attr('data-game-id')
                let form = $(`#game_${gameId}`)
                let awayScoreElement = $(form).find('input[name="goals_away"]')
                let score = parseInt($(awayScoreElement).attr('value'))
                score--

                if (score >= 0) {
                    $(form).find('button').removeAttr('disabled')
                    disableOthers(gameId)
                }

                $(awayScoreElement).attr('value', score >= 0 ? score : 0)
                updateDisplayScore(gameId)
            })

            $('.away-up').on('click', (e) => {
                let element = $(e.target)
                if (!element.attr('data-game-id'))
                    element = element.parent()

                let gameId = element.attr('data-game-id')
                let form = $(`#game_${gameId}`)
                let awayScoreElement = $(form).find('input[name="goals_away"]')
                let score = parseInt($(awayScoreElement).attr('value'))
                score++
                $(awayScoreElement).attr('value', score >= 0 ? score : 0)
                $(form).find('button').removeAttr('disabled')
                disableOthers(gameId)
                updateDisplayScore(gameId)
            })

            $('.filled-in').on('click', event => {
                let element = $(event.target)
                let gameId = element.attr('data-game-id')
                $(`#game_${gameId}`).find('button').removeAttr('disabled')
                disableOthers(gameId)
            })
        })
    </script>
@endsection