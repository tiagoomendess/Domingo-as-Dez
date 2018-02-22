<script>

    function updateGameTeams(dropdown_origem, dropdown_destino) {

        console.log('Entrou em updateGameTeams');

        var game_dropdown = $('#' + dropdown_origem);
        var game_teams_dropdown = $('#' + dropdown_destino);

        game_teams_dropdown.prop('disabled', true);
        game_teams_dropdown.empty();

        var game_id = game_dropdown.val();

        if( game_id == null || game_id == 0) {

            var op = $('<option>Escolha primeiro um jogo</option>');
            op.prop('disabled', true);
            op.prop('selected', true);
            op.appendTo(game_teams_dropdown);
            return;

        }

        $.get("/games/" + game_id + "/teams", function (data) {

            if(data.length == 0) {

                var op = $("<option>Nenhuma</option>");
                op.attr('value', 0);
                op.appendTo(game_teams_dropdown);

            } else {

                var op = $("<option>Escolha uma opção</option>");
                op.attr('value', 0);
                op.prop('disabled', true);
                op.prop('selected', true);
                op.appendTo(game_teams_dropdown);
            }

            for (i = 0; i < data.length; i++) {

                var op = $("<option>" + data[i].club.name + "</option>");

                op.attr('value', data[i].id);
                op.appendTo(game_teams_dropdown);
            }

            game_teams_dropdown.prop('disabled', false);

        });
    }
</script>