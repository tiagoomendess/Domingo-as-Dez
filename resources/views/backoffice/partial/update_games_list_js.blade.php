<script>

    function updateGamesList(id, element_id) {

        if (element_id == null)
            element_id = "#game_id";
        else
            element_id = "#" + element_id;

        var game_dropdown = $(element_id);
        game_dropdown.prop('disabled', true);
        game_dropdown.empty();

        //Disable 2nd dropdown
        if (id == 0) {

            var op = $("<option> Primeiro escolhe Epoca</option>");
            op.attr('value', '0');
            op.appendTo(game_dropdown);
            game_dropdown.prop('disabled', true);
            return;
        }

        $.get("/seasons/" + id + "/games", function (data) {


            if(data.length == 0) {
                var op = $("<option>Nenhuma</option>");
                op.attr('value', 0);
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