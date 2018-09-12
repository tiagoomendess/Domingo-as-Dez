$(document).ready(function(){
    $('.parallax').parallax();
    start();
});

var finished;
var started;

function start() {

    var start_date = new Date(Date.parse($('#exact_time').text().trim()));
    start_date.setUTCHours(start_date.getHours(), start_date.getMinutes());
    var now = new Date();

    if (start_date > now)
        started = false;
    else
        started = true;

    if ($('#finished').hasClass('hide'))
        finished = false;
    else
        finished = true;

    if (!started) {
        countdown(start_date);
    }

    if (started && !finished)
        liveScore();

}

function countdown(countDownDate) {

    console.log("inicia countdown");

    var x = setInterval(function() {

        var now = new Date().getTime();
        var distance = countDownDate - now;

        var days = Math.floor(distance / (1000 * 60 * 60 * 24));
        var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        var seconds = Math.floor((distance % (1000 * 60)) / 1000);

        var countdown = $('#countdown');
        var table = countdown.find('table');

        table.find('.time td').eq(0).text(days > 9 ? days : '0' + days);
        table.find('.time td').eq(1).text(hours > 9 ? hours : '0' + hours);
        table.find('.time td').eq(2).text(minutes > 9 ? minutes : '0' + minutes);
        table.find('.time td').eq(3).text(seconds > 9 ? seconds : '0' + seconds);

        if (distance < 0) {
            clearInterval(x);
            countdown.addClass('hide');
            $('#score').removeClass('hide');
            liveScore();
        }
    }, 1000);
}

function liveScore() {

    var game_id = $('#game_id').text().trim();
    $('#score').addClass('live-score-animation');

    var x = setInterval(function () {

        console.log('Updating score...');
        makeGetRequest('/api/games/' + game_id, {}, updateGameInfo);

        if (finished)
            clearInterval(x);

    }, 10000);
}

function updateGameInfo(response) {

    var new_score = response.data.home_score + ' - ' + response.data.away_score;
    var old_score = $('#score').text().trim();
    finished = response.data.finished;
    console.log(response);

    if (new_score !== old_score) {
        $('#score').text(new_score);
    }

    if (finished) {

        if (response.data.penalties) {
            console.log('Entrou no if penalties');
            var gp = $('#penalties');
            gp.text(response.data.penalties);
            gp.removeClass('hide');
        }

        $('#finished').removeClass('hide');
        $('#score').removeClass('live-score-animation');
    }

}