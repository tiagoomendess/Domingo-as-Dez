@extends('front.layouts.default-page')

@section('head-content')
    <title>{{ config('custom.site_name') }}</title>
    <link rel="stylesheet" href="/css/front/homepage-style.css">

@endsection
@section('content')

    <div class="container">

        <div class="row no-margin-bottom {{ $live ? '' : 'hide' }}" id="is_live_warning">
            <div class="col s12">
                <a href="{{ route('games.live') }}">
                    <div class="is_live_outer">
                        <div>
                            <p>{{ trans('front.is_live') }}</p>
                            <span>{{ trans('front.is_live_desc') }}</span>
                        </div>

                        <div>
                            <i class="material-icons">arrow_forward_ios</i>
                        </div>

                    </div>
                </a>
            </div>
        </div>

        <div class="row no-margin-bottom">
            <div class="col s12 m12 l8">
                <div class="vertical-spacer"></div>
                <div class="aspect-ratio-16by9">
                    <div class="aspect-ratio-inside" id="news_snippets">

                        <div class="news-snippet-progress" style="width: 0%"></div>

                        @foreach($articles as $index => $article)

                            <a href="{{ $article->getPublicUrl() }}" class="{{ $index > 0 ? 'hide': 'active' }}">

                                <div class="news-thumbnail">

                                    <div>
                                        @if ($article->media)

                                            <img src="{{ $article->media->thumbnail_url }}" alt="{{ $article->media->tags }}">

                                        @else
                                            <img src="{{ \App\Media::getPlaceholder('16:9', $article->id) }}" alt="">
                                        @endif
                                    </div>

                                    <span class="news-title">{{ $article->title }}</span>

                                </div>
                            </a>

                        @endforeach
                    </div>
                </div>
            </div>

            <div class="col s12 m12 l4">
                <div class="vertical-spacer"></div>
                <a href="">
                    <div class="player-of-week">
                        <figure>
                            <img src="{{ config('custom.default_profile_pic') }}" alt="">
                            <figcaption>João Bita</figcaption>
                        </figure>
                        <span>
                        {{ trans('front.player_of_the_week') }}
                    </span>
                    </div>
                </a>

            </div>
        </div>

        <div class="row hide-on-large-only no-margin-bottom">
            <div class="col s12">
                <div class="vertical-spacer"></div>
                <div class="divider"></div>
                <div class="auth-buttons">
                    <a class="waves-effect waves-light btn btn-large blue">{{ trans('auth.login') }}</a>
                    <a class="waves-effect waves-light btn btn-large blue">{{ trans('auth.register') }}</a>

                </div>
                <div class="divider"></div>
            </div>
        </div>

        <div class="row no-margin-bottom">

            <div class="col s6 m6 l3">
                <div class="front-page-facts">
                    <span>{{ $total_clubs }}</span>
                    <span>{{ trans('models.clubs') }}</span>
                </div>
            </div>

            <div class="col s6 m6 l3">
                <div class="front-page-facts">
                    <span>{{ $total_games }}</span>
                    <span>{{ trans('models.games') }}</span>
                </div>
            </div>

            <div class="col s6 m6 l3">
                <div class="front-page-facts">
                    <span>{{ $total_players }}</span>
                    <span>{{ trans('models.players') }}</span>
                </div>
            </div>

            <div class="col s6 m6 l3">
                <div class="front-page-facts">
                    <span>{{ $total_goals }}</span>
                    <span>{{ trans('models.goals') }}</span>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col s12">
                <div class="vertical-spacer"></div>
                <h2 class="over-card-title">{{ trans('models.competitions') }}</h2>
            </div>

            @foreach($competitions as $competition)
            <div class="col s6 m6 l3">
                <a href="{{ route('competition', ['slug' => str_slug($competition->name)]) }}">
                    <div class="homepage-competition-box">
                        <img src="{{ $competition->picture }}" alt="">
                        <span>{{ $competition->name }}</span>
                    </div>
                </a>

            </div>
            @endforeach

        </div>

    </div>

@endsection

@section("scripts")
    <script src="/js/front/homepage-scripts.js"></script>
@endsection
