@extends('front.layouts.default-page')

@section('head-content')
    <title>{{ $display_name }}</title>
    <link rel="stylesheet" href="/css/front/competition-style.css?v=season-stepper">

    <meta property="og:title" content="{{ $display_name . ' - ' . config('app.name') }}"/>
    <meta property="og:type" content="website"/>
    <meta property="og:description" content="{{ trans('front.footer_desc') }}"/>
    <meta property="og:image" content="{{ url($display_picture) }}">

@endsection

@section('content')
    <div style="min-height: 100vh">
        <div class="competition-season-selector">
            <div class="container">
                <div class="row no-margin-bottom valign-wrapper">
                    <div class="col s12 m8 l9 competition-heading">
                        <img id="competition_logo" class="competition-heading-logo" src="{{ $display_picture }}" alt="">
                        <h1 id="competition_title" class="competition-heading-title">{{ $display_name }}</h1>
                    </div>

                    <div class="col s12 m4 l3">
                        <div id="season_stepper" class="season-stepper">
                            <a id="season_prev" href="javascript:void(0)" class="button button-left disabled" role="button"
                               aria-label="{{ trans('general.previous') }}"><i
                                        class="material-icons no-select">keyboard_arrow_left</i></a>
                            <a id="season_current" href="javascript:void(0)" class="season-stepper-name" role="button"
                               aria-haspopup="dialog"></a>
                            <a id="season_next" href="javascript:void(0)" class="button button-right disabled" role="button"
                               aria-label="{{ trans('general.next') }}"><i
                                        class="material-icons no-select">keyboard_arrow_right</i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="season_picker_modal" class="modal">
            <div class="modal-content">
                <h5 class="season-picker-title">{{ trans('models.season') }}</h5>
                <ul id="season_picker_list" class="season-picker-list"></ul>
            </div>
            <div class="modal-footer">
                <a href="javascript:void(0)"
                   class="modal-action modal-close waves-effect btn-flat">{{ trans('general.close') }}</a>
            </div>
        </div>

        <script>
            window.competitionPage = {
                seasonSlug: @json($season_slug),
                competitionSlug: @json($display_slug),
                competitionId: {{ $competition->id }},
                seasonId: {{ $season->id }}
            };
        </script>

        @if($game_started_and_not_finished)
            <div class="container">
                <div class="row no-margin-bottom">
                    <div class="col s12 m12 l12 xl12">
                        <div>
                            <p class="flow-text red-text text-darken-2">
                                Atenção: Os resultados nesta página <b>não são atualizados</b> automaticamente. Para seguir todos os resultados com as atualizações mais recentes visite a página <a href="/direto">Resultados em Direto</a>. Os resultados apresentados nesta página podem já estar desatualizados.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        
        <div id="group_template" class="game-group hide">
            <div class="container">
                <h2 class="game-group-title">
                </h2>
                <div class="row no-margin-bottom">
                    <div class="col xs12 s12 m12 l12 xl6">
                        <section class="card">
                            <div class="card-content group-games">
                                <div class="round-title">
                                    <a class="button button-left"><i
                                                class="material-icons no-select">keyboard_arrow_left</i></a>
                                    <span class="round-name"></span>
                                    <a class="button button-right"><i
                                                class="material-icons no-select">keyboard_arrow_right</i></a>
                                </div>

                                <div class="games hide" id="games">
                                    <div class="overview hide" id="overview">
                                        <a href="">
                                            <div class="teams">
                                                <div class="col s4 m5 home-team">
                                                    <div>
                                                        <span class="hide-on-small-only"></span>
                                                        <img src="" alt="">
                                                    </div>
                                                </div>

                                                <div class="separator col s4 m2">
                                                    <time></time>
                                                </div>

                                                <div class="away-team col s4 m5">
                                                    <div>
                                                        <img src="" alt="">
                                                        <span class="hide-on-small-only"></span>
                                                    </div>
                                                </div>

                                            </div>
                                        </a>
                                    </div>
                                </div>

                            </div>
                        </section>
                    </div>

                    <div class="col xs12 s12 m12 l12 xl6">
                        <section class="card">
                            <div class="card-content group-table">

                                <div class="center table-loading hide">
                                    <div class="preloader-wrapper small active">
                                        <div class="spinner-layer spinner-blue-only">
                                            <div class="circle-clipper left">
                                                <div class="circle"></div>
                                            </div>
                                            <div class="gap-patch">
                                                <div class="circle"></div>
                                            </div>
                                            <div class="circle-clipper right">
                                                <div class="circle"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tables">

                                    <table id="table" class="positions-table hide">
                                        <thead>
                                        <tr>
                                            <th class="number">#</th>
                                            <th></th>
                                            <th>{{ trans('front.table_club') }}</th>
                                            <th class="number hide-on-small-and-down">{{ trans('front.table_played') }}</th>
                                            <th class="number hide-on-med-and-down">{{ trans('front.table_wins') }}</th>
                                            <th class="number hide-on-med-and-down">{{ trans('front.table_draws') }}</th>
                                            <th class="number hide-on-med-and-down">{{ trans('front.table_loses') }}</th>
                                            <th class="number hide-on-med-and-down">{{ trans('front.table_goals_favor') }}</th>
                                            <th class="number hide-on-med-and-down">{{ trans('front.table_goals_against') }}</th>
                                            <th class="number hide-on-small-and-down">{{ trans('front.table_goal_difference') }}</th>
                                            <th class="number">{{ trans('front.table_points') }}</th>
                                        </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </div>

        <div id="groups">

        </div>

        <div id="main_loading" style="min-height: 80vh">
            <div class="container" style="padding-top: 100px;">
                <div class="center">
                    <div class="preloader-wrapper big active">
                        <div class="spinner-layer spinner-blue-only">
                            <div class="circle-clipper left">
                                <div class="circle"></div>
                            </div>
                            <div class="gap-patch">
                                <div class="circle"></div>
                            </div>
                            <div class="circle-clipper right">
                                <div class="circle"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div style="margin-top: 20px; margin-bottom: 20px" class="container center" id="season_obs">

        </div>

        <div class="row hide" id="stats_button">
            <div class="container center">
                <a id="stats_link" class="waves-effect waves-light btn-large green darken-3" href=""><i
                            class="material-icons left">insert_chart</i>{{ trans('front.statistics') }}</a></div>
        </div>

        @if(!has_permission('disable_ads') && \Config::get('custom.adsense_enabled'))
            <div class="row">
                <div class="col col-xs-12 s12 m10 l8 offset-m1 offset-l2">
                    <script async
                            src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-3518000096682897"
                            crossorigin="anonymous"></script>
                    <!-- Competitions -->
                    <ins class="adsbygoogle"
                         style="display:block"
                         data-ad-client="ca-pub-3518000096682897"
                         data-ad-slot="6228552458"
                         data-ad-format="auto"
                         data-full-width-responsive="true"></ins>
                    <script>
                        (adsbygoogle = window.adsbygoogle || []).push({});
                    </script>
                </div>
            </div>
        @endif
    </div>
@endsection

@section('scripts')
    <script src="/js/front/competition-scripts.js?v=season-stepper"></script>
    <script src="/js/front/points-tie-breakers-scripts.js"></script>
@endsection
