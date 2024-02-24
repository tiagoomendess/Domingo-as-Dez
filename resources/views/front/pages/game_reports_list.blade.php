@extends('front.layouts.default-page')

@section('head-content')
    <title>Resultados Enviados</title>

    <style>
        .image-header {
            margin-top: 10px;
            background-image: url({{ $game->playground->getPicture() }});
            background-size: cover;
            width: 100%;
            padding: 20px 0 10px 0;
        }
    </style>
@endsection

@section('content')
    <div class="container">
        <div class="row no-margin-bottom hide-on-med-and-down">
            <div class="col s12">
                <h1>Resultados Enviados</h1>
            </div>
        </div>

        <div class="row no-margin-bottom">
            <div class="col s12 m12">
                <div class="image-header">
                    <div class="row no-margin-bottom">
                        <div class="col s6 m3 offset-m3 center">
                            <img src="{{ $game->home_team->club->getEmblem() }}" alt="" style="width: 70%">
                        </div>
                        <div class="col s6 m3 center">
                            <img src="{{ $game->away_team->club->getEmblem() }}" alt="" style="width: 70%">
                        </div>
                    </div>
                    <div class="row no-margin-bottom">
                        <div class="col s12 center">
                            <span class="card-title white-text flow-text">{{ $game->home_team->club->name }} vs {{ $game->away_team->club->name }}</span>
                        </div>
                        <div class="col s12 center white-text flow-text">
                            <span style="font-weight: 800; font-size: 20pt">{{ $game->getHomeScore() }} - {{ $game->getAwayScore() }}</span>
                        </div>
                    </div>
                </div>

                <div class="card" style="margin: 0 0 10px 0">
                    <div class="card-content" style="padding: 10px 7px">
                        @if($reports->count() == 0)
                            <div class="row">
                                <div class="col s12 center">
                                    <span class="flow-text">Ainda nenhum resultado foi enviado</span>
                                </div>
                            </div>
                        @else
                            <ul style="margin: 0">
                                <div class="divider"></div>
                                @foreach($reports as $report)
                                    <li>
                                        <div class="row" style="margin: 5px 0">
                                            <div class="col s3">
                                                <div style="padding-top: 5px">
                                                <span style="font-weight: 200;">
                                                     {{ \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $report->created_at)->timezone('Europe/Lisbon')->format("H:i") }}
                                                </span>
                                                </div>
                                            </div>
                                            <div class="col s3 center">
                                                <div style="font-weight: 800; color: white; padding: 5px; background-color: @if($report->finished) #a50303 @else grey @endif">
                                                <span>
                                                    {{ $report->home_score }} - {{ $report->away_score }}
                                                </span>
                                                </div>
                                            </div>
                                            <div class="col s6">
                                                <div style="padding-top: 7px" class="left">
                                                    @if($report->user_id)
                                                        <i class="material-icons grey-text text-darken-3">person</i>
                                                    @else
                                                        <i class="material-icons grey-text text-lighten-1">person_outline</i>
                                                    @endif
                                                    @if(!empty($report->location))
                                                        @if($report->location_accuracy < 50)
                                                            <i class="material-icons green-text text-darken-3">location_on</i>
                                                        @elseif($report->location_accuracy < 100)
                                                            <i class="material-icons green-text text-darken-1">location_on</i>
                                                        @elseif($report->location_accuracy < 250)
                                                            <i class="material-icons yellow-text text-darken-2">location_on</i>
                                                        @elseif($report->location_accuracy < 600)
                                                            <i class="material-icons orange-text text-darken-1">location_on</i>
                                                        @else
                                                            <i class="material-icons red-text">location_on</i>
                                                        @endif
                                                    @else
                                                        <i class="material-icons grey-text">location_off</i>
                                                    @endif

                                                    @switch($report->source)
                                                        @case('website')
                                                            <i class="material-icons grey-text text-darken-3">phone_iphone</i>
                                                            @break
                                                        @case('api_afpb_crawler')
                                                            <i class="material-icons grey-text text-darken-3">bug_report</i>
                                                            @break
                                                        @case('placard')
                                                            <i class="material-icons grey-text text-darken-3">tv</i>
                                                            @break
                                                    @endswitch
                                                </div>

                                                <a href="#modal_report_{{ $report->id }}"
                                                   class="waves-effect waves-light btn blue lighten-1 right modal-trigger"
                                                   style="padding: 0; width: 36px">
                                                    <i class="material-icons">remove_red_eye</i>
                                                </a>
                                            </div>
                                        </div>
                                    </li>
                                    <div class="divider"></div>

                                    <div id="modal_report_{{ $report->id }}" class="modal" style="width: 90%; max-height: 90%!important;">
                                        <div class="modal-content" style="padding: 15px">
                                            <p class="text-bold" style="font-size: 20pt; margin-bottom: 10px;">
                                                Informação {{ $report->id }}</p>
                                            <div class="divider"></div>

                                            <table class="bordered">
                                                <tbody>
                                                <tr>
                                                    <td>
                                                        <i class="material-icons grey-text text-darken-3">person</i>
                                                    </td>
                                                    <td>
                                                        @if($report->user_id)
                                                            <a href="{{ route('users.show', ['user' => $report->user]) }}">{{ $report->user->name }}</a>
                                                            &nbsp;({{ $report->user_id }})
                                                        @else
                                                            Não
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <i class="material-icons grey-text text-darken-3">location_on</i>
                                                    </td>
                                                    <td>
                                                        @if(!empty($report->location))
                                                            <a target="_blank"
                                                               href="{{ $report->getGoogleMapsLink() }}">Sim, precisão
                                                                de {{ (int) $report->location_accuracy }}m</a>
                                                        @else
                                                            Não
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <i class="material-icons grey-text text-darken-3">signal_wifi_4_bar</i>
                                                    </td>
                                                    <td>
                                                        {{ $report->ip_address ?? 'Sem IP' }} - {{ $report->ip_country ?? 'Sem País' }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><i class="material-icons grey-text text-darken-3">system_update_alt</i>
                                                    </td>
                                                    <td>{{ $report->source }}</td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <i class="material-icons grey-text text-darken-3">phone_iphone</i>
                                                    </td>
                                                    <td>{{ $report->user_agent ?? 'Desconhecido' }}</td>
                                                </tr>

                                                <tr>
                                                    <td>
                                                        <i class="material-icons grey-text text-darken-3">assistant_photo</i>
                                                    </td>
                                                    <td>
                                                        {{ $report->uuid ?? 'Sem UUID' }}
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>

                                            @if($report->source == "website")
                                            <div>
                                                <form action="{{ route('score_reports.update_is_fake', ['report' => $report]) }}" method="POST" style="width: 100%; display: flex; align-items: center; justify-content: space-between; flex-direction: row; padding: 10px 0">
                                                    <div>
                                                        {{ csrf_field() }}
                                                        {{ method_field('PUT') }}
                                                        <p onclick="fakeCheckboxClicked({{ $report->id }})">
                                                            <input type="checkbox" name="is_fake" class="filled-in checkbox-blue" id="is_fake_{{ $report->id }}" @if($report->is_fake)checked="checked"@endif />
                                                            <label for="is_fake_{{ $report->id }}">Resultado Falso</label>
                                                        </p>
                                                    </div>
                                                    <div>
                                                        <button id="is_fake_submit_button_{{ $report->id }}"
                                                                disabled type="submit"
                                                                style="padding: 0; width: 36px"
                                                                class="waves-effect waves-light btn blue center">
                                                            <i class="material-icons">send</i>
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                            @endif
                                            <div class="divider"></div>
                                        </div>
                                        <div class="modal-footer">
                                            <a href="javascript:void(0)"
                                               class="modal-action modal-close waves-effect btn-flat">Fechar</a>
                                        </div>
                                    </div>

                                @endforeach()
                            </ul>
                        @endif
                    </div>
                </div>

                <div class="row no-margin-bottom">
                    <div class="col s12">
                        <a href="{{ $backUrl }}" class="btn waves-effect waves-light grey darken-1">
                            <i class="material-icons left">chevron_left</i> Voltar
                        </a>
                    </div>
                </div>

                <div class="row">
                    <div class="col s12">
                        <div class="card">
                            <div class="card-content">
                                <p class="flow-text">Legenda:</p>

                                <table class="table table-condensed">
                                    <tr>
                                        <td><i class="material-icons grey-text text-darken-3">person</i></td>
                                        <td>Utilizador tinha login feito</td>
                                    </tr>
                                    <tr>
                                        <td><i class="material-icons grey-text text-lighten-1">person_outline</i></td>
                                        <td>Utilizador não tinha login feito</td>
                                    </tr>
                                    <tr>
                                        <td><i class="material-icons green-text text-darken-3">location_on</i></td>
                                        <td>Localização partilhada, cores indicam a precisão</td>
                                    </tr>
                                    <tr>
                                        <td><i class="material-icons grey-text">location_off</i></td>
                                        <td>Localização não partilhada</td>
                                    </tr>
                                    <tr>
                                        <td><i class="material-icons grey-text text-darken-3">phone_iphone</i></td>
                                        <td>Informação enviada pelo site</td>
                                    </tr>
                                    <tr>
                                        <td><i class="material-icons grey-text text-darken-3">bug_report</i></td>
                                        <td>Informação retirada do site AFPB automaticamente</td>
                                    </tr>
                                    <tr>
                                        <td><i class="material-icons grey-text text-darken-3">tv</i></td>
                                        <td>Informação vinda do placard</td>
                                    </tr>

                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            // the "href" attribute of the modal trigger must specify the modal ID that wants to be triggered
            $('.modal').modal();
        });

        const fakeCheckboxClicked = (reportId) => {
            // enable submit button
            const submitButton = document.getElementById('is_fake_submit_button_' + reportId);
            submitButton.disabled = false;
        }

    </script>
@endsection
