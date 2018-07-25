@extends('front.layouts.default-page')

@section('head-content')
    <title>{{ $club->name }}</title>
    <link rel="stylesheet" href="/css/front/club-page-style.css">
@endsection

@section('content')
    <div class="parallax-container">
        <div class="parallax">
            @if (!is_null($playground))
                <img src="{{ $playground->getPicture() }}">
            @else
                <img src="{{ \App\Media::getPlaceholder('16:9', $club->id) }}">
            @endif
        </div>

        <div class="club-parallax-container">
            <div class="page-title">
                <img src="{{ $club->getEmblem() }}" alt="{{ $club->name }}">
                <h1>{{ $club->name }}</h1>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col s12 m12 l8 xl8">
                <h2>{{ trans('models.teams') }}</h2>
                <div class="card teams">

                    @if (count($teams) > 0)
                        <div class="card-tabs">
                            <ul class="tabs tabs-fixed-width">
                                @foreach($teams as $team)
                                    <li class="tab"><a href="#team{{ $team->id }}" class="">{{ $team->name }}</a></li>
                                @endforeach
                            </ul>
                        </div>

                        <div class="card-content grey lighten-4">
                            @foreach($teams as $team)
                                <div id="team{{ $team->id }}">
                                    <div class="row">
                                        @if(count($team->getCurrentPlayers()) < 1)
                                            <p class="center">{{ trans('front.no_players_in_team', ['team_name' => $team->name]) }}</p>
                                        @endif
                                        @foreach($team->getCurrentPlayers() as $player)
                                            <div class="col s6 m6 l4 xl3">
                                                <a href="{{ $player->getPublicURL() }}">
                                                    <div class="player">
                                                        <img src="{{ $player->getPicture() }}" alt="{{ $player->name }}" class="circle">
                                                        <span class="">{{ $player->displayName() }}</span>
                                                    </div>
                                                </a>

                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="card">
                            <div class="card-content">
                                <p class="flow-text center">{{ trans('models.no_teams') }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="col s12 m12 l4 xl4">
                <h2>{{ trans('models.transfers') }}</h2>
                <div class="card">
                    <div class="card-content transfers">
                        @if (count($transfers) > 0)

                            @foreach($transfers as $transfer)

                                <div class="transfer">
                                    <a href="{{ $transfer->player->getPublicURL() }}">
                                        <figure>
                                            <img class="circle" src="{{ $transfer->player->getPicture() }}" alt="{{ $transfer->player->displayName() }}">
                                        </figure>

                                        <div class="info">
                                            <span class="player-name">{{ $transfer->player->displayName() }}</span>

                                            @if ($transfer->team && $transfer->team->club->id == $club->id)
                                                <i class="material-icons green-text flow-text">arrow_back</i>
                                            @else
                                                <i class="material-icons red-text flow-text">arrow_forward</i>
                                            @endif

                                        </div>
                                    </a>
                                </div>

                            @endforeach
                        @else
                            <p class="center">{{ trans('models.no_transfers') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="/js/front/club-page-scripts.js"></script>
@endsection