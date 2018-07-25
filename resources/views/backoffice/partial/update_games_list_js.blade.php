<script>

    function updateGamesList(dropdown_origem, dropdown_destino) {

        console.log('Entrou em updateGamesList');

        var game_dropdown = $('#' + dropdown_destino);
        game_dropdown.prop('disabled', true);
        game_dropdown.empty();

        var id = $('#' + dropdown_origem).val();

        //Disable 2nd dropdown
        if (id == null || id == 0) {

            var op = $("<option> Primeiro escolhe Epoca</option>");
            op.attr('value', 0);
            op.appendTo(game_dropdown);
            game_dropdown.prop('disabled', true);
            return;

        }

        $.get("/gamegroups/" + id + "/games", function (data) {

            if(data.length == 0) {
                var op = $("<option>Nenhuma</option>");
                op.attr('value', 0);
                op.appendTo(game_dropdown);
            } else {
                var op = $("<option>Escolha uma opção</option>");
                op.attr('value', 0);
                op.prop('disabled', true);
                op.prop('selected', true);
                op.appendTo(game_dropdown);
            }

            for (i = 0; i < data.length; i++) {

                var op = $("<option>" + data[i].home_team.club.name + " vs " + data[i].away_team.club.name + "</option>");

                op.attr('value', data[i].id);
                op.appendTo(game_dropdown);
            }

            game_dropdown.prop('disabled', false);

        });
    }

</script>