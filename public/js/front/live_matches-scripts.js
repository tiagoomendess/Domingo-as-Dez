$(document).ready(function(){
    console.log('Live Matches scripts loaded!');
});

function start() {
    console.log('Start live matches');
    makeGetRequest('/api/games/live', {}, updateMatches);
    liveMatches();
}

function updateMatches(response) {

    console.log('Updating Matches...');
    var live_matches_element = $('#live_matches');
    live_matches_element.empty();

    if (response.data.length < 1) {
        live_matches_element.append($('<p class="center flow-text">Não existem jogos a decorrer</p>'))
        $('#live_matches_caption').attr('class', 'hide');
    } else {
        $('#live_matches_caption').removeClass('hide');
    }

    for (var i = 0; i < response.data.length; i++) {

        var new_group = $('#group_template').clone();
        new_group.removeAttr('id');
        new_group.find('.header .left img').attr('src', response.data[i].competition_logo);
        new_group.find('.header .left span').text(response.data[i].competition_name);

        for (var j = 0; j < response.data[i].games.length; j++) {

            var new_game = new_group.find('#match_template').clone();

            new_game.attr('href', response.data[i].games[j].game_link);
            new_game.find('.home-club .right span:eq(0)').text(response.data[i].games[j].home_club_name);
            new_game.find('.home-club .right span:eq(1)').text(response.data[i].games[j].home_club_name_small);
            new_game.find('.home-club .right img').attr('src', response.data[i].games[j].home_club_emblem);

            new_game.find('.separator .flow-text').text(response.data[i].games[j].home_score + ' - ' + response.data[i].games[j].away_score);

            new_game.find('.away-club .left span:eq(0)').text(response.data[i].games[j].away_club_name);
            new_game.find('.away-club .left span:eq(1)').text(response.data[i].games[j].away_club_name_small);
            new_game.find('.away-club .left img').attr('src', response.data[i].games[j].away_club_emblem);

            if (response.data[i].games[j].penalties) {
                new_game.find('.separator .penalties').text(response.data[i].games[j].penalties);
                new_game.find('.separator .penalties').removeClass('hide');
            }

            if (response.data[i].games[j].started) {
                new_game.find('.state').addClass('red');
            } else {
                new_game.find('.state').addClass('orange');
            }

            if (response.data[i].games[j].finished) {
                new_game.find('.state').addClass('blue');
            }

            new_game.removeAttr('id');
            new_game.removeClass('hide');
            new_game.appendTo(new_group.find('.matches'));
        }

        new_group.removeClass('hide');
        new_group.appendTo(live_matches_element);
    }

}

function liveMatches() {

    setInterval(function () {

        console.log('Updating matches...');
        makeGetRequest('/api/games/live', {}, updateMatches);

    }, 5000);

}

start();
