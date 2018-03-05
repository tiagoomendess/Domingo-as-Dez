<script>

    $(document).ready(function() {
        $('select').material_select();
    });

    window.onload = firstLoad;

    function rightClick() {

        var season_id = $('#season_id').val();
        var round = parseInt($('#round').val());
        var competition_slug = $('#competition_slug').val();
        var max_round = parseInt($('#max_round').val());

        if(round >= max_round) {

            return;
        }

        var round_name_element = $('#round_name');
        round_name_element.empty();

        $('<p class="center"> Jornada ' + (round + 1) +'</p>').appendTo(round_name_element);

        $('#round').attr('value', round + 1);

        updateRoundInfo(competition_slug, season_id, round + 1);

    }

    function leftClick() {

        var season_id = $('#season_id').val();
        var round = parseInt($('#round').val());
        var competition_slug = $('#competition_slug').val();

        if(round <= 1) {

            $("#left_button").prop('disabled', true);
            return;
        }

        var round_name_element = $('#round_name');
        round_name_element.empty();

        $('<p class="center"> Jornada ' + (round - 1) +'</p>').appendTo(round_name_element);

        $('#round').attr('value', round -1);

        updateRoundInfo(competition_slug, season_id, round - 1);

    }

    function firstLoad() {

        var season_id = $('#season_id').val();
        var round = parseInt($('#round').val());
        var competition_slug = $('#competition_slug').val();

        updateRoundInfo(competition_slug, season_id, round);

    }

    function seasonChange(){

        console.log('seasonChange');

        var select = $('#season_select');
        var season_id = $('#season_id');
        var max_round = $('#max_round');

        var new_id = select.val();
        season_id.attr('value', new_id);

        var round = parseInt($('#round').val());
        var competition_slug = $('#competition_slug').val();

        $.get("/api/season/" + new_id, function (response) {

            console.log(response);
            max_round.attr("value", response.data['max_rounds']);

        }).fail(function () {
            console.log("failled");
        });

        updateRoundInfo(competition_slug, new_id, round);
    }

    function updateRoundInfo(competition_slug, season, round) {

        var tbody = $("#tbody");
        var game_list = $("#game_list");

        $.get("/api/competicao/" + competition_slug + "/season/" + season + "/round/" + round + "/games", function (data) {

            game_list.empty();

            for (i = 0; i < data.length; i++) {

                var game_link = $("<a></a>");
                game_link.attr('href', '#');
                game_link.addClass('collection-item');
                game_link.appendTo(game_list);

                var game_table = $('<div></div>');
                game_table.appendTo(game_link);

                var table_row = $('<div class="row" style="margin-bottom: 0px;"></div>');
                table_row.appendTo(game_table);

                var td1 = $('<div class="col xs4 s4 m4 l4"></div>');
                td1.appendTo(table_row);
                var div1 = $('<div class="center"></div>');
                div1.appendTo(td1);

                var home_emblem = $('<img style="width: 50px;" src="' + data[i]['home_club_emblem'] + '"/>');
                home_emblem.appendTo( div1);

                var home_name = $('<div style="width: 100%">' + data[i]['home_club_name'] +'</div>');
                home_name.appendTo(div1);

                var td2 = $('<div class="col xs4 s4 m4 l4"></div>');
                td2.appendTo(table_row);
                var div2 = $('<div class="valign-wrapper"></div>');
                div2.appendTo(td2);

                if (data[i]['started'] && !data[i]['finished']) {

                    var inside_wrapper1 = $('<div style="width: 100%;" class="center"></div>');
                    inside_wrapper1.appendTo(div2);

                    var div_date1 = $('<div style="width: 100%" class=""><h5 class="center">' + data[i]['goals_home'] +' - ' + data[i]['goals_away'] +'</h5></div>');
                    div_date1.appendTo(inside_wrapper1);

                    var div_playground1 = $('<div style="width: 100%" class="center"><small>Não terminado</small></div>');
                    div_playground1.appendTo(inside_wrapper1);

                } else if(data[i]['finished']) {

                    var h3 = $('<h4 style="width: 100%" class="center">' + data[i]['goals_home'] +' - ' + data[i]['goals_away'] + '</h4>');
                    h3.appendTo(div2);

                } else {

                    var inside_wrapper = $('<div style="width: 100%;" class="center"></div>');
                    inside_wrapper.appendTo(div2);

                    var div_date = $('<div style="width: 100%; margin-top: 10px;" class=""><span class="center" style="margin-top: 20px;">' + data[i]['date'] +'</span></div>');
                    div_date.appendTo(inside_wrapper);

                    var div_playground = $('<div style="width: 100%" class="center"><small>' + data[i]['playground_name'] + '</small></div>');
                    div_playground.appendTo(inside_wrapper);

                }

                var td3 = $('<div class="col xs4 s4 m4 l4"></div>');
                td3.appendTo(table_row);
                var div3 = $('<div class="center"></div>');
                div3.appendTo(td3);

                var away_emblem = $('<img style="width: 50px;" src="' + data[i]['away_club_emblem'] + '"/>');
                away_emblem.appendTo(div3);

                var away_name = $('<div style="width: 100%">' + data[i]['away_club_name'] +'</div>');
                away_name.appendTo(div3);
            }

        }).fail(function () {
            game_list.empty();
            $('<p class="flow-text red-text center">Não foram encontrados nenhuns jogos</p>').appendTo(game_list);
        });

        $.get("/api/competicao/" + competition_slug + "/season/" + season + "/round/" + round + "/table", function (data) {

            tbody.empty();
            console.log(data);

            for (i = 0; i < data.length; i++) {

                var tr = $("<tr></tr>");
                var td1 = $("<td>" + (i + 1) + "</td>");

                var td2 = $("<td></td>");
                td2.attr('style', 'width: 30px');

                var emblem = $("<img>");
                emblem.addClass('table_emblem');
                emblem.attr('src', data[i].team.club['emblem']);
                emblem.appendTo(td2);

                var td3 = $("<td>" + data[i].team.club['name'] + "</td>");

                var td4 = $("<td>" + data[i]['gf'] + "</td>");

                var td5 = $("<td>" + data[i]['ga'] + "</td>");

                var td6 = $("<td>" + data[i]['gd'] + "</td>");

                var td7 = $("<td>" + data[i]['points'] + "</td>");
                td7.addClass('right');

                tr.appendTo(tbody);
                td1.appendTo(tr);
                td2.appendTo(tr);
                td3.appendTo(tr);
                td4.appendTo(tr);
                td5.appendTo(tr);
                td6.appendTo(tr);
                td7.appendTo(tr);

            }

        }).fail(function () {
            tbody.empty();
        });

    }
    


</script>