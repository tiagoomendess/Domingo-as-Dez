$(document).ready(function(){

    $('.collapsible').collapsible();


    $('#rgpd_analytics_cookies_switch input').on('change', showSaveButton);
    $('#rgpd_user_data_collect_switch input').click(function (event) {
        showUserDataWarning();
    });
    $('#rgpd_all_data_collect_switch input').on('change', showSaveButton);
    
});

function showSaveButton() {

    var btn = $('.consents-submit-btn');
    btn.removeClass('hide');

}

function showUserDataWarning() {
    alert('Nao pode desligar isso.');
}
