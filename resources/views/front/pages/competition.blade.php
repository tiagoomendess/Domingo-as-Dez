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

                </div>
            </div>

            <div class="col xs12 s12 m12 l6 xl6">
                <div class="card-panel">
                    <table>

                        <thead>

                            <th>{{ trans('front.table_position') }}</th>
                            <th>{{ trans('front.table_club') }}</th>
                            <th class="right">{{ trans('front.table_points') }}</th>

                        </thead>

                        <tr>
                            <td>1</td>
                            <td>Carapeços</td>
                            <td class="right">12</td>
                        </tr>
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