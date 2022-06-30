@extends('front.layouts.default-page')

@section('head-content')
    <title>Jogos de Hoje</title>

    <meta property="og:title" content="{{ 'Jogos Hoje - ' . config('app.name') }}"/>
    <meta property="og:type" content="website"/>
    <meta property="og:image" content="{{ url('/images/todays_games.jpg') }}">
    <meta property="og:image:width" content="1920">
    <meta property="og:image:height" content="1080">
    <meta property="og:description" content="Lista de todos os jogos marcados para o dia de hoje" />

    <meta itemprop="name" content="Jogos de Hoje">
    <meta itemprop="description" content="Lista de todos os jogos marcados para o dia de hoje">
    <meta itemprop="image" content="{{ url('/images/todays_games.jpg') }}">

@endsection

@section('content')
    <div class="container">
        <h1 class="hide-on-med-and-down">Jogos de Hoje</h1>

        <div class="hide-on-med-and-up" style="margin-top: 5px">
            <!-- Today Games Horizontal -->
            <ins class="adsbygoogle"
                 style="display:block"
                 data-ad-client="ca-pub-3518000096682897"
                 data-ad-slot="2210321320"
                 data-ad-format="horizontal"
                 data-full-width-responsive="true"></ins>
            <script>
                (adsbygoogle = window.adsbygoogle || []).push({});
            </script>
        </div>

        <div class="row">
            <div class="col s12 m12 l8">
                <div class="card">
                    <div class="card-content group-games">
                        @if (count($games) < 1)
                            <p class="flow-text text-center">
                                Não existem jogos marcados para hoje.
                                @if($closest)
                                    O <a href="{{ $closest->getPublicUrl() }}">jogo mais próximo </a>está marcado para dia
                                    {{ (new \Carbon\Carbon($closest->date))->setTimezone('Europe/Lisbon')->format('d/m \d\e Y') }}
                                @endif
                            </p>
                        @else
                            <ul class="list-a">
                                @foreach($games as $game)
                                    <li>
                                        <a href="{{ $game->getPublicUrl() }}">

                                            <div class="row" style="margin-bottom: 0; width: 100%;">
                                                <div class="col s4" style="text-align: right; vertical-align: middle; vert-align: middle">
                                                    <div style="display: flex; flex-direction: row; justify-content: end; align-items: center; height: 37px">
                                                        <span style="" class="hide-on-med-and-down">{{ $game->home_team->club->name }}</span>
                                                        <span class="hide-on-large-only">
                                                            {{ mb_strtoupper(str_limit($game->home_team->club->name, 3, '')) }}
                                                        </span>
                                                        <img class="" style="width: 30px; margin-left: 5px; resize: none;"
                                                             src="{{ $game->home_team->club->getEmblem() }}">
                                                    </div>
                                                </div>

                                                <div class="col s2"
                                                     style="text-align: center; margin-top: 6px; padding: 0">
                                                        <span style="background-color: #989898; padding: 0.2rem 0.5rem; color: white; font-weight: bold">
                                                            @if ($game->finished)
                                                                {{ $game->getHomeScore() }}
                                                                - {{ $game->getAwayScore() }}
                                                            @else
                                                                @if($game->postponed)
                                                                    ADI
                                                                @else
                                                                    {{ (new \Carbon\Carbon($game->date))->setTimezone('Europe/Lisbon')->format('H:i') }}
                                                                @endif
                                                            @endif
                                                        </span>
                                                </div>

                                                <div class="col s4">
                                                    <div style="display: flex; flex-direction: row; justify-content: start; align-items: center; height: 37px">
                                                        <img style="width: 30px; resize: none; margin-right: 5px"
                                                             src="{{ $game->away_team->club->getEmblem() }}">
                                                        <span style="" class="hide-on-med-and-down">{{ $game->away_team->club->name }}</span>
                                                        <span class="hide-on-large-only">
                                                            {{ mb_strtoupper(str_limit($game->away_team->club->name, 3, '')) }}
                                                        </span>
                                                    </div>
                                                </div>

                                                <div class="col s2" style="text-align: right">
                                                    <img style="width: 25px; right: 0; margin-top: 5px"
                                                         src="{{ $game->game_group->season->competition->picture }}"
                                                         alt="{{ $game->game_group->season->competition->name }}">
                                                </div>
                                            </div>

                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>

            @if(has_permission('games.edit'))
                <div class="row">
                    <div class="col s12">
                        <a href="{{ route('games.today_edit') }}"
                           class="btn-floating btn-large waves-effect waves-light blue right"><i class="material-icons">edit</i></a>
                    </div>
                </div>
            @endif

            <div class="col m12 l4">

                <!-- Today Games Page -->
                <ins class="adsbygoogle"
                     style="display:block"
                     data-ad-client="ca-pub-3518000096682897"
                     data-ad-slot="8596939730"
                     data-ad-format="vertical"
                     data-full-width-responsive="true"></ins>
                <script>
                    (adsbygoogle = window.adsbygoogle || []).push({});
                </script>

            </div>

        </div>

    </div>

@endsection

@section('scripts')
@endsection