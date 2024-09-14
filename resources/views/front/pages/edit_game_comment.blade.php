@extends('front.layouts.default-page')

@section('head-content')
    <title>Flash Interview</title>
@endsection

@section('content')
    <div class="container">
        <div class="row no-margin-bottom">
            <div class="col s12 hide-on-med-and-down">
                <h1>Flash Interview</h1>
            </div>
        </div>

        <div class="row no-margin-bottom">
            <div class="col s12">
                <p class="flow-text center">
                    {{ $game->game_group->season->competition->name }}
                </p>
            </div>
            <div class="col s6 text-right">
                <img class="right" style="width: 90px" src="{{ $game->home_team->club->getEmblem() }}"
                     alt="{{ $game->home_team->club->name }}">
            </div>
            <div class="col s6">
                <img style="width: 90px" src="{{ $game->away_team->club->getEmblem() }}"
                     alt="{{ $game->away_team->club->name }}">
            </div>
            <div class="col s12">
                <p class="flow-text center text-bold">
                    {{ $game->home_team->club->name }} {{ $game->goals_home }}
                    - {{ $game->goals_away }} {{ $game->away_team->club->name }}
                </p>
            </div>
        </div>

        <div class="row no-margin-bottom">
            <div class="col s12">
                <p class="flow-text no-margin-bottom">
                    O Domingo às Dez pede a colaboração do {{ $recipientClubName }} para recolher informações sobre o
                    jogo
                    realizado no dia {{ $gameDate }}.
                </p>
            </div>
        </div>

        @if(count($errors) > 0)
            <div class="row">
                <div class="col s12">
                    @include('backoffice.partial.form_errors')
                </div>
            </div>
        @endif

        <div class="row">
            <form method="POST" action="{{ route('front.game_comment_update', ['uuid' => $uuid]) }}">
                {{ csrf_field() }}

                <input type="hidden" name="pin" value="{{ $pin }}">

                @if($amountOfGoals > 0)
                    @include('front.partial.flash_interview_goals', [
                        'amountOfGoals' => $amountOfGoals,
                        'recipientClubName' => $recipientClubName,
                        'goals' => $goals,
                        'players' => $players,
                        'game' => $game,
                        'canEdit' => $canEdit
                    ])
                @endif

                <div class="col s12">
                    <p class="flow-text text-bold" style="margin-bottom: 10px">
                        Na opinião do {{ $recipientClubName }} como foi o jogo?
                    </p>
                </div>
                <div class="input-field col s12">
                        <textarea id="content" name="content" class="materialize-textarea validate"
                                  required @if(!$canEdit) disabled @endif data-length="1000">{{ $gameComment->content }}</textarea>
                    <label for="content">Comentário</label>
                </div>

                <div class="col s12" style="margin-bottom: 20px">
                    <a href="#help_modal" class="modal-trigger">Sem ideia do que escrever?</a>
                </div>

                @if($canEdit)
                    <div class="col s12">
                        <div class="col s12 text-center center" style="margin-bottom: 15px">
                            <button type="submit" class="btn-large waves-effect waves-light green">
                                Guardar
                            </button>
                        </div>

                        <p class="center text-gray" style="font-size: 10pt; color: grey">
                            Pode sempre voltar a esta página para fazer alterações até {{ $deadline }}.
                        </p>
                    </div>
                @else
                    <p class="center text-gray" style="font-size: 10pt; color: grey;">
                        Já não é possível fazer alterações, a data limite foi ultrapassada.
                    </p>
                @endif
            </form>
        </div>
    </div>

    <div id="help_modal" class="modal">
        <div class="modal-content">
            <p>Se não tem ideia do que falar, aqui tem alguns tópicos que pode abordar.</p>
            <ul>
                <li>- Como foram os golos?</li>
                <li>- O que correu bem?</li>
                <li>- O que correu mal ou menos bem?</li>
                <li>- O resultado foi justo?</li>
                <li>- No que vai trabalhar a equipa para enfrentar o próximo encontro?</li>
            </ul>
            <p>
                Desencorajamos a falar sobre arbitragem, mas se o fizer por favor seja respeitoso. Não sensuramos o
                texto, o que escrever será publicado, a menos que contenha ofensas e injurias a qualquer dos intervenientes, e
                não apenas aos elementos da equipa de arbitragem.
            </p>
        </div>
        <div class="modal-footer">
            <a href="javascript:void(0)" class="modal-action modal-close waves-effect waves-green btn-flat">Fechar</a>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Get the current URL
        const currentUrl = new URL(window.location.href);

        // Delete the 'pin' parameter from the URL search params
        currentUrl.searchParams.delete('pin');

        // Update the URL without reloading the page
        window.history.pushState({}, document.title, currentUrl.toString());

        setTimeout(function () {
            $(document).ready(function(){
                $('.modal').modal();
            });
        }, 10);
    </script>
@endsection
