@extends('front.layouts.default-page')

@section('head-content')
    <title>{{ $competition->name }}</title>
@endsection

@section('content')

    @if($competition->competition_type == 'league')

        <div class="row">

            <div class="col xs12 s12 m12 l12 xl12">
                <h2>{{ $competition->name }}</h2>
            </div>

            <div class="col xs12 s12 m12 l6 xl6">
                <div class="card-panel">
                    <div class="row">

                        <input id="competition_slug" type="hidden" value="{{ str_slug($competition->name) }}">
                        <input id="season_id" type="hidden" value="{{ $season->id }}">
                        <input id="round" type="hidden" value="{{ $round_chosen }}">

                        <table id="round_table">
                            <thead>
                                <th><a class="waves-effect waves btn-flat left"> <i class="material-icons">arrow_back</i></a></th>
                                <th class="center">{{ trans('front.league_round') }}</th>
                                <th><a class="waves-effect waves btn-flat right"> <i class="material-icons">arrow_forward</i></a></th>
                            </thead>

                        </table>

                    </div>
                </div>
            </div>

            <div class="col xs12 s12 m12 l6 xl6">
                <div class="card-panel">
                    <table id="positions_table">

                        <thead>
                            <th>{{ trans('front.table_position') }}</th>
                            <th>{{ trans('front.table_club') }}</th>
                            <th class="right">{{ trans('front.table_points') }}</th>
                        </thead>

                        <tbody id="tbody">

                        </tbody>

                    </table>
                </div>
            </div>
        </div>



    @elseif ($competition->competition_type == 'cup')

        <p>Ainda não existe nenhuma representaçlão gráfica para o tipo de competição Taça</p>

    @elseif ($competition->competition_type == 'friendly')

        <p>Ainda não existe nenhuma representaçlão gráfica para o tipo de competição Amigavel</p>

    @elseif ($competition->competition_type == 'tournament')

        <p>Ainda não existe nenhuma representaçlão gráfica para o tipo de competição Torneio</p>

    @endif

@endsection

@section('scripts')

    @if($competition->competition_type == 'league')
        @include('front.partial.competition_league_js')
    @endif

@endsection