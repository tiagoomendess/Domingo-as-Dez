@extends('front.layouts.default-page')

@section('head-content')
    <title>Enviar Resultado</title>

    <meta property="og:title" content="Enviar Resultado"/>
    <meta property="og:type" content="website"/>
    <meta property="og:description" content="Envie o resultado do jogo que está a ver"/>
@endsection

@section('content')

    <div class="progress blue lighten-4 hide" id="loader_animation" style="margin: 0">
        <div class="indeterminate blue darken-1"></div>
    </div>

    <div class="container">
        <div class="col s12 hide-on-med-and-down">
            <h1>Enviar Resultado</h1>
        </div>

        <div class="row" style="margin-top: 20px">
            <div class="col s12 m8 l6 offset-m2 offset-l3">
                @if($errors)
                    <div class="row no-margin-bottom">
                        <div class="col xs12 s12 no-margin-bottom">
                            @include('front.partial.form_errors')
                        </div>
                    </div>
                @endif

                @if(!$game->allowScoreReports())
                    <blockquote>
                        <ul style="color: red;">
                            <li>Este jogo não está a aceitar resultados porque ainda não começou ou já terminou terminou
                                há muito tempo.
                            </li>
                        </ul>
                    </blockquote>
                @endif

                @if(!empty($ban) && !$ban->shadow_ban && !$ban->shadow_ban)
                        <blockquote>
                            <ul style="color: red;">
                                <li>
                                    Você não pode enviar resultados porque foi temporariamente bloqueado até
                                    {{  \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $ban->expires_at)->format("d/m/Y \à\s H:i") }} pela razão:
                                    {{ $ban->reason }}. Se acredita que isto é um erro, por favor contacte-nos pelos canais oficiais.
                                </li>
                            </ul>
                        </blockquote>
                @endif

                <form action="{{ route('score_reports.store', ['game' => $game]) }}" method="POST"
                      id="score_report_form">

                    <div class="row">
                        <div class="col s12">
                            <h2 class="center over-card-title">{{ $game->home_team->club->name }}
                                vs {{ $game->away_team->club->name }}</h2>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col s6 center">
                            <img width="80%" src="{{ $game->home_team->club->getEmblem() }}" alt="">
                        </div>
                        <div class="col s6 center">
                            <img width="80%" src="{{ $game->away_team->club->getEmblem() }}" alt="">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col s12">
                            <div style="margin: 0 10px" class="divider"></div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col s2">
                            <div class="col s12" style="margin-bottom: 10px">
                                <a style="padding: 0; width: 36px"
                                   class="@if(!$game->allowScoreReports() || !empty($ban) && !$ban->shadow_ban)disabled @endif() waves-effect waves-light btn green"
                                   onclick="handleHomeUp()">
                                    <i class="material-icons">arrow_upward</i>
                                </a>
                            </div>
                            <div class="col s12">
                                <a style="padding: 0; width: 36px"
                                   class="@if(!$game->allowScoreReports() || !empty($ban) && !$ban->shadow_ban)disabled @endif() waves-effect waves-light btn red"
                                   onclick="handleHomeDown()">
                                    <i class="material-icons">arrow_downward</i>
                                </a>
                            </div>
                        </div>

                        <div class="col s8 center">
                            <h1 id="score_title" class="center">{{ $game->getHomeScore() }}
                                - {{ $game->getAwayScore() }}</h1>
                        </div>

                        <div class="col s2">
                            <div class="col s12" style="margin-bottom: 10px">
                                <a style="padding: 0; width: 36px"
                                   class="@if(!$game->allowScoreReports() || !empty($ban) && !$ban->shadow_ban)disabled @endif() waves-effect waves-light btn green right"
                                   onclick="handleAwayUp()">
                                    <i class="material-icons">arrow_upward</i>
                                </a>
                            </div>
                            <div class="col s12">
                                <a style="padding: 0; width: 36px"
                                   class="@if(!$game->allowScoreReports() || !empty($ban) && !$ban->shadow_ban)disabled @endif() waves-effect waves-light btn red right"
                                   onclick="handleAwayDown()">
                                    <i class="material-icons">arrow_downward</i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col s12">
                            <div class="divider" style="margin: 0 10px"></div>
                        </div>
                    </div>

                    {{ csrf_field() }}

                    <input type="hidden" id="home_score" name="home_score" value="{{ $game->getHomeScore() }}">
                    <input type="hidden" id="away_score" name="away_score" value="{{ $game->getAwayScore() }}">
                    <input type="hidden" id="latitude" name="latitude" value="">
                    <input type="hidden" id="longitude" name="longitude" value="">
                    <input type="hidden" id="accuracy" name="accuracy" value="">
                    <input type="hidden" id="ip" name="ip" value="">
                    <input type="hidden" name="redirect_to" value="{{ $backUrl }}">

                    @if(!Auth::check())
                        <div class="row hide" id="captcha_row">
                            <small class="center grey-text">&nbsp;&nbsp;&nbsp;Faça login para não ter de resolver o
                                captcha</small>
                            <div class="col xs12 s12 center">
                                {!! Recaptcha::render() !!}
                            </div>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col s12">
                            <div class="col s6">
                                <a class="waves-effect waves-light btn @if($game->allowScoreReports() && empty($ban)) grey @else() green @endif() darken-1"
                                   href="{{ $backUrl }}">
                                    <i class="material-icons left">arrow_back</i>voltar</a>
                            </div>
                            <div class="col s6">
                                <a id="master_send" onclick="handleSendForm()"
                                   class="disabled waves-effect waves-light btn green darken-2 right">
                                    <i class="material-icons right">send</i>enviar</a>
                            </div>
                        </div>
                    </div>

                    @if($already_sent->count() > 0)
                        <div class="row">
                            <div class="col s12">
                                <div class="divider"></div>
                                <p class="center flow-text">Já enviou os seguintes resultados:</p>
                                <div style="display: flex; justify-content: center; align-items: center; flex-wrap: wrap">
                                    @foreach($already_sent as $report)
                                        <span class="score-badge">
                                            {{ $report->home_score }} - {{ $report->away_score }}
                                        </span>
                                    @endforeach
                                </div>
                                <div class="vertical-spacer"></div>
                                <div class="divider"></div>
                            </div>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col s12 center">
                            <small class="modal-trigger grey-text" style="text-decoration: underline; cursor: pointer"
                                   href="#modal_explain_1">Porquê a localização?</small>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col s12" style="text-align: justify">
                            <small class="grey-text">
                                Por favor não envie resultados falsos, os resultados não são diretamente atualizados sem antes serem analisados.
                                Para além de ser um comportamento infantil, poderá vir a ser automaticamente bloqueado
                                pelo sistema no fim do jogo, assim que a sequência de resultados for conhecida.
                                Valorize o futebol popular, seja um contribuidor positivo.
                            </small>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Structure -->
    <div id="modal_explain_1" class="modal bottom-sheet">
        <div class="modal-content" style="padding-bottom: 0">
            <div class="row">
                <div class="col s12 m10 l8 offset-m1 offset-l2">
                    <p class="flow-text"><b>Porque me pede a localização?</b></p>

                    <p style="text-align: justify">
                        Utilizamos a localização apenas no momento exato em que envia o resultado, para garantir que a
                        informação está a ser enviada por alguém que está a assistir ao jogo pessoalmente no campo, e
                        assim garantir uma melhor qualidade dos dados que recolhemos.
                    </p>

                    <p style="text-align: justify">
                        No entanto, se escolher não partilhar a sua localização, pode continuar a enviar resultados,
                        estes apenas terão um peso menor que os outros, pelo que recomendamos sempre que autorize o
                        envio da localização juntamente com o resultado.
                    </p>

                    <p style="text-align: justify">
                        O domingo às dez não faz monitorização ativa e constante da sua localização, apenas recebe a sua
                        localização no instante que envia o resultado. O código fonte do projeto é aberto e pode
                        visualiza-lo no <a target="_blank"
                                        href="https://github.com/tiagoomendess/Domingo-as-Dez">GitHub</a>,
                        e confirmar esta afirmação.
                    </p>
                </div>
            </div>
        </div>
        <div class="divider"></div>
        <div class="modal-footer">
            <a href="javascript:void(0)" class="modal-action modal-close waves-effect waves-green btn-flat">Ok</a>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        const scoreTitle = document.getElementById('score_title');
        const originalScore = scoreTitle.innerText
        let homeScore = 0
        let awayScore = 0
        let askedForLocation = false

        // on document ready, get the score from the DOM
        $(document).ready(function () {
            $('.modal').modal();
            homeScore = parseInt(scoreTitle.innerText.split(' - ')[0])
            awayScore = parseInt(scoreTitle.innerText.split(' - ')[1])

            setTimeout(() => {
                if (document.cookie.indexOf("ip=") < 0) {
                    makeGetRequest("https://api.my-ip.io/v1/ip", (ip) => {
                        if (ip) {
                            $('#ip').val(ip)
                            document.cookie = `ip=${ip};max-age=600;path=/`;
                        }
                    })
                } else {
                    $('#ip').val(document.cookie.split('ip=')[1].split(';')[0])
                }
            }, 2000)
        })

        const handleHomeUp = () => {
            if (homeScore > 31)
                return

            homeScore++
            scoreTitle.innerText = `${homeScore} - ${awayScore}`
            checkIfCanSend()
        }

        const handleHomeDown = () => {
            if (homeScore > 0) {
                homeScore--
                scoreTitle.innerText = `${homeScore} - ${awayScore}`
                checkIfCanSend()
            }
        }

        const handleAwayUp = () => {
            if (awayScore > 31)
                return

            awayScore++
            scoreTitle.innerText = `${homeScore} - ${awayScore}`
            checkIfCanSend()
        }

        const handleAwayDown = () => {
            if (awayScore > 0) {
                awayScore--
                scoreTitle.innerText = `${homeScore} - ${awayScore}`
                checkIfCanSend()
            }
        }

        const checkIfCanSend = () => {
            if (originalScore !== `${homeScore} - ${awayScore}`) {
                $('#master_send').removeClass('disabled')
                $('#captcha_row').removeClass('hide')
            } else {
                $('#master_send').addClass('disabled')
            }
        }

        const handleSendForm = () => {
            $('#master_send').addClass('disabled')
            $('#loader_animation').removeClass('hide')

            navigator.geolocation.getCurrentPosition((position) => {
                console.log("Got location: ", position);
                submitScoreReportForm(position.coords, position.coords.accuracy)
            }, (error) => {
                console.error("Could not get location: ", error)
                submitScoreReportForm(null, null)
            });
        }

        const submitScoreReportForm = (coords, accuracy) => {
            if (coords !== null) {
                $('#latitude').val(coords.latitude)
                $('#longitude').val(coords.longitude)
                $('#accuracy').val(accuracy)
            }

            $('#home_score').val(homeScore)
            $('#away_score').val(awayScore)

            $('#score_report_form').submit()
        }

        async function makeGetRequest(theUrl, callback) {
            let xmlHttp = new XMLHttpRequest();
            xmlHttp.onreadystatechange = function () {
                if (xmlHttp.readyState === 4) {
                    if (xmlHttp.status === 200)
                        callback(xmlHttp.responseText)
                    else
                        callback(null)
                }
            }
            xmlHttp.open("GET", theUrl, true);
            xmlHttp.send(null);
        }

    </script>
@endsection
