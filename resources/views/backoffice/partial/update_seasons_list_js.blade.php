<script>

    function updateSeasonList(dropdown_origem, dropdown_destino) {

        console.log('Entrou em updateSeasonList');

        var season_dropdown = $('#' + dropdown_destino);
        season_dropdown.prop('disabled', true);
        season_dropdown.empty();

        var id = $('#' + dropdown_origem).val();

        //Disable 2nd dropdown
        if (id == 0 || id == null) {

            var op = $("<option> Primeiro escolhe Competição</option>");
            op.attr('value', '0');
            op.appendTo(season_dropdown);
            season_dropdown.prop('disabled', true);
            return;
        }

        $.get("/competitions/" + id + "/seasons", function (data) {

            season_dropdown.prop('disabled', false);

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

                if (data[i].start_year != data[i].end_year)
                    var op = $("<option>" + data[i].start_year + "/" + data[i].end_year + "</option>");
                else
                    var op = $("<option>" + data[i].start_year + "</option>");

                op.attr('value', data[i].id);
                op.appendTo(season_dropdown);
            }
        });
    }

</script>