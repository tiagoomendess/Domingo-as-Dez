<script>

    //Mostra os jogadores presentes no jogo "game_dropdown". Primeiro os da equipa selecionada em dropdown_origem
    // e depois a outra equipa coorespondente ao jogo
    function updateGamePlayers(dropdown_origem, dropdown_destino, game_dropdown) {

        console.log('updateGamePlayers');

        var selected_team_drop = $('#' + dropdown_origem);
        var players_drop = $('#' + dropdown_destino);
        var game_drop = $('#' + game_dropdown);

        players_drop.prop('disabled', true);
        players_drop.empty();

        var game_id = game_drop.val();
        var selected_team_id = selected_team_drop.val();

        if(game_id == null || selected_team_id == null)
            return;

        var op = $("<option>Desconhecido</option>");
        op.attr('value', '');
        op.prop('selected', true);
        op.appendTo(players_drop);

        $.get("/teams/" + selected_team_id + "/current_players", function (players) {

            for (i = 0; i < players.length; i++) {

                if(players[i].nickname != null)
                    op = $("<option>" + players[i].name + "(" + players[i].nickname + ")</option>");
                else
                    op = $("<option>" + players[i].name + "</option>");

                op.attr('value', players[i].id);
                op.appendTo(players_drop);
            }
        });

        $.get("/games/" + game_id + "/teams", function (data) {

            if(data.length == 0) {
                console.log('No teams found');
                return;
            }

            for (i = 0; i < data.length; i++) {

                console.log('Dentro do for');

                if (data[i].id != selected_team_id) {

                    console.log('Dentro do if ' + i);

                    addOtherTeamPlayers(data[i].id, players_drop);
                }
            }

        });

        players_drop.prop('disabled', false);

    }

    function addOtherTeamPlayers(team_id, players_drop) {

        return; //Remendo, alterar mais tarde
        console.log("addOtherTeamPlayers");

        $.get("/teams/" + team_id + "/current_players", function (players) {

            console.log("addOtherTeamPlayers dentro do get");
            console.log(players);

            for (i = 0; i < players.length; i++) {

                if(players[i].nickname != null)
                    op = $("<option>" + players[i].name + "(" + players[i].nickname + ")</option>");
                else
                    op = $("<option>" + players[i].name + "</option>");

                op.attr('value', players[i].id);
                op.appendTo(players_drop);
            }
        });

    }
</script>