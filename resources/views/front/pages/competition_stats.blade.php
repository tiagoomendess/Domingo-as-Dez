@extends('front.layouts.default-page')

@section('head-content')
    <title>{{ trans('front.competition_stats', ['competition' => $competition->name]) }}</title>
    <link rel="stylesheet" href="/css/front/competition-stats-style.css">

    <meta property="og:title" content="{{ trans('front.competition_stats', ['competition' => $competition->name]) }}"/>
    <meta property="og:type" content="website"/>
    <meta property="og:description" content="{{ trans('front.footer_desc') }}"/>
    <meta property="og:image" content="{{ url($competition->picture) }}">

@endsection

@section('content')
    <nav class="navigation-bar">
        <div class="nav-wrapper">
            <div class="container">
                <div class="col s12">
                    <a href="{{ route('competitions') }}" class="breadcrumb">{{ trans('models.competitions') }}</a>
                    <a href="{{ route('competition', ['slug' => str_slug($competition->name)]) }}"
                       class="breadcrumb">{{ $competition->name }}</a>
                    <a href="#stats" class="breadcrumb">{{ trans('front.statistics') }}</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container" id="stats">
        <div class="row no-margin-bottom">
            <div class="col s12">
                <h1 class="hide">{{ trans('front.competition_stats', ['competition' => $competition->name]) }}</h1>
            </div>

            <div class="col s12 m12 l12 xl6">
                <h2 class="over-card-title">
                    {{ trans('front.best_scorers') }}
                </h2>

                <div class="row">
                    <div class="col s12">
                        <div class="card">
                            <div class="card-content">
                                @if (count($bestScorers) == 0)
                                    <p class="center flow-text">{{ trans('front.stats_unavailable') }}</p>
                                @else
                                    <ul class="list-a best-scorers-list">
                                        @foreach($bestScorers as $scorer)
                                            <li>
                                                <a href="{{ $scorer['player']->getPublicURL() }}">
                                                    <figure>
                                                        <img src="{{ $scorer['player']->getPicture() }}"
                                                             alt="{{ $scorer['player']->displayName() }}">
                                                    </figure>

                                                    <div class="scorer-info">
                                                        <div class="names">
                                                <span class="scorer-name">
                                                    {{ $scorer['player']->name }}</span>
                                                            <span class="scorer-nickname">
                                                    {{ $scorer['player']->nickname }}
                                                </span>
                                                        </div>

                                                        <span class="scorer-goals">
                                                <span class="right">
                                                    {{ $scorer['amount'] }}
                                                </span>
                                            </span>
                                                    </div>
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col s12 m12 l12 xl6">
                <h2 class="over-card-title">
                    {{ trans('front.attack') }}
                </h2>
                <div class="row no-margin-bottom">
                    <div class="col s6">
                        <div class="card">
                            <div class="card-content">
                                <div class="club-stats">
                                    <span class="desc-best">{{ trans('general.best') }}</span>
                                    @if(!empty($attack['best']['team']))
                                        <figure>
                                            <img src="{{ $attack['best']['team']->club->getEmblem() }}" alt=""/>
                                        </figure>
                                        <span class="club-name">{{ $attack['best']['team']->club->name }}</span>
                                        <span class="club-goal-count">
                                        {{ trans_choice('front.amount_goals_scored', $attack['best']['goal_count']) }}
                                    </span>
                                    @else
                                        <p class="center flow-text">{{ trans('front.stats_unavailable') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col s6">
                        <div class="card">
                            <div class="card-content">
                                <div class="club-stats">
                                    <span class="desc-worst">{{ trans('general.worst') }}</span>
                                    @if(!empty($attack['worst']['team']))
                                        <figure>
                                            <img src="{{ $attack['worst']['team']->club->getEmblem() }}" alt=""/>
                                        </figure>
                                        <span class="club-name">{{ $attack['worst']['team']->club->name }}</span>
                                        <span class="club-goal-count">
                                        {{ trans_choice('front.amount_goals_scored', $attack['worst']['goal_count']) }}
                                    </span>
                                    @else
                                        <p class="center flow-text">{{ trans('front.stats_unavailable') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <h2 class="over-card-title">
                    {{ trans('front.defense') }}
                </h2>
                <div class="row">
                    <div class="col s6">
                        <div class="card">
                            <div class="card-content">
                                <div class="club-stats">
                                    <span class="desc-best">{{ trans('general.best') }}</span>
                                    @if(!empty($defense['best']['team']))
                                        <figure>
                                            <img src="{{ $defense['best']['team']->club->getEmblem() }}" alt=""/>
                                        </figure>
                                        <span class="club-name">{{ $defense['best']['team']->club->name }}</span>
                                        <span class="club-goal-count">
                                        {{ trans_choice('front.amount_goals_against', $defense['best']['goal_count']) }}
                                    </span>
                                    @else
                                        <p class="center flow-text">{{ trans('front.stats_unavailable') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col s6">
                        <div class="card">
                            <div class="card-content">
                                <div class="club-stats">
                                    <span class="desc-worst">{{ trans('general.worst') }}</span>
                                    @if(!empty($defense['worst']['team']))
                                        <figure>
                                            <img src="{{ $defense['worst']['team']->club->getEmblem() }}" alt=""/>
                                        </figure>
                                        <span class="club-name">{{ $defense['worst']['team']->club->name }}</span>
                                        <span class="club-goal-count">
                                        {{ trans_choice('front.amount_goals_against', $defense['worst']['goal_count']) }}
                                    </span>
                                    @else
                                        <p class="center flow-text">{{ trans('front.stats_unavailable') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="/js/front/competition-stats-scripts.js"></script>
@endsection