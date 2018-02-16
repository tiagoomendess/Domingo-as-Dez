<script>

    function updateSeasonList(id, element_id) {

        if (element_id == null)
            element_id = "#season_id";
        else
            element_id = "#" + element_id;

        var season_dropdown = $(element_id);
        season_dropdown.prop('disabled', true);
        season_dropdown.empty();

        //Disable 2nd dropdown
        if (id == 0) {

            var op = $("<option> Primeiro escolhe Clube</option>");
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