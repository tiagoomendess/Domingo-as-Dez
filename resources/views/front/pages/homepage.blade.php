@extends('front.layouts.default-page')

@section('head-content')
    <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
    <script>
        (adsbygoogle = window.adsbygoogle || []).push({
            google_ad_client: "ca-pub-2937345687433041",
            enable_page_level_ads: true
        });
    </script>
    <title>{{ config('custom.site_name') }}</title>
    <link rel="stylesheet" href="/css/front/homepage-style.css">
    <meta property="og:title" content="{{ config('app.name') }}" />
    <meta property="og:type" content="website" />
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

            <div class="col s6 m6 l4">
                <div class="vertical-spacer"></div>

                <a class="modal-trigger" href="#modal-pof">
                    <div class="player-of-the-week">

                        <div>
                            <span class="player-of-the-week-name">João Bita</span>
                            <span class="player-of-the-week-desc">{{ trans('front.player_of_the_week') }}</span>
                            <img src="{{ config('custom.default_profile_pic') }}" alt="">
                        </div>

                    </div>
                </a>

                <div id="modal-pof" class="modal">
                    <div class="modal-content">
                        <h4>Informação</h4>
                        <p class="flow-text">O João Bita não é o jogador da Semana. Apenas está aqui enquanto o sistema de escolha automático
                            não está pronto. Primeiro estamos a recolher dados, analisar e verificar quais os melhores fatores a ter em consideração.
                            Assim que estiver pronto será anunciado. Apenas terão hipotese de ser jogador da semana aqueles registados neste website.</p>
                    </div>
                    <div class="modal-footer">
                        <a href="#" class="modal-action modal-close waves-effect waves-green btn-flat">Ok</a>
                    </div>
                </div>

            </div>

            <div class="col s6 m6 hide-on-large-only">
                <div class="vertical-spacer"></div>
                <a href="http://www.slc.pt">
                    <div class="pub">
                        <img src="/images/slc_pub_1by1.jpg" alt="">
                    </div>
                </a>
            </div>

        </div>

        @if (!Auth::check())
            <div class="row hide-on-large-only no-margin-bottom">
                <div class="col s12">
                    <div class="vertical-spacer"></div>
                    <div class="divider"></div>
                    <div class="auth-buttons">
                        <a href="{{ route('login') }}" class="waves-effect waves-light btn btn-large blue">{{ trans('auth.login') }}</a>
                        <a href="{{ route('register') }}" class="waves-effect waves-light btn btn-large blue">{{ trans('auth.register') }}</a>

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
                        <img class="circle" src="{{ \Illuminate\Support\Facades\Auth::user()->profile->getPicture() }}" alt="">
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


    <div class="container">
        <div class="row">
            <div class="col s6 m4 l3">
                <a href="http://www.prediol.com">
                    <img style="width: 100%" src="/images/prediol_card_pub.jpeg" alt="">
                </a>
            </div>

            <div class="col s6 m4 l3">
                <a href="http://www.slc.pt">
                    <img style="width: 100%" src="/images/slc_pub_1by1.jpg" alt="">
                </a>
            </div>
        </div>

    </div>

@endsection

@section("scripts")
    <script src="/js/front/homepage-scripts.js"></script>
    <script>
        $(document).ready(function(){
            // the "href" attribute of the modal trigger must specify the modal ID that wants to be triggered
            $('.modal').modal();
        });
    </script>
@endsection
