<script>

    function addReferee(){

        var allRefs = $('#game_referees');
        var rows = parseInt('0');

        allRefs.find('.row').each(function () {
            rows++;
        });

        var hidden = $('#game_referee_hidden');

        var newRef = hidden.clone();

        newRef.removeAttr('id');
        newRef.removeClass('hide');
        newRef.attr('id', 'ref_' + rows);

        newRef.find('#i_referee_id').attr('name', 'referees_id[]');
        newRef.find('#i_referee_id').attr('required', '');
        newRef.find('#i_referee_id').removeAttr('id');

        newRef.find('#i_type_id').attr('name', 'types_id[]');
        newRef.find('#i_type_id').attr('required', '');
        newRef.find('#i_type_id').removeAttr('id');

        newRef.find('a').attr('onclick', 'removeReferee(' + rows + ')');

        newRef.appendTo(allRefs);

    }
    
    function removeReferee(id) {

        var string = '#ref_' + id;

        var row = $(string);
        row.remove();
    }

</script>