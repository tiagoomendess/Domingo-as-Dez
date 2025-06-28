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
        if (id == 0 || id == '' || id == null) {

            var op = $("<option>{{ trans('general.none') }}</option>");
            op.attr('value', '');
            op.appendTo(team_dropdown);
            team_dropdown.prop('disabled', false);
            return;
        }

        $.get("/clubs/" + id + "/teams", function (data) {

            team_dropdown.prop('disabled', false);

            // Always add None option first
            var noneOp = $("<option>{{ trans('general.none') }}</option>");
            noneOp.attr('value', '');
            noneOp.appendTo(team_dropdown);

            if(data.length == 0) {
                // If no teams, None is the only option
                noneOp.prop('selected', true);
            } else {
                // If there are teams, add choose option
                var chooseOp = $("<option>{{ trans('general.choose_option') }}</option>");
                chooseOp.attr('value', '');
                chooseOp.prop('disabled', true);
                chooseOp.appendTo(team_dropdown);
            }

            for (i = 0; i < data.length; i++) {

                var op = $("<option>" + data[i].name + "</option>");
                op.attr('value', data[i].id);
                op.appendTo(team_dropdown);
            }
        });
    }

</script>