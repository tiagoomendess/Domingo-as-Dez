@extends('front.layouts.default-page')

@section('head-content')
    <title>Todos os Jogos</title>
    <style>
        .game-score {
            padding: 3px 7px;
            background-color: grey;
            color: white;
            font-weight: 600;
        }

        .emblem {
            width: 30px;
            height: 30px;
            margin: 3px;
        }

        .game-home-side {
            text-align: right;
            display: flex;
            justify-content: right;
            align-items: center;
        }

        .game-away-side {
            text-align: left;
            display: flex;
            justify-content: left;
            align-items: center;
        }

        @media only screen and (max-width: 600px) {
            .competition-logo-for-game {
                width: 20px;
                margin-top: 7px;
            }
        }

        @media only screen and (min-width: 601px) {
            .competition-logo-for-game {
                width: 30px;
            }
        }

    </style>
@endsection

@section('content')
    <div class="container">
        <div class="row no-margin-bottom hide-on-med-and-down">
            <div class="col s12">
                <h1>Todos os Jogos</h1>
            </div>
        </div>

        @if(!has_permission('disable_ads'))
            <div class="row no-margin-bottom">
                <div class="vertical-spacer hide-on-med-and-down"></div>
                <div class="col s12 m10 l8 offset-m1 offset-l2">
                    <script async
                            src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-3518000096682897"
                            crossorigin="anonymous"></script>
                    <!-- All Games Top Page -->
                    <ins class="adsbygoogle"
                         style="display:block"
                         data-ad-client="ca-pub-3518000096682897"
                         data-ad-slot="7326175269"
                         data-ad-format="auto"
                         data-full-width-responsive="true"></ins>
                    <script>
                        (adsbygoogle = window.adsbygoogle || []).push({});
                    </script>
                </div>
            </div>
        @endif

        <div class="row">
            <div class="col s12 m12 l8">
                <div class="card">
                    <div class="card-content">
                        <ul class="list-a">
                            @foreach($games as $game)
                                <li>
                                    <a href="{{ $game->public_url }}" style="padding: 7px 0">
                                        <div class="row no-margin-bottom" style="width: 100%">
                                            <div class="col s4 m4 l4 game-home-side">
                                                <span class="hide-on-med-and-down">{{ $game->home_team->club->name }}</span>
                                                <span class="hide-on-large-only">{{ $game->home_team_three_letters }}</span>
                                                <img class="emblem" src="{{ $game->home_team_emblem }}" alt="{{ $game->home_team->club->name }}">
                                            </div>

                                            <div class="col s3 m2 l2 center" style="padding: 5px 0 0 0">
                                                <span class="game-score">
                                                @if($game->postponed)
                                                    ADI
                                                @elseif ($game->started)
                                                    {{ $game->home_score }} - {{ $game->away_score }}
                                                @else
                                                    {{ \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $game->date)->timezone('Europe/Lisbon')->format('d\/m') }}
                                                @endif
                                                </span>
                                            </div>

                                            <div class="col s4 m4 l4 game-away-side">
                                                <img class="emblem" src="{{ $game->away_team_emblem }}" alt="{{ $game->away_team->club->name }}">
                                                <span class="hide-on-med-and-down">{{ $game->away_team->club->name }}</span>
                                                <span class="hide-on-large-only">{{ $game->away_team_three_letters }}</span>
                                            </div>

                                            <div class="col s1 m2 l2 right">
                                                <img class="competition-logo-for-game right" src="{{ $game->game_group->season->competition->picture }}" alt="{{ $game->game_group->season->competition->name }}">
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col s12 m12 l4">
                <div class="card">
                    <div class="card-content">
                        <p class="flow-text">Legenda</p>
                        <div class="divider"></div>
                        <ul>
                            <li style="margin-top: 5px"><span class="game-score">ADI</span> - Jogo Adiado</li>
                            @foreach($competitions as $competition)
                                <li style="display: flex; align-items: center; margin-top: 5px">
                                    <img class="emblem" src="{{ $competition->picture }}" alt="{{ $competition->name }}">
                                    {{ $competition->name }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            {{ $games->onEachSide(1)->links() }}
        </div>
    </div>

@endsection
