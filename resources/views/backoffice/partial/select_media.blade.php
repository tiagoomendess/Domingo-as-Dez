<div id="select_media" class="modal modal-fixed-footer">
    <div class="modal-content">
        <h4 class="center">{{ trans('general.select') }} {{ trans('models.media') }}</h4>

        <div class="row">
            <div class="col s12">
                <div class="row">
                    <div class="input-field inline">
                        <input name="tags" id="tags" type="text" class="validate">
                        <label for="tags">{{ trans('general.tags') }}</label>
                    </div>

                    <div class="input-field inline">

                        <a id="search_media" class="btn waves-effect waves-light">{{ trans('general.search') }}</a>

                    </div>
                </div>
            </div>
        </div>

        <div class="row" id="media_list">

        </div>

    </div>

    <div class="modal-footer">
        <a href="#" class="modal-action modal-close waves-effect waves-green btn-flat ">{{ trans('general.close') }}</a>
    </div>

</div>

<script>
    $(function () {

        $('#search_media').on('click', function () {

            var tags = $('#tags').val();

            $.post('/media_query', { tags: tags }, function (callback) {

                buildList(callback.response);

            })
        });
    });

    function buildList(response) {

        var media_list = $('#media_list');

        media_list.empty();

        for (var i = 0; i < response.total; i++) {

            var m1 = $('<div></div>');
            m1.addClass('col s4 l3');
            m1.attr('id', 'm_1_' + i);
            m1.appendTo('#media_list');

            var m2 = $('<a></a>');
            m2.addClass('modal-close');
            m2.attr('id', 'm_2_' + i);
            m2.attr('href', '#');
            m2.attr('onclick', 'setSelectedMediaId(' + response.data[i].id + ')');
            m2.appendTo(m1);

            var m3 = $('<div></div>');
            m3.addClass('media_square_box');
            m3.attr('id', 'm_3_' + i);
            m3.addClass('hoverable');
            m3.appendTo(m2);

            var m4 = $('<div></div>');
            m4.addClass('media_square_box_content');
            m4.attr('id', 'm_4_' + i);
            m4.appendTo(m3);

            if (response.data[i].media_type == 'image') {
                var m5 = $('<img/>');
                m5.attr('id', 'm_5_' + i);
                m5.attr('src', response.data[i].url);
                m5.appendTo(m4);
            } else {
                var m5 = $('<img/>');
                m5.attr('id', 'm_5_' + i);
                m5.attr('src', 'http://placehold.it/300x300');
                m5.appendTo(m4);
            }

            var m6 = $('<p>' + response.data[i].media_type + '<br>' + response.data[i].tags + '</p>');
            m6.attr('id', 'm_6_' + i);
            m6.addClass('truncate center');
            m6.appendTo(m4);

        }
    }

    function setSelectedMediaId(id) {
        $('#selected_media_id').attr('value', id);
        Materialize.updateTextFields();
    }
</script>