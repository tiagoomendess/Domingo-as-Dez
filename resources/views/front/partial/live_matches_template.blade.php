<div id="live_matches_template">
    <div id="group_template" class="match-group hide">
        <div class="header">
            <div class="left">
                <img src="http://via.placeholder.com/50x50" alt="">
                <span class="flow-text text-darken-1">1ª Divisão AFPB</span>
            </div>
        </div>
        <div class="matches">
            <a href="#" id="match_template" class="hide">
                <div class="match">
                    <div class="state"></div>
                    <div class="home-club flow-text">
                        <div class="right">
                            <span></span>
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
                            <span></span>
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