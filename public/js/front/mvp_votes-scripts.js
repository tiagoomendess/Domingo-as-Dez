$(document).ready(function(){
    $('.modal').modal({
        ready: function() {
            $('#mpv_player_id').attr('value', '');
            $('#mvp_submit_btn').addClass('disabled');
        },
        complete: function() {
            $('.mvp_player').removeClass('mvp-selected');
        }
    });
});

$('.mvp_player').on('click', function () {
    $('.mvp_player').removeClass('mvp-selected');
    $(this).addClass('mvp-selected');

    var player_id_form = $('#mpv_player_id');
    player_id_form.attr('value', $(this).attr('data-content'));

    $('#mvp_submit_btn').removeClass('disabled');
});

$('#mvp_submit_btn').on('click', function () {
    $(this).addClass('disabled');
    $('#mvp_form').submit();
});
