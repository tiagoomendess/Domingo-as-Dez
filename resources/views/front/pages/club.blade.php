@extends('front.layouts.default-page')

@section('head-content')
    <title>Clube {{ $club->name }}</title>
    <link rel="stylesheet" href="/css/front/club-page-style.css">

    <meta property="og:title" content="{{ $club->name . ' - ' . config('app.name') }}"/>
    <meta property="og:type" content="website"/>
    <meta property="og:description" content="{{ trans('front.footer_desc') }}"/>
    <meta property="og:image" content="{{ url($club->getEmblem()) }}">

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
        @if(!has_permission('disable_ads') && \Config::get('custom.adsense_enabled'))
            <div class="row no-margin-bottom">
                <div class="vertical-spacer hide-on-med-and-down"></div>
                <div class="col s12 m10 l8 offset-m1 offset-l2">
                    <script async
                            src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-3518000096682897"
                            crossorigin="anonymous"></script>
                    <!-- Club Bellow Picture -->
                    <ins class="adsbygoogle"
                         style="display:block; width: 100%; max-height: 100px;"
                         data-ad-client="ca-pub-3518000096682897"
                         data-ad-slot="2392223415"
                         data-ad-format="auto"
                         data-full-width-responsive="true"></ins>
                    <script>
                        (adsbygoogle = window.adsbygoogle || []).push({});
                    </script>
                </div>
            </div>
        @endif

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
                                    <!-- Technical Staff Section -->
                                    <h5>{{ trans('front.technical_staff') }}</h5>
                                    <div class="row">
                                        @if(count($team->teamAgents) < 1)
                                            <p class="center">{{ trans('front.no_technical_staff_in_team', ['team_name' => $team->name]) }}</p>
                                        @endif
                                        @foreach($team->teamAgents as $agent)
                                            <div class="col s6 m6 l4 xl3">
                                                <a href="{{ $agent->getPublicURL() }}">
                                                    <div class="player">
                                                        <img src="{{ $agent->picture ?: config('custom.default_profile_pic') }}"
                                                             alt="{{ $agent->name }}">
                                                        <span>
                                                            {{ $agent->name }}
                                                            <small style="display: block; color: white; font-size: 0.9em;">
                                                                {{ $agent->getAgentTypeTranslated() }}
                                                            </small>
                                                        </span>
                                                    </div>
                                                </a>
                                            </div>
                                        @endforeach
                                    </div>

                                    <!-- Players Section -->
                                    <h5>{{ trans('front.players') }}</h5>
                                    <div class="row">
                                        @if(count($team->getCurrentPlayers()) < 1)
                                            <p class="center">{{ trans('front.no_players_in_team', ['team_name' => $team->name]) }}</p>
                                        @endif
                                        @foreach($team->getCurrentPlayers() as $player)
                                            <div class="col s6 m6 l4 xl3">
                                                <a href="{{ $player->getPublicURL() }}">
                                                    <div class="player">
                                                        <img src="{{ $player->getAgeSafePicture() }}"
                                                             alt="{{ $player->name }}">
                                                        <span>{{ $player->displayName() }}</span>
                                                    </div>
                                                </a>

                                            </div>
                                        @endforeach
                                    </div>
                                    @if(has_permission('teams.edit.' . $team->id))
                                        <div class="row">
                                            <a href="{{ route('teams.show', ['team' => $team]) }}"
                                               class="btn-floating btn-large waves-effect waves-light blue right"><i
                                                        class="material-icons">edit</i></a>
                                        </div>
                                    @endif

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
                                            <img src="{{ $transfer->player->getPicture() }}"
                                                 alt="{{ $transfer->player->displayName() }}">
                                        </figure>

                                        <div class="info">
                                            <span style="line-height: 20px">{{ $transfer->player->displayName() }}<br/><small
                                                        style="color: grey">{{ $transfer->team ? $transfer->team->name : 'Nenhuma Equipa' }}</small></span>

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

    @if(has_permission('clubs.edit.' . $club->id))
        <div class="row">
            <div class="container">
                <a href="{{ route('clubs.show', ['club' => $club]) }}"
                   class="btn-floating btn-large waves-effect waves-light blue right"><i class="material-icons">edit</i></a>
            </div>
        </div>
    @endif

@endsection

@section('scripts')
    <script src="/js/front/club-page-scripts.js"></script>
@endsection