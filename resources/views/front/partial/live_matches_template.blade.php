<div id="live_matches_template">
    <div id="group_template" class="match-group hide">
        <div class="header">
            <div class="left">
                <img src="https://via.placeholder.com/50x50" alt="">
                <span class="flow-text text-darken-1">1ª Divisão AFPB</span>
            </div>
        </div>
        <div class="matches">
            <a href="#" id="match_template" class="hide">
                <div class="match">
                    <div class="state"></div>
                    <div class="home-club flow-text">
                        <div class="right">
                            <span class="hide-on-med-and-down"></span>
                            <span class="hide-on-large-only"></span>
                            <img src="" alt="">
                        </div>
                    </div>
                    <div class="separator">
                        <span class="flow-text"></span>
                        <span class="penalties hide"></span>
                    </div>
                    <div class="away-club flow-text">
                        <div class="left">
                            <img src="" alt="">
                            <span class="hide-on-med-and-down"></span>
                            <span class="hide-on-large-only"></span>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>

<div id="live_matches_caption" class="hide">
    <div class="item">
        <div class="color orange"></div>
        <span class="caption">{{ trans('front.warmup') }}</span>
    </div>

    <div class="item">
        <div class="color red"></div>
        <span class="caption">{{ trans('front.on_going') }}</span>
    </div>

    <div class="item">
        <div class="color blue"></div>
        <span class="caption">{{ trans('front.finished') }}</span>
    </div>
</div>

<!-- Modal Structure -->
<div id="score_update_modal" class="modal">
    <div class="modal-content" style="padding-bottom: 0">
        <h5 class="center">Resultado errado?</h5>
        <div class="row">
            <div class="col s12 center" style="margin-bottom: 20px">
                <div class="divider"></div>
            </div>
            <div class="col s6 center">
                <img style="width: 50%" id="score_update_modal_home_team_img" src="" alt="">
            </div>
            <div class="col s6 center">
                <img style="width: 50%" id="score_update_modal_away_team_img" src="" alt="">
            </div>
            <div class="col s12 center">
                <p id="score_update_modal_desc"></p>
            </div>
            <div class="col s12 center">
                <a style="margin-bottom: 10px" id="game_details_btn" href="#" class="waves-effect blue waves-light btn">Detalhe do jogo<i class="material-icons left">info</i></a>
                <a style="margin-bottom: 10px" id="send_score_report_btn" href="#" class="waves-effect green darken-3 waves-light btn">Enviar Resultado <i class="material-icons left">event</i></a>
            </div>
        </div>

        <div class="row no-margin-bottom">
            <div class="col s12 center">
                <div class="divider"></div>
            </div>
        </div>

    </div>
    <div class="modal-footer" style="">
        <a href="javascript:void(0)" class="modal-action modal-close waves-effect btn-flat">Fechar</a>
    </div>
</div>
