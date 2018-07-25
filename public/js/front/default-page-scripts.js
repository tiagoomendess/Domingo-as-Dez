$(document).ready(function(){
    handleNavbarMobileTitle();
    $('#modal_logout').modal();
    
});

$('.button-collapse').sideNav({
        menuWidth: 300, // Default is 240
        edge: 'left', // Choose the horizontal origin
        closeOnClick: false, // Closes side-nav on <a> clicks, useful for Angular/Meteor
        draggable: true // Choose whether you can drag to open on touch screens
    }
);

function handleNavbarMobileTitle() {

    var title = $('title').text().trim();

    var nav_title = $('#navbar_title');

    if (!nav_title.text().trim())
        nav_title.text(title);

    nav_title.removeClass('hide');
}

function makeGetRequest(url, data, callback) {

    $.ajax({
        url: url,
        data: data,
        type: 'GET',
        crossDomain: true,
        dataType: 'JSON',
        success: function(response) { callback(response); },
        error: function() { console.log('failed get request to ' + url) }
    });

}
