@extends('front.layouts.no-container')

@section('head-content')
    <title>
        {{ $game->homeTeam->club->name }} vs {{ $game->awayTeam->club->name }}
        {{ $game->season->competition->name }}
        @if($game->season->start_year != $game->season->end_year)
            {{ $game->season->start_year }}/{{ $game->season->end_year }}
        @else
            {{ $game->season->start_year }}
        @endif
    </title>
@endsection

@section('content')

    <div class="parallax-container">

        <div class="parallax" style="background-color: black">
            @if($game->playground->picture)
                <img style="opacity: 0.5" src="{{ $game->playground->picture }}" alt="">
            @else
                <img style="opacity: 0.5" src="{{ \App\Media::getPlaceholder('16:9', $game->homeTeam->club->id) }}" alt="">
            @endif

        </div>

        <div style="height: 100%;">
            <div class="outer">
                <div class="middle">
                    <div class="inner">

                        <div class="container">
                            <div class="row">

                                <div class="col xs12 s12 hide-on-med-and-up">
                                    <h4 class="center white-text light">
                                        @if($game->season->competition->competition_type == 'league')
                                            {{ trans('front.league_round') }} {{ $game->round  }}
                                        @elseif($game->season->competition->competition_type == 'cup')
                                            {{ trans('front.cup_round') }} {{ $game->round  }}
                                        @else
                                            {{ trans('front.round') }} {{ $game->round  }}
                                        @endif
                                    </h4>
                                </div>

                                <div class="col s6 m4 l4 center">
                                    <img class="game-emblem" src="{{ $game->homeTeam->club->getEmblem() }}" alt="">
                                    <p class="flow-text white-text" style="margin-top: 5px">{{ $game->homeTeam->club->name }}</p>
                                </div>

                                <div class="col s12 m4 l4 hide-on-small-and-down">

                                    <h4 class="center white-text light">
                                        @if($game->season->competition->competition_type == 'league')
                                            {{ trans('front.league_round') }} {{ $game->round  }}
                                        @elseif($game->season->competition->competition_type == 'cup')
                                            {{ trans('front.cup_round') }} {{ $game->round  }}
                                        @else
                                            {{ trans('front.round') }} {{ $game->round  }}
                                        @endif
                                    </h4>

                                    @if($game->started())
                                        <div id="game_started" class="center">
                                            <h1 class="center white-text">
                                                {{ $game->getHomeScore() }} - {{ $game->getAwayScore() }}
                                            </h1>
                                        </div>
                                    @endif

                                    <ul class="center white-text">
                                        <li>
                                            <i class="material-icons valign-middle">date_range</i>
                                            <span class="valign-middle">
                                                {{ \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $game->date)->format("d/m/Y") }}
                                            </span>
                                        </li>

                                        <li>
                                            <i class="material-icons valign-middle">access_time</i>
                                            <span class="valign-middle">
                                                {{ \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $game->date)->format("H\Hi") }}
                                            </span>
                                        </li>

                                        <li>
                                            <i class="material-icons valign-middle">place</i>
                                            <span class="valign-middle">
                                                {{ $game->playground->name }}
                                            </span>
                                        </li>
                                    </ul>

                                </div>

                                <div class="col s6 m4 l4 center">
                                    <img class="game-emblem" src="{{ $game->awayTeam->club->getEmblem() }}" alt="">
                                    <p class="flow-text white-text" style="margin-top: 5px">{{ $game->awayTeam->club->name }}</p>
                                </div>

                                <div class="col xs12 s12 hide-on-med-and-up center">

                                    @if($game->started())
                                        <div id="game_started">
                                            <h1 class="white-text">{{ $game->getHomeScore() }} - {{ $game->getAwayScore() }}</h1>
                                        </div>
                                    @endif

                                    <div id="game_info">
                                        <p class="white-text">
                                            <i class="material-icons valign-middle">date_range</i> <span class="valign-middle">{{ \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $game->date)->format("d/m/Y") }}</span> |
                                            <i class="material-icons valign-middle">access_time</i> <span class="valign-middle">{{ \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $game->date)->format("H\Hi") }}</span> |
                                            <i class="material-icons valign-middle">place</i> <span class="valign-middle">{{ $game->playground->name }}</span>
                                        </p>
                                    </div>

                                </div>

                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="section grey lighten-4">
        <div class="container">
            <div class="row">
                <div class="col s7 m7 l5">
                    <div class="input-field col s12">
                        <select id="competition_id">
                            <option value="{{ $game->season->competition->id }}" selected>{{ $game->season->competition->name }}</option>
                        </select>
                        <label>{{ trans('models.competition') }}</label>
                    </div>
                </div>

                <div class="col s5 m5 l2">
                    <div class="input-field col s12">
                        <select id="season_id">
                            <option value="{{ $game->season->id }}" selected>@if($game->season->start_year != $game->season->end_year){{ $game->season->start_year }}/{{ $game->season->end_year }}@else{{ $game->season->start_year }}@endif</option>
                        </select>
                        <label>{{ trans('models.season') }}</label>
                    </div>
                </div>

                <div class="col s12 m12 l5">
                    <div class="input-field col s12">
                        <select id="game_id">
                            <option value="{{ $game->id }}" selected>{{ $game->homeTeam->club->name }} vs {{ $game->awayTeam->club->name }}</option>
                        </select>
                        <label>{{ trans('models.game') }}</label>
                    </div>
                </div>
            </div>

            <div class="row">

                <div class="col xs12 s12 m12 l4">
                    <div class="card col s12">
                        <div class="card-content">
                            <span class="card-title"><img style="width: 20px;" src="{{ $game->homeTeam->club->getEmblem() }}" alt=""> {{ trans('models.goals') }}</span>

                            <ul class="collection">

                                @foreach($game->goals as $goal)

                                    @if($goal->team->id == $game->homeTeam->id)
                                        <li class="collection-item avatar">
                                            <img src="{{ $goal->getPlayerPicture() }}" alt="" class="circle">
                                            <span class="title">
                                                @if($goal->getPlayerNickname())
                                                    {{ $goal->getPlayerName() }} ({{ $goal->getPlayerNickname() }})
                                                @else
                                                    {{ $goal->getPlayerName() }}
                                                @endif
                                            </span>
                                            <p>{{ $goal->minute }}"
                                                @if($goal->penalty)
                                                    <br> {{ trans('models.penalty') }}
                                                @elseif($goal->own_goal)
                                                    <br> {{ trans('models.own_goal') }}
                                                @endif
                                            </p>
                                        </li>
                                    @endif

                                @endforeach
                            </ul>
                        </div>
                    </div>

                </div>

                <div class="col xs12 s12 m12 l4">
                    <div class="card">
                        <div class="card-content">
                            <span class="card-title center">{{ trans('front.referee_team') }}</span>

                        </div>
                    </div>
                </div>

                <div class="col xs12 s12 m12 l4">
                    <div class="card col s12">
                        <div class="card-content">
                            <span class="card-title"><img style="width: 20px;" src="{{ $game->awayTeam->club->getEmblem() }}" alt=""> {{ trans('models.goals') }}</span>

                            <ul class="collection">
                                @foreach($game->goals as $goal)

                                    @if($goal->team->id == $game->awayTeam->id)
                                        <li class="collection-item avatar">
                                            <img src="{{ $goal->getPlayerPicture() }}" alt="" class="circle">
                                            <span class="title">
                                                @if($goal->getPlayerNickName())
                                                    {{ $goal->getPlayerName() }} ({{ $goal->getPlayerNickName() }})
                                                @else
                                                    {{ $goal->getPlayerName() }}
                                                @endif
                                            </span>
                                            <p>{{ $goal->minute }}"
                                            </p>
                                        </li>
                                    @endif

                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>



@endsection

@section ('scripts')
    <script>
        $(document).ready(function(){
            $('.parallax').parallax();
            $('select').material_select();
        });
    </script>
@endsection