@extends('front.layouts.default-page')

@section('head-content')
    <title>{{ config('custom.site_name') }}</title>
    <link rel="stylesheet" href="/css/front/homepage-style.css">
    <meta property="og:title" content="{{ config('app.name') }}"/>
    <meta property="og:type" content="website"/>
@endsection
@section('content')

    <div class="container">
        <div class="row no-margin-bottom {{ $live ? '' : 'hide' }}" id="is_live_warning">
            <div class="col s12">
                <a href="{{ route('games.live') }}">
                    <div class="is_live_outer">
                        <div>
                            <p class="flow-text text-bold white-text">{{ trans('front.is_live') }}</p>
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
            <div class="col s12 m12 l12">
                <div class="vertical-spacer"></div>
                <div class="aspect-ratio-16by9">
                    <div class="aspect-ratio-inside" id="news_snippets">

                        <div class="news-snippet-progress" style="width: 0%"></div>

                        @foreach($articles as $index => $article)

                            <a href="{{ $article->public_url }}" class="{{ $index > 0 ? 'hide': 'active' }}">

                                <div class="news-thumbnail">

                                    <div>
                                        @if ($article->media)

                                            <img src="{{ $article->media->thumbnail_url }}"
                                                 alt="{{ $article->media->tags }}">

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

        </div>

        @if (!Auth::check())
            <div class="row hide-on-large-only no-margin-bottom">
                <div class="col s12">
                    <div class="vertical-spacer"></div>
                    <div class="divider"></div>
                    <div class="auth-buttons">
                        <a href="{{ route('login') }}"
                           class="waves-effect waves-light btn btn-large blue">{{ trans('auth.login') }}</a>
                        <a href="{{ route('register') }}"
                           class="waves-effect waves-light btn btn-large blue">{{ trans('auth.register') }}</a>
                    </div>
                    <div class="divider"></div>
                </div>
            </div>
        @else
            <div class="row hide-on-large-only no-margin-bottom">
                <div class="col s12">
                    <div class="vertical-spacer"></div>
                    <div class="divider"></div>
                    <div class="account-greeting">
                        <img class="circle" src="{{ \Illuminate\Support\Facades\Auth::user()->profile->getPicture() }}"
                             alt="">
                        <span class="flow-text">{{ trans('front.hello_user', ['name' => \Illuminate\Support\Facades\Auth::user()->name]) }}</span>
                    </div>
                    <div class="divider"></div>
                </div>
            </div>
        @endif

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

        <div class="row no-margin-bottom">
            @if(!has_permission('disable_ads'))
                <div class="col col-xs-12 s12 m12 l12" style="margin-top: 15px">
                    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-3518000096682897"
                            crossorigin="anonymous"></script>
                    <!-- Home page Horizontal -->
                    <ins class="adsbygoogle"
                         style="display:block"
                         data-ad-client="ca-pub-3518000096682897"
                         data-ad-slot="6406546239"
                         data-ad-format="auto"
                         data-full-width-responsive="true"></ins>
                    <script>
                        (adsbygoogle = window.adsbygoogle || []).push({});
                    </script>
                </div>
            @endif

            <div class="col s12 m12 l12 center text-center" style="margin-top: 10px">
                <div class="divider"></div>
                <div class="vertical-spacer"></div>
                <a class="waves-effect waves-light btn-large blue" href="{{ route('games.today') }}">
                    <i class="large material-icons left">date_range</i>
                    Jogos de Hoje
                </a>
                <div class="vertical-spacer"></div>
                <div class="divider"></div>
            </div>

            <div class="col s12">
                <div class="vertical-spacer"></div>
                <h2 class="over-card-title">{{ trans('models.competitions') }}</h2>
            </div>

            @foreach($competitions as $competition)
                <div class="col s6 m6 l3">
                    <a href="{{ route('competition', ['slug' => $competition->name_slug]) }}">
                        <div class="homepage-competition-box">
                            <img src="{{ $competition->picture }}" alt="">
                            <span class="truncate">{{ $competition->name }}</span>
                        </div>
                    </a>
                </div>
            @endforeach

            <div class="col s6 m6 l3">
                <a href="{{ route('competitions') }}">
                    <div class="homepage-competition-box">
                        <img src="/images/3-dot-white.png" alt="">
                        <span>{{ trans('front.see_all_competitions') }}</span>
                    </div>
                </a>
            </div>
        </div>

    </div>

    <div class="col s12 m12 l12 center text-center" style="margin-top: 10px">
        <div class="divider"></div>
        <div class="vertical-spacer"></div>
        <a class="waves-effect waves-light btn-large yellow darken-3" href="{{ route('info.create') }}">
            <i class="large material-icons left">send</i>
            Enviar Informação</a>
        <div class="vertical-spacer"></div>
        <div class="divider"></div>
    </div>

    <div class="vertical-spacer"></div>

@endsection

@section("scripts")
    <script src="/js/front/homepage-scripts.js"></script>
    <script>
        $(document).ready(function () {
            // the "href" attribute of the modal trigger must specify the modal ID that wants to be triggered
            $('.modal').modal();
        });
    </script>
@endsection
