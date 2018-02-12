<script>

    function updateTeamList(id) {

        var team_dropdown = $("#team_id");
        team_dropdown.prop('disabled', true);
        team_dropdown.empty();


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
            }

            for (i = 0; i < data.length; i++) {

                var op = $("<option>" + data[i].name + "</option>");
                op.attr('value', data[i].id);
                op.appendTo(team_dropdown);
            }
        });
    }

</script>