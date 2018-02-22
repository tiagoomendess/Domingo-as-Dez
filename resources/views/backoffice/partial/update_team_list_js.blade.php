<script>

    function updateTeamList(dropdown_origem, dropdown_destino) {

        console.log('Entrou em updateTeamList');

        if (dropdown_destino == null)
            element_id = "#team_id";
        else
            element_id = "#" + dropdown_destino;

        var selected = $('#' + dropdown_origem);

        var team_dropdown = $(element_id);
        team_dropdown.prop('disabled', true);
        team_dropdown.empty();

        var id = selected.val();

        console.log(id);

        //Disable 2nd dropdown
        if (id == 0) {

            var op = $("<option> Primeiro escolhe Clube</option>");
            op.attr('value', '0');
            op.appendTo(team_dropdown);
            team_dropdown.prop('disabled', true);
            return;
        }

        $.get("/clubs/" + id + "/teams", function (data) {

            team_dropdown.prop('disabled', false);

            if(data.length == 0) {
                var op = $("<option>Nenhuma</option>");
                op.attr('value', 0);
                op.appendTo(team_dropdown);
            } else {
                var op = $("<option>Escolha uma opção</option>");
                op.attr('value', 0);
                op.prop('disabled', true);
                op.prop('selected', true);
                op.appendTo(team_dropdown);
            }

            for (i = 0; i < data.length; i++) {

                var op = $("<option>" + data[i].name + "</option>");
                op.attr('value', data[i].id);
                op.appendTo(team_dropdown);
            }
        });
    }

</script>