@extends('front.layouts.default-page')

@section('head-content')
    <title> {{ trans('front.detailed_table') }} {{ $competition->name }}</title>
@endsection

@section('content')
    <h1> {{ trans('front.detailed_table') }} {{ $competition->name }}</h1>

    @if($competition->competition_type == 'league')

        <input id="competition_slug" type="hidden" value="{{ str_slug($competition->name) }}">
        <input id="season_id" type="hidden" value="{{ $season->id }}">
        <input id="round" type="hidden" value="{{ $round_chosen }}">
        <input id="max_round" type="hidden" value="{{ $season->getTotalRounds() }}">

        <div class="row">
            <div class="card col xs12 s12 m12 l12">
                <div class="card-content">
                    <div class="row">

                        <table>
                            <th><a onclick="leftClick()" id="left_button" class="waves-effect waves btn-flat"><i class="material-icons left">arrow_back</i></a></th>
                            <th id="round_name">
                                <p class="center">{{ trans('front.league_round') }} {{ $round_chosen }}</p>
                            </th>
                            <th><a onclick="rightClick()" id="right_button" class="waves-effect waves btn-flat right"><i class="material-icons left">arrow_forward</i></a></th>
                        </table>

                    </div>

                    <table class="">
                        <thead>
                        <tr>
                            <th>{{ trans('front.table_position') }}</th>
                            <th style="width: 30px"></th>
                            <th>{{ trans('front.table_club') }}</th>
                            <th>{{ trans('front.table_played') }}</th>
                            <th>{{ trans('front.table_wins') }}</th>
                            <th>{{ trans('front.table_draws') }}</th>
                            <th>{{ trans('front.table_loses') }}</th>
                            <th>{{ trans('front.table_goals_favor') }}</th>
                            <th>{{ trans('front.table_goals_against') }}</th>
                            <th>{{ trans('front.table_goal_difference') }}</th>
                            <th class="right">{{ trans('front.table_points') }}</th>
                        </tr>
                        </thead>

                        <tbody id="tbody">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    @else
    @endif

@endsection

@section('scripts')

    @if($competition->competition_type == 'league')
        @include('front.partial.competition_full_table_js')
    @endif

@endsection