$(document).ready(function(){
    console.log('Live Matches scripts loaded!');
    $('.modal').modal();
});

function start() {
    console.log('Start live matches');
    makeGetRequest('/api/games/live', {}, updateMatches);
    liveMatches();
}

let games = {};

function updateMatches(response) {
    console.log('Updating Matches...');
    var live_matches_element = $('#live_matches');
    live_matches_element.empty();

    if (response.data.length < 1) {
        live_matches_element.append($('<p class="center flow-text">' +
            'Não existem jogos a decorrer, vai ser redirecionado para página com todos os resultados de hoje. ' +
            'Clique <a href="/hoje">aqui</a> caso não seja redirecionado automaticamente</p>'))
        $('#live_matches_caption').attr('class', 'hide');
        // Redirect for today matches page
        setTimeout(function () {
            window.location.href = '/hoje';
        }, 2000)
    } else {
        $('#live_matches_caption').removeClass('hide');
    }

    games = {};

    for (var i = 0; i < response.data.length; i++) {

        var new_group = $('#group_template').clone();
        new_group.removeAttr('id');
        new_group.find('.header .left img').attr('src', response.data[i].competition_logo);
        new_group.find('.header .left span').text(response.data[i].competition_name);

        for (var j = 0; j < response.data[i].games.length; j++) {

            games[response.data[i].games[j].id] = response.data[i].games[j];

            var new_game = new_group.find('#match_template').clone();
            new_game.attr('onclick', `handleGameClick(${response.data[i].games[j].id})`);
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

function handleGameClick(game_id) {
    let game = games[game_id];
    let current_url = encodeURIComponent(window.location.href);

    $('#send_score_report_btn').attr('href', `/score-reports/${game_id}?returnTo=${current_url}`);
    $('#score_update_modal_home_team_img').attr('src', game.home_club_emblem);
    $('#score_update_modal_away_team_img').attr('src', game.away_club_emblem);

    $('#score_update_modal_desc').html(`O resultado de ${game.home_score}-${game.away_score} entre <a href="${game.game_link}">${game.home_club_name} e ${game.away_club_name}</a> não está correto?`);

    $('#score_update_modal').modal('open');
}

function liveMatches() {

    setInterval(function () {

        console.log('Updating matches...');
        makeGetRequest('/api/games/live', {}, updateMatches);

    }, 10000);
}

start();
