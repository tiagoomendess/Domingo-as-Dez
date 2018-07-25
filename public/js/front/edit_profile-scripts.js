$(document).ready(function(){

    $("#user_data_form :input").on('click', function() {
        $('#save_btn').removeClass('hide');
    });

    $('#edit-profile-pic').click(function () {
        $('#edit-profile-pic-modal').modal('open');
    });

    $('#edit-profile-pic-modal').modal();

    $('#edit-profile-pic-btn').click(function () {
        $('#edit-profile-pic-modal').modal('close');
    });

    $('#edit-profile-pic-form').on('submit', function (event) {

        event.preventDefault();

        $('#progress-bar').addClass('determinate');
        $('#progress-bar').attr('style', 'width: 1%');
        $('#progress-bar').removeClass('hide');

        var formData = new FormData($('#edit-profile-pic-form')[0]);

        $.ajax({
            xhr : function () {
                var xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener('progress', function (ev) {

                    if (ev.lengthComputable) {

                        var percent = Math.round((ev.loaded / ev.total) * 100);

                        $('#progress-bar').attr('style', 'width: ' + percent + '%');
                    }
                });

                return xhr;
            },
            type : 'POST',
            data : formData,
            url : $('#edit-profile-pic-form').attr('action'),
            processData : false,
            contentType : false,
            success : function () {

                $('#progress-bar').removeClass('determinate');
                $('#progress-bar').addClass('indeterminate');

                var url = window.location.href;

                $.get(url, function (data) {

                    $('#edit-profile-pic div img').attr('src', $(data).find('#edit-profile-pic div img').attr('src').trim());
                    $('li .dropdown-button .navbar-profile-pic').attr('src', $(data).find('#edit-profile-pic div img').attr('src').trim());

                });

                $('#progress-bar').addClass('hide');
            },
            error : function () {
                $('#progress-bar').addClass('hide');
                alert('Erro ao carregar imagem. Perfil não foi alterado!');
            }
        });
    });

});