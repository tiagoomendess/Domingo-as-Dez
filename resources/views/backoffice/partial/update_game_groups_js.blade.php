<script>

    function updateGameGroupsList(dropdown_origem, dropdown_destino) {

        console.log('Entrou em updateGameGroupsList');

        var season_dropdown = $('#' + dropdown_destino);
        season_dropdown.prop('disabled', true);
        season_dropdown.empty();

        var id = $('#' + dropdown_origem).val();

        //Disable 2nd dropdown
        if (id == 0 || id == null) {

            var op = $("<option> Primeiro escolha Época</option>");
            op.attr('value', '0');
            op.appendTo(season_dropdown);
            season_dropdown.prop('disabled', true);
            return;
        }

        $.get("/seasons/" + id + "/game_groups", function (data) {

            season_dropdown.prop('disabled', false);
            console.log(data);

            if(data.length == 0) {
                var op = $("<option>Nenhuma</option>");
                op.attr('value', 0);
                op.appendTo(season_dropdown);
            } else {
                var op = $("<option>Escolha uma opção</option>");
                op.attr('value', 0);
                //op.prop('disabled', true);
                op.prop('selected', true);
                op.appendTo(season_dropdown);
            }

            for (i = 0; i < data.length; i++) {


                var op = $("<option>" + data[i].name + "</option>");

                op.attr('value', data[i].id);
                op.appendTo(season_dropdown);
            }
        });
    }

</script>