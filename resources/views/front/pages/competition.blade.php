@extends('front.layouts.default-page')

@section('head-content')
    <title>{{ $competition->name }}</title>
    <link rel="stylesheet" href="/css/front/competition-style.css">

    <meta property="og:title" content="{{ $competition->name . ' - ' . config('app.name') }}"/>
    <meta property="og:type" content="website"/>
    <meta property="og:description" content="{{ trans('front.footer_desc') }}"/>
    <meta property="og:image" content="{{ url($competition->picture) }}">

@endsection

@section('content')
    <div style="min-height: 100vh">
        <h1 class="hide">{{ $competition->name }}</h1>

        <div class="competition-season-selector">
            <div class="container">
                <div class="row no-margin-bottom">
                    <div class="input-field col s12 m6 l4">
                        <select id="competition_selector" class="icons">
                            <option slug="{{ str_slug($competition->name) }}" class="left circle"
                                    value="{{ $competition->id }}" data-icon="{{ $competition->picture }}"
                                    selected>{{ $competition->name }}</option>
                        </select>
                        <label>{{ trans('models.competition') }}</label>
                    </div>

                    <div class="right input-field col s12 m4 l2">
                        <select class="" id="season_selector">
                        </select>
                        <label>{{ trans('models.season') }}</label>
                    </div>
                </div>
            </div>
        </div>

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


        <div class="row">
            <div class="col col-xs-12 s12 m6 l4 offset-l4 offset-m3">
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

    </div>
@endsection

@section('scripts')
    <script src="/js/front/competition-scripts.js"></script>
    <script src="/js/front/points-tie-breakers-scripts.js"></script>
@endsection
