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

$('.dropdown-button').dropdown({
        inDuration: 300,
        outDuration: 225,
        constrainWidth: false, // Does not change width of dropdown to that of the activator
        hover: true, // Activate on hover
        gutter: 0, // Spacing from edge
        belowOrigin: true, // Displays dropdown below the button
        alignment: 'left', // Displays dropdown with edge aligned to the left of button
        stopPropagation: true // Stops event propagation
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

function makeRequest(url, data, method, callback) {

    $.ajax({
        url: url,
        data: data,
        type: method,
        crossDomain: true,
        dataType: 'JSON',
        success: function(response) { callback(response); },
        error: function() { console.log('failed get request to ' + url) }
    });

}
