<script>

    window.onload = firstLoad;

    function firstLoad() {

        var season_id = $('#season_id').val();
        var round = $('#round').val();
        var competition_slug = $('#competition_slug').val();

        console.log(season_id + ' - ' + round + ' ' + competition_slug);

        updateRoundInfo(competition_slug, season_id, round);

    }

    function updateRoundInfo(competition_slug, season, round) {

        var positions_table = $('#positions_table');
        
        $.get("/competicao/" + competition_slug + "/get_season_info/" + season + "/round/" + round, function (data) {

            console.log(data.table);

            for (i = 0; i < data.table.length; i++) {

                var tr = $("<tr></tr>");
                var td1 = $("<td>" + (i + 1) + "</td>");
                var td2 = $("<td>" + data.table[i]['club_name'] + "</td>");
                var td3 = $("<td>" + data.table[i]['points'] + "</td>");
                td3.addClass('right');

                tr.appendTo(positions_table);
                td1.appendTo(tr);
                td2.appendTo(tr);
                td3.appendTo(tr);


            }

        });

        console.log('Done');
    }

</script>