@extends('front.layouts.default-page')

@section('head-content')
    <title>
        {{ $game->homeTeam->club->name }} vs {{ $game->awayTeam->club->name }}
    </title>
    <link rel="stylesheet" href="/css/front/game-style.css">

    <meta property="og:title" content="{{ $game->homeTeam->club->name }} vs {{ $game->awayTeam->club->name }}"/>
    <meta property="og:type" content="website"/>
    <meta property="og:description" content="{{ trans('front.footer_desc') }}"/>
    <meta property="og:image" content="{{ url($game->game_group->season->competition->picture) }}">

@endsection

@section('content')
    <div class="parallax-container">
        <div class="parallax">
            @if ($game->playground)
                <img src="{{ $game->playground->getPicture() }}" alt="{{ $game->playground->name }}">
            @else
                <img src="{{ \App\Media::getPlaceholder('16:9', $game->id) }}">
            @endif
        </div>

        <div class="game-header">
            <div class="details">
                <span class="hide" id="exact_time">{{ $game->date }}</span>
                <time>
                    <i class="material-icons">date_range</i> {{ \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $game->date)->timezone('Europe/Lisbon')->format("d/m/Y") }}
                </time>
                <time>
                    <i class="material-icons">access_time</i> {{ \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $game->date)->timezone('Europe/Lisbon')->format("H:i") }}
                </time>
                @if ($game->playground)<span><i
                            class="material-icons">location_on</i> {{ $game->playground->name }}</span>@endif
                <span>
                    <img src="{{$game->game_group->season->competition->picture}}"
                         alt="{{ $game->game_group->season->competition->name }}">
                    {{ $game->game_group->season->competition->name }}
                </span>
            </div>

            <div class="container">

                <div class="row no-margin-bottom">

                    <div class="col l3 xl3 hide-on-med-and-down">
                        <span class="club-name right">{{ $game->home_team->club->name }}</span>
                    </div>

                    <div class="col xs4 s4 m4 l2 xl2 center">
                        <a href="{{ $game->home_team->club->getPublicURL() }}">
                            <figure>
                                <img src="{{ $game->home_team->club->getEmblem() }}"
                                     alt="{{ $game->home_team->club->name }}">
                            </figure>
                        </a>
                    </div>
                    <div class="col xs4 s4 m4 l2 xl2 center">
                        <div class="separator">
                            <span class="hide" id="game_id">{{ $game->id }}</span>
                            @if($game->started())
                                <span id="score">{{ $game->getHomeScore() }} - {{ $game->getAwayScore() }}</span>
                                <div class="hide" id="countdown">
                                    <table>
                                        <tr class="time">
                                            <td>00</td>
                                            <td>00</td>
                                            <td>00</td>
                                            <td>00</td>
                                        </tr>
                                        <tr class="description">
                                            <td>{{ trans('front.days_small') }}</td>
                                            <td>{{ trans('front.hours_small') }}</td>
                                            <td>{{ trans('front.minutes_small') }}</td>
                                            <td>{{ trans('front.seconds_small') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            @else
                                <div id="countdown">
                                    <table>
                                        <tr class="time">
                                            <td>00</td>
                                            <td>00</td>
                                            <td>00</td>
                                            <td>00</td>
                                        </tr>
                                        <tr class="description">
                                            <td>{{ trans('front.days_small') }}</td>
                                            <td>{{ trans('front.hours_small') }}</td>
                                            <td>{{ trans('front.minutes_small') }}</td>
                                            <td>{{ trans('front.seconds_small') }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <span class="hide" id="score">{{ $game->getHomeScore() }}
                                    - {{ $game->getAwayScore() }}</span>
                            @endif

                            @if($game->finished)
                                @if($game->decidedByPenalties())
                                    <span id="penalties">({{ trans('front.after_penalties', ['penalties_home' => $game->penalties_home, 'penalties_away' => $game->penalties_away]) }}
                                        )</span>
                                @else
                                    <span id="penalties" class="hide"></span>
                                @endif
                                <span id="finished">{{ trans('general.finished') }}</span>
                            @else
                                <span id="penalties" class="hide"></span>
                                <span id="finished" class="hide">{{ trans('general.finished') }}</span>
                            @endif

                        </div>
                    </div>

                    <div class="col xs4 s4 m4 l2 xl2 center">
                        <a href="{{ $game->away_team->club->getPublicURL() }}">
                            <figure>
                                <img src="{{ $game->away_team->club->getEmblem() }}"
                                     alt="{{ $game->home_team->club->name }}">
                            </figure>
                        </a>
                    </div>

                    <div class="col l3 xl3 hide-on-med-and-down">
                        <span class="club-name">{{ $game->away_team->club->name }}</span>
                    </div>

                </div>

                <div class="row no-margin-bottom">
                    <div id="man_of_the_match">
                        @if($game->finished && !$game->isMvpVoteOpen() && $mvp && $mvp->amount >= 1)
                            <span>Homem do Jogo:</span>
                            <div class="chip">
                                <img src="{{ $mvp->player->getPicture() }}" alt="{{ $mvp->player->displayName() }}, homem do jogo">
                                {{ $mvp->player->displayName() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="container">
        <div class="row no-margin-bottom center">
            @if($game->isMvpVoteOpen())
                @if($mvp_vote)
                    <div style="margin-top: 20px">
                        Votou em
                        <div class="chip">
                            <img src="{{ $mvp_vote->player->getPicture() }}" alt="Contact Person">
                            {{ $mvp_vote->player->name }}
                        </div>
                        para homem do Jogo!
                    </div>

                @else
                    @if(\Illuminate\Support\Facades\Auth::user())
                        <a class="btn green darken-2 waves-effect waves-light modal-trigger" href="#mvp_vote_modal"
                           style="margin-top: 15px">Votar no Homem do Jogo</a>
                    @else
                        <a class="btn green darken-2 waves-effect waves-light modal-trigger" href="/login"
                           style="margin-top: 15px">Votar no Homem do Jogo (Faça Login)</a>
                    @endif
                @endif
            @endif
        </div>

        <div class="row">

            <section class="col xs12 s12 m12 l4 xl4">
                <h2 class="over-card-title">{{ trans('front.goals_from', ['club' => $game->home_team->club->name]) }}</h2>
                <div class="card">
                    <div class="card-content">
                        <div id="home_goals">
                            @foreach($game->getHomeGoals()->sortBy('minute') as $goal)
                                <div class="goal-overview">
                                    <a href="
                                        @if(has_permission('goals.edit.' . $goal->id))
                                    {{ route('goals.show', ['goal' => $goal]) }}
                                    @elseif(!is_null($goal->player))
                                    {{ route('front.player.show', ['id' => $goal->player->id, 'name_slug' => str_slug($goal->player->name)]) }}
                                    @else
                                            #
                                        @endif">

                                        <figure>
                                            <img src="{{ $goal->getPlayerPicture() }}"
                                                 alt="{{ $goal->getPlayerName() }}">
                                        </figure>
                                        <span class="player-name">
                                            {{ $goal->getPlayerName() }}
                                            @if ($goal->own_goal)
                                                <small>{{ trans('models.own_goal') }}</small>
                                            @elseif($goal->penalty)
                                                <small>{{ trans('models.penalty') }}</small>
                                            @endif
                                        </span>
                                        @if ($goal->minute)
                                            <span class="minute right">{{ $goal->minute }}"</span>
                                        @endif
                                    </a>
                                </div>
                            @endforeach

                            @if (count($game->getHomeGoals()) < 1)
                                <span class="center">{{ trans('models.no_goals') }}</span>
                            @endif

                            @if(has_permission('goals.create'))
                                <div class="add-goal-btn">
                                    <a href="{{ route('goals.create', ['game_id' => $game->id, 'team_id' => $game->home_team->id])}}"
                                       class="waves-effect waves-light btn-flat"><i
                                                class="material-icons left">add</i> {{trans('general.add')}}</a>
                                </div>
                            @endif

                        </div>
                    </div>
                </div>
            </section>

            <section class="col xs12 s12 m12 l4 xl4">
                <h2 class="over-card-title">{{ trans('models.referees') }}</h2>
                <div class="card">
                    <div class="card-content">

                        <ul class="list-a">
                            @foreach($game->game_referees as $game_referee)
                                <li>
                                    <a href="{{ $game_referee->referee->getPublicURL() }}">
                                        <div class="ref-overview">
                                            <figure>
                                                <img src="{{ $game_referee->referee->getPicture() }}"
                                                     alt="{{ $game_referee->referee->name }}">
                                            </figure>

                                            <div class="ref-info">
                                                <span class="ref-name">{{ $game_referee->referee->name }}</span>
                                                <span class="ref-type">{{ trans('general.' . $game_referee->referee_type->name) }}</span>
                                            </div>

                                        </div>
                                    </a>

                                </li>
                            @endforeach
                        </ul>

                        @if (count($game->game_referees) < 1)
                            <span class="zero-count">{{ trans('front.no_referees') }}</span>
                        @endif
                    </div>
                </div>
            </section>

            <section class="col xs12 s12 m12 l4 xl4">
                <h2 class="over-card-title">{{ trans('front.goals_from', ['club' => $game->away_team->club->name]) }}</h2>
                <div class="card">
                    <div class="card-content">
                        <div id="away_goals">
                            @foreach($game->getAwayGoals()->sortBy('minute') as $goal)
                                <div class="goal-overview">
                                    <a href="
                                        @if(has_permission('goals.edit.' . $goal->id))
                                    {{ route('goals.show', ['goal' => $goal]) }}
                                    @elseif(!is_null($goal->player))
                                    {{ route('front.player.show', ['id' => $goal->player->id, 'name_slug' => str_slug($goal->player->name)]) }}
                                    @else
                                            #
                                        @endif">

                                        <figure>
                                            <img src="{{ $goal->getPlayerPicture() }}"
                                                 alt="{{ $goal->getPlayerName() }}">
                                        </figure>
                                        <span class="player-name">
                                            {{ $goal->getPlayerName() }}
                                            @if ($goal->own_goal)
                                                <small>{{ trans('models.own_goal') }}</small>
                                            @elseif($goal->penalty)
                                                <small>{{ trans('models.penalty') }}</small>
                                            @endif
                                        </span>

                                        @if ($goal->minute)
                                            <span class="minute right">{{ $goal->minute }}"</span>
                                        @endif

                                    </a>

                                </div>
                            @endforeach

                            @if (count($game->getAwayGoals()) < 1)
                                <span class="center">{{ trans('models.no_goals') }}</span>
                            @endif

                            @if(has_permission('goals.create'))
                                <div class="add-goal-btn">
                                    <a href="{{ route('goals.create', ['game_id' => $game->id, 'team_id' => $game->away_team->id])}}"
                                       class="waves-effect waves-light btn-flat"><i
                                                class="material-icons left">add</i> {{trans('general.add')}}</a>
                                </div>
                            @endif

                        </div>
                    </div>
                </div>
            </section>

        </div>
    </div>

    <!-- MVP Vote Modal -->
    <div id="mvp_vote_modal" class="modal bottom-sheet modal-fixed-footer">
        <div class="modal-content">
            <div class="container">
                <div class="row">
                    <div class="col s12">
                        <h4 class="center">Escolha o Homem do Jogo</h4>
                    </div>
                    <div class="col s12 m12 l6">
                        <h5>{{ $game->home_team->club->name }}</h5>
                        <ul class="list-a mvp-list">
                            @foreach($game->home_team->getCurrentPlayers() as $player)
                                <li>
                                    <a href="#" class="mvp_player" data-content="{{ $player->id }}">
                                        <img src="{{ $player->getPicture() }}" alt="">
                                        <span>{{ $player->displayName() }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="col s12 m12 l6">
                        <h5>{{ $game->away_team->club->name }}</h5>
                        <ul class="list-a mvp-list">
                            @foreach($game->away_team->getCurrentPlayers() as $player)
                                <li>
                                    <a href="#" class="mvp_player" data-content="{{ $player->id }}">
                                        <img src="{{ $player->getPicture() }}" alt="">
                                        <span>{{ $player->displayName() }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <a href="#" class="modal-action modal-close waves-effect waves-green btn-flat">Cancelar</a>
            <a href="#" id="mvp_submit_btn" class="modal-action waves-effect waves-green btn-flat disabled">Votar</a>
        </div>

        <form id="mvp_form" action="{{ route('mvp_vote') }}" method="POST">
            {{ csrf_field() }}
            <input type="hidden" name="game" value="{{ $game->id }}">
            <input type="hidden" id="mpv_player_id" name="player" value="">
        </form>
    </div>

    @if(has_permission('games'))
        <div class="row">
            <div class="container">
                <a href="{{ route('games.show', ['game' => $game]) }}"
                   class="btn-floating btn-large waves-effect waves-light blue right"><i class="material-icons">edit</i></a>

                <form action="{{ route('generate_game_image', ['game' => $game->id]) }}" method="POST" target="_blank">
                    {{ csrf_field() }}
                    <button class="btn waves-effect waves-light green darken-3" type="submit" name="action"><i class="material-icons right">cloud_download</i>Download Imagem</button>
                </form>
            </div>
        </div>
    @endif

@endsection

@section ('scripts')
    <script src="/js/front/game-scripts.js"></script>
    <script src="/js/front/mvp_votes-scripts.js"></script>
@endsection