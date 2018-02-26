<script>

    window.onload = firstLoad;

    function rightClick() {

        var season_id = $('#season_id').val();
        var round = parseInt($('#round').val());
        var competition_slug = $('#competition_slug').val();
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

        $('#round').attr('value', round -1);

        updateRoundInfo(competition_slug, season_id, round - 1);

    }

    function firstLoad() {

        var season_id = $('#season_id').val();
        var round = parseInt($('#round').val());
        var competition_slug = $('#competition_slug').val();

        updateRoundInfo(competition_slug, season_id, round);

    }

    function updateRoundInfo(competition_slug, season, round) {

        var tbody = $("#tbody");

        console.log(round);


        $.get("/competicao/" + competition_slug + "/get_season_info/" + season + "/round/" + round, function (data) {

            tbody.empty();
            console.log(data);

            for (i = 0; i < data.table.length; i++) {

                var tr = $("<tr></tr>");
                var td1 = $("<td>" + (i + 1) + "</td>");

                var td2 = $("<td></td>");
                td2.attr('style', 'width: 30px');

                var emblem = $("<img>");
                emblem.addClass('table_emblem');
                emblem.attr('src', data.table[i].team.club['emblem']);
                emblem.appendTo(td2);

                var td3 = $("<td>" + data.table[i].team.club['name'] + "</td>");

                var td4 = $("<td>" + data.table[i]['gf'] + "</td>");

                var td5 = $("<td>" + data.table[i]['ga'] + "</td>");

                var td6 = $("<td>" + data.table[i]['gd'] + "</td>");

                var td7 = $("<td>" + data.table[i]['points'] + "</td>");
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

            for (i = 0; i < data.matches.length; i++) {

                var home__name = $('#home_club');
            }

        });

    }

</script>