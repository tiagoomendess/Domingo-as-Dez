@extends('front.layouts.default-page')

@section('head-content')
    <title>{{ $competition->name }}</title>
@endsection

@section('content')

    <div class="row">

        <div class="col xs12 s12 m10 l10 xl10 hide-on-med-and-down">
            <h1>{{ $competition->name }}</h1>
        </div>

        <div class="col xs12 s12 m12 l2 xl2">

            <div id="season_select_div">
                <div class="input-field">

                    <select id="season_select" onchange="seasonChange()">

                        <option value="{{ $season->id }}" selected>@if($season->start_year != $season->end_year){{$season->start_year}}/{{ $season->end_year }}@else{{$season->start_year}}@endif</option>

                        @foreach($competition->seasons as $s)

                            @if($s->id != $season->id)
                                <option value="{{ $s->id }}">@if($s->start_year != $s->end_year){{$s->start_year}}/{{ $s->end_year }}@else{{$s->start_year}}@endif</option>
                            @endif

                        @endforeach
                    </select>
                    <label>{{ trans('models.season') }}</label>
                </div>
            </div>


        </div>
    </div>

    @if($competition->competition_type == 'league')

        <input id="competition_slug" type="hidden" value="{{ str_slug($competition->name) }}">
        <input id="season_id" type="hidden" value="{{ $season->id }}">
        <input id="round" type="hidden" value="{{ $round_chosen }}">
        <input id="max_round" type="hidden" value="{{ $season->getTotalRounds() }}">

        <div class="row">

            <div class="col xs12 s12 m12 l6 xl6">
                <div class="card-panel">

                    <div class="row">

                        <table>
                            <th><a onclick="leftClick()" id="left_button" class="waves-effect waves btn-flat"><i class="material-icons left">arrow_back</i></a></th>
                            <th id="round_name">
                                <p class="center">{{ trans('front.league_round') }} {{ $round_chosen }}</p>
                            </th>
                            <th><a onclick="rightClick()" id="right_button" class="waves-effect waves btn-flat right"><i class="material-icons left">arrow_forward</i></a></th>
                        </table>

                    </div>

                    <div id="game_list" class="collection">
                        @for($i = 0; $i < $season->getTotalTeams() / 2; $i++)
                            <a href="#" class="collection-item">
                                <div>
                                    <div class="row" style="margin-bottom: 0px;">

                                        <div class="col xs4 s4 m4 l4">

                                            <div class="center">
                                                <div style="width: 100%">
                                                    <div class="linear-background center" style="height: 50px; width: 50px; margin-bottom: 10px;"></div>
                                                </div>

                                                <div class="linear-background" style="height: 14px"></div>
                                            </div>
                                        </div>

                                        <div class="col xs4 s4 m4 l4">
                                            <div class="valign-wrapper">
                                                <div style="width: 100%">
                                                    <div class="linear-background" style="height: 14px"></div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col xs4 s4 m4 l4">
                                            <div class="center">
                                                <div class="linear-background" style="height: 50px; width: 50px; margin-bottom: 10px;"></div>
                                                <div class="linear-background" style="height: 14px"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @endfor
                    </div>

                </div>
            </div>

            <div class="col xs12 s12 m12 l6 xl6">
                <div class="card-panel">
                    <table>

                        <thead>
                        <th>{{ trans('front.table_position') }}</th>
                        <th style="width: 30px"></th>
                        <th>{{ trans('front.table_club') }}</th>
                        <th>{{ trans('front.table_goals_favor') }}</th>
                        <th>{{ trans('front.table_goals_against') }}</th>
                        <th>{{ trans('front.table_goal_difference') }}</th>
                        <th class="right">{{ trans('front.table_points') }}</th>
                        </thead>

                        <tbody id="tbody">

                        @for($i = 0; $i < $season->getTotalTeams(); $i++)
                            <tr>
                                <td style="height: 61px">{{ $i + 1}}</td>
                                <td style="height: 61px; width: 30px"><div class="linear-background" style="height: 25px; width: 25px"></div></td>
                                <td style="height: 61px"><div class="linear-background" style="height: 14px"></div></td>
                                <td style="height: 61px"><div class="linear-background" style="height: 14px"></div></td>
                                <td style="height: 61px"><div class="linear-background" style="height: 14px"></div></td>
                                <td style="height: 61px"><div class="linear-background" style="height: 14px"></div></td>
                                <td style="height: 61px"><div class="linear-background" style="height: 14px"></div></td>
                            </tr>
                        @endfor

                        </tbody>

                    </table>
                </div>

                <div class="row">

                    <div class="col s12 m12 l8">
                        <small>Nota: Esta tabela está ordenada pelos criterios de desempate.</small>
                    </div>

                    <div class="col s12 m12 l4">
                        <a class="right" href="{{ route('competition.detailed_table', ['slug' => str_slug($competition->name)]) }}">{{ trans('front.detailed_table') }}</a>
                    </div>

                </div>


            </div>
        </div>


    @elseif ($competition->competition_type == 'cup')

        <p class="flow-text">Ainda não existe nenhuma representação gráfica para o tipo de competição Taça</p>

    @elseif ($competition->competition_type == 'friendly')

        <p class="flow-text">Ainda não existe nenhuma representação gráfica para o tipo de competição Amigavel</p>

    @elseif ($competition->competition_type == 'tournament')

        <p class="flow-text">Ainda não existe nenhuma representação gráfica para o tipo de competição Torneio</p>

    @endif

@endsection

@section('scripts')

    @if($competition->competition_type == 'league')
        @include('front.partial.competition_league_js')
    @endif

@endsection