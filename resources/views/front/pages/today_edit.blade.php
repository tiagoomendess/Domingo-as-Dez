@extends('front.layouts.default-page')

@section('head-content')
    <title>Editar Resultados de Hoje</title>
@endsection

@section('content')
    <div class="container">
        <h1 class="hide-on-med-and-down">Editar Resultados de Hoje</h1>

        <div class="row">
            <div class="col s12 m8 l6">
                <div class="card">
                    <div class="card-content group-games">
                        @if (count($games) < 1)
                            <p class="flow-text text-center">
                                Não existem jogos marcados para hoje.
                            </p>
                        @else
                            <ul class="list-a">
                                @foreach($games as $game)
                                    <li>
                                        <div class="row">
                                            <form method="POST" class="col s12" action="{{ route('games.today_update_score') }}" id="game_{{ $game->id }}">
                                                {{ csrf_field() }}
                                                <div class="row center">
                                                    <input type="hidden" name="game_id" value="{{ $game->id }}"/>
                                                    <div class="col s3">
                                                        <img style="width: 100%; margin-top: 5px" src="{{ $game->home_team->club->getEmblem() }}" alt="">
                                                    </div>
                                                    <div class="input-field col s3">
                                                        <input class="center" type="text" name="goals_home" value="{{ $game->getHomeScore() }}"/>
                                                    </div>
                                                    <div class="input-field col s3">
                                                        <input class="center" type="text" name="goals_away" value="{{ $game->getAwayScore() }}"/>
                                                    </div>
                                                    <div class="col s3">
                                                        <img style="width: 100%; margin-top: 5px" src="{{ $game->away_team->club->getEmblem() }}" alt="">
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col s2">
                                                        <a data-game-id="{{ $game->id }}" style="padding: 0; width: 100%" class="waves-effect waves-light btn red center home-down">
                                                            <i class="material-icons">arrow_downward</i>
                                                        </a>
                                                    </div>
                                                    <div class="col s2">
                                                        <a data-game-id="{{ $game->id }}" style="padding: 0; width: 100%" class="waves-effect waves-light btn green center home-up">
                                                            <i class="material-icons">arrow_upward</i>
                                                        </a>
                                                    </div>
                                                    <div class="col s2 text-center center">
                                                        <p style="margin-left: 13px">
                                                            @if($game->finished)
                                                                <input data-game-id="{{ $game->id }}" name="finished" type="checkbox" class="filled-in" id="finished_checkbox_{{ $game->id }}" value="1" checked="checked"/>
                                                            @else
                                                                <input data-game-id="{{ $game->id }}" name="finished" type="checkbox" class="filled-in" value="1" id="finished_checkbox_{{ $game->id }}"/>
                                                            @endif
                                                            <label for="finished_checkbox_{{ $game->id }}"></label>
                                                        </p>
                                                    </div>
                                                    <div class="col s2">
                                                        <a data-game-id="{{ $game->id }}" style="padding: 0; width: 100%" class="waves-effect waves-light btn red center away-down">
                                                            <i class="material-icons">arrow_downward</i>
                                                        </a>
                                                    </div>
                                                    <div class="col s2">
                                                        <a data-game-id="{{ $game->id }}" style="padding: 0; width: 100%" class="waves-effect waves-light btn green center away-up">
                                                            <i class="material-icons">arrow_upward</i>
                                                        </a>
                                                    </div>
                                                    <div class="col s2">
                                                        <button disabled type="submit" style="padding: 0; width: 100%" class="waves-effect waves-light btn blue center">
                                                            <i class="material-icons">send</i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        const disableOthers = (gameId) => {
            console.log("Disabling other but game id ", gameId)
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

        $(document).ready(function() {
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
            })

            $('.filled-in').on('click', event => {
                let element = $(event.target)
                let gameId = element.attr('data-game-id')
                $(`#game_${gameId}`).find('button').removeAttr('disabled')
            })
        })
    </script>
@endsection