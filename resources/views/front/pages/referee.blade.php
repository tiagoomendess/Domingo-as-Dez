@extends('front.layouts.default-page')

@section('head-content')
    <link rel="stylesheet" href="/css/front/referee-style.css">
    <title>{{ $referee->name }}</title>
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col s12 hide-on-med-and-down">
                <h1>{{ $referee->name }}</h1>
            </div>
            <section class="col s12 xl4">
                <h2 class="over-card-title">
                    {{ trans('front.photograph') }}
                </h2>

                <figure>
                    <img src="{{ $referee->getPicture() }}" alt="{{ $referee->name }}">
                </figure>
            </section>

            <section class="col s12 xl8">
                <h2 class="over-card-title">
                    {{ trans('models.games') }}
                </h2>
                <div class="card">
                    <div class="card-content">
                        <ul class="list-a">
                            @foreach($game_referees as $game_referee)
                                @if ($game_referee->game->finished)
                                    <li>
                                        <a href="{{ $game_referee->game->getPublicURL() }}">
                                            <div class="game-referee">
                                                <div class="game-info">

                                                    <div class="home-club">
                                                        <div class="right">
                                                            <span class="hide-on-med-and-down">{{ $game_referee->game->home_team->club->name }}</span>
                                                            <img src="{{ $game_referee->game->home_team->club->getEmblem() }}" alt="{{ $game_referee->game->home_team->club->name }}">
                                                        </div>
                                                    </div>

                                                    <div class="separator">
                                                        <div class="score">
                                                            {{ $game_referee->game->getHomeScore() }} - {{ $game_referee->game->getAwayScore() }}
                                                        </div>
                                                    </div>

                                                    <div class="away-club">
                                                        <img src="{{ $game_referee->game->away_team->club->getEmblem() }}" alt="{{ $game_referee->game->away_team->club->name }}">
                                                        <span class="hide-on-med-and-down">{{ $game_referee->game->away_team->club->name }}</span>
                                                    </div>
                                                </div>

                                                <div class="competition-info">
                                                    <span class="">{{ trans('general.' . $game_referee->referee_type->name ) }}</span>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                </div>

                {{ $game_referees->links() }}
            </section>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="/js/front/referee-scripts.js"></script>
@endsection