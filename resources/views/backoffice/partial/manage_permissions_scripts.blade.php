<script>

    $(document).ready(function () {
        buildPermissionsList();
    });

    function buildPermissionsList() {
        $.get("/users/get_permissions_json/" + "{{ $user->id }}", function (data) {

            console.log(data);

            var table = $('#permissions_table');

            table.empty();

            if (data.length < 1) {
                var m0 = $('<p>{{ trans('models.no_permissions') }}</p>');
                m0.appendTo(table);
            }

            for (var i = 0; i < data.length; i++) {

                var m1 = $("<tr></tr>");
                var m2 = $("<td>" + data[i].name + "</td>");

                m1.appendTo(table);
                m2.appendTo(m1);

                var m3 = $("<td></td>");
                m3.appendTo(m1);

                var m4 = $("<a></a>");
                m4.attr('href', '#');
                m4.attr('onclick', 'removePermission(' + data[i].id + ')');
                m4.appendTo(m3);

                var m5 = $("<i>delete</i>");
                m5.addClass("material-icons right");

                m5.appendTo(m4);
            }

        });
    }

    function removePermission(id) {

        $.post('/users/remove_permission', {
            permission_id: id,
            user_id: "{{ $user->id }}",
            _token: "{{ csrf_token() }}"
        }, function (callback) {

            buildPermissionsList();

        });
    }

    function addPermission() {

        var selected_id = $('#permissions_dropdown').val();

        $.post('/users/add_permission', {
            permission_id: selected_id,
            user_id: "{{ $user->id }}",
            _token: "{{ csrf_token() }}"
        }, function (callback) {

            buildPermissionsList();

        });
    }

</script>