$(function () {
    $('#competition_selector').on('change', competitionChanged);
    $('#season_selector').on('change', seasonChanged);

    start();
});

var data;

function leftBtnClicked(event) {

    var src = $(event.srcElement);
    var card = src.parents().eq(3);
    var group = src.parents().eq(7).attr('group');
    var active = card.find('.group-games .active');
    var previous = active.prev();

    if (!previous.attr('round'))
        return;

    showTable(group, previous.attr('round'));

    active.addClass('hide');
    active.removeClass('active');
    previous.removeClass('hide');
    previous.addClass('active');

    var round_name = card.find('.round-title .round-name');
    var newName = round_name.text().trim();
    newName = newName.replace(/[0-9]+/, parseInt(previous.attr('round')));
    round_name.text(newName);

}

function rightBtnClicked(event) {

    var src = $(event.srcElement);
    var group = src.parents().eq(7).attr('group');
    var card = src.parents().eq(3);
    var active = card.find('.group-games .active');
    var next = active.next();

    if (!next.attr('round'))
        return;

    showTable(group, next.attr('round'));

    active.addClass('hide');
    active.removeClass('active');
    next.removeClass('hide');
    next.addClass('active');

    var round_name = card.find('.round-title .round-name');
    var newName = round_name.text().trim();
    newName = newName.replace(/[0-9]+/, parseInt(next.attr('round')));
    round_name.text(newName);
}

function start() {
    makeGetRequest('/api/competitions', {}, addCompetitionSelectorOptions);
    $('select').material_select();
}

function seasonChanged() {
    $('#stats_button').addClass('hide');
    var season_selector = $('#season_selector');
    var season_id = parseInt(season_selector.val());

    $('#groups').empty();
    $('#main_loading').removeClass('hide');
    $('#season_obs > span').addClass('hide');
    $('#season_obs_' + season_id).removeClass('hide');

    makeGetRequest('/api/seasons/' + season_id + '/games', {}, handleGetGamesRequest);
}

function competitionChanged() {
    var competition_selector = $('#competition_selector');
    var id_selected = parseInt(competition_selector.val());

    var url = document.URL;
    var new_title = undefined;
    var slug = undefined;

    competition_selector.find('option').each(function () {
        if (this.selected) {
            new_title = $(this).text();
            slug = $(this).attr('slug');
        }
    });

    document.title = new_title;

    url = url.substring(0, url.lastIndexOf('/') + 1);
    url += slug;

    history.pushState({
        id: 'competitions'
    }, new_title, url);

    updateSeasonsList(id_selected);
}

function updateSeasonsList(competition_id) {
    makeGetRequest('/api/competitions/' + competition_id + '/seasons', {}, addSeasonsToOptions);
}

function addSeasonsToOptions(response) {

    var season_selector = $('#season_selector');
    season_selector.empty();

    for(var i = 0; i < response.length; i++) {

        var new_option = $('<option>');
        new_option.attr('value', response[i].id);
        new_option.text(response[i].name);
        if (i === 0)
            new_option.selected = true;

        $('#season_obs').append(`<span id="season_obs_${response[i].id}" class="small hide">${response[i].obs ?? ''}</span>`)

        new_option.appendTo(season_selector);
    }

    $('select').material_select();
    season_selector.trigger('change');

}

function addCompetitionSelectorOptions(response) {

    var competition_selector = $('#competition_selector');
    var id_selected = parseInt(competition_selector.val());
    updateSeasonsList(id_selected);

    for (var i = 0; i < response.competitions.length; i++) {

        if (response.competitions[i].id !== id_selected) {
            var new_option = $('<option>');
            new_option.attr('value', response.competitions[i].id);
            new_option.attr('data-icon', response.competitions[i].logo);
            new_option.attr('class', 'left circle');
            new_option.attr('slug', response.competitions[i].slug);
            new_option.text(response.competitions[i].name);

            new_option.appendTo(competition_selector);
        }
    }

}

function handleGetGamesRequest(response) {
    data = response.data;

    var groups = $('#groups');

    // Se não houver grupos
    if (!response.data) {
        groups.append($('<p class="center">Ainda nada foi adicionado a esta época.</p>'));
        $('#main_loading').addClass('hide');
        return;
    }

    for (var i = 0; i < response.data.groups.length; i++) {

        var round_chosen = undefined;
        var group = $('#group_template').clone();
        group.attr('id', 'group_' + i);
        group.attr('group', i);
        group.find('.game-group-title').text(response.data.groups[i].name);

        //Se não houver jogos, continua para o próximo
        if (response.data.groups[i].rounds.length < 1) {
            group.find('.row').empty();
            group.append($('<p class="center">Não existem jogos neste grupo.</p>'));
            group.appendTo(groups);
            group.removeClass('hide');
            continue;
        }

        var group_games = group.find('.group-games');
        var left_btn = group_games.find('.button-left');
        left_btn.attr('id', 'left_' + i);
        left_btn.attr('onclick', "leftBtnClicked(event)");

        var right_btn = group_games.find('.button-right');
        right_btn.attr('id', 'right_' + i);
        right_btn.attr('onclick', "rightBtnClicked(event)");

        response.data.groups[i].rounds.sort(function (a, b) {
            return a.number - b.number;
        });

        for (var j = 0; j < response.data.groups[i].rounds.length; j++) {

            var games = $('#games').clone();
            games.attr('id', 'group_' + i + '_round_' + response.data.groups[i].rounds[j].number);
            games.attr('round', response.data.groups[i].rounds[j].number);

            for (var k = 0; k < response.data.groups[i].rounds[j].games.length; k++) {

                if (response.data.groups[i].rounds[j].games[k].finished)
                    round_chosen = response.data.groups[i].rounds[j].number;

                var overview = $('#overview').clone();
                overview.removeAttr('id');
                overview.find('a').attr('href', response.data.groups[i].rounds[j].games[k].game_link);

                var now = Date.now();
                var startGame = new Date(response.data.groups[i].rounds[j].games[k].date);

                startGame.setUTCHours(startGame.getHours(), startGame.getMinutes());

                overview.find('.teams .home-team div span').text(response.data.groups[i].rounds[j].games[k].home_club_name);
                overview.find('.teams .home-team div img').attr('src', response.data.groups[i].rounds[j].games[k].home_club_emblem);

                overview.find('.teams .away-team div span').text(response.data.groups[i].rounds[j].games[k].away_club_name);
                overview.find('.teams .away-team div img').attr('src', response.data.groups[i].rounds[j].games[k].away_club_emblem);

                if (response.data.groups[i].rounds[j].games[k].postponed) {
                    overview.find('.teams .separator time').text("ADI");
                } else if (startGame > now) {
                    var hours = startGame.getHours() > 9 ? startGame.getHours() : '0' + startGame.getHours();
                    var minutes = startGame.getMinutes() > 9 ? startGame.getMinutes() : '0' + startGame.getMinutes();
                    overview.find('.teams .separator time').text(hours + ':' + minutes);
                } else {
                    overview.find('.teams .separator time').text(
                        response.data.groups[i].rounds[j].games[k].home_team_score +
                        ' - ' +
                        response.data.groups[i].rounds[j].games[k].away_team_score);
                }

                overview.removeClass('hide');
                overview.appendTo(games);

            }

            games.appendTo(group_games);

        }

        if (!round_chosen) {
            round_chosen = response.data.groups[i].rounds[0].number
        }

        group.find('#group_' + i + '_round_' + round_chosen + '').removeClass('hide');
        group.find('#group_' + i + '_round_' + round_chosen + '').addClass('active');
        group.find('.round-title .round-name').text(response.data.groups[i].round_name + ' ' + round_chosen);
        group.appendTo(groups);
        group.removeClass('hide');

        showTable(i, round_chosen);

        //stats button
        $('#stats_link').attr('href', data.stats_link);
        $('#stats_button').removeClass('hide');

        // Only show legend for points groups
        if (response.data.groups[i].type === 'points') {
            let legendElement = buildLegendElement(response.data.groups[i]);
            $(legendElement).appendTo(group.find('.group-table'));
        }
    }

    $('#main_loading').addClass('hide');
}

function showTable(group, round) {

    if (group === undefined || round === undefined)
        return;

    var group_element = $('#group_' + group);
    group_element.find('.table-loading').removeClass('hide');
    var tables = group_element.find('.tables');

    if (data.groups[group].type === 'points') {

        var current_table = tables.find('.active');
        if (current_table.length > 0) {

            current_table.removeClass('active');
            current_table.addClass('hide');
        }

        tables.find('table').each(function () {

            if ($(this).attr('round') === round) {
                $(this).addClass('active');
                $(this).removeClass('hide');
                group_element.find('.table-loading').addClass('hide');
            }

        });

        if (tables.find('.active').length !== 1) {
            buildPointsTable(group, round);
        }

    } else {
        group_element.find('.table-loading').addClass('hide');
        group_element.find('.group-table').empty();
        group_element.find('.group-table').append($('<p class="center">Não existe tabela de pontos para esta competição</p>'));
    }
}

function buildPointsTable(group, round) {

    var table = [];

    for (var i = 0; i < data.groups[group].rounds.length; i++) {

        for (var j = 0; j < data.groups[group].rounds[i].games.length; j++) {

            var game = data.groups[group].rounds[i].games[j];

            table = addLine(table, game.home_club_name, game.home_club_emblem, game.home_club_url);
            table = addLine(table, game.away_club_name, game.away_club_emblem, game.away_club_url);

            if (game.finished) {
                table = addMatchPlayed(table, game.home_club_name);
                table = addMatchPlayed(table, game.away_club_name);
                table = processGameResult(table, game);
            }
        }

        if (data.groups[group].rounds[i].number >= round)
            break;
    }

    orderTable(table, data.groups[group].rules_name, group, round);
}

function buildTableDOM(table, group, round) {

    var group_element = $('#group_' + group);

    var tables = group_element.find('.tables');
    var table_element = tables.find('#table').clone();
    table_element.attr('id', 'group_' + group +'_table_' + round);
    table_element.attr('round', round);
    table_element.append('<tbody></tbody>');
    let gameGroup = data.groups[group];

    for (var k = 0; k < table.length; k++) {
        var line = $('<tr></tr>');

        // Legacy promotes and relegates logic
        if (!gameGroup.positions || gameGroup.positions.length === 0) {
            if (k === 0)
                line.attr("id", "champion");
    
            if (k > 0 && k < data.groups[group].promotes) {
                line.addClass("promotion");
            }
    
            if (k > 0 && k >= (table.length - data.groups[group].relegates)) {
                line.addClass("relegate");
            }
        } else {
            line = setLineStyle(line, k+1, gameGroup.positions);
        }

        var position = $('<td class="number">' + (k + 1) + '</td>');
        var club_emblem = $('<td class="table-club-emblem"><img src="' + table[k].club_emblem + '"/></td>');
        var club_name = $('<td><a href="' + table[k].club_url + '">' + table[k].club_name + '</a></td>');
        var matches_played = $('<td class="number hide-on-small-and-down">' + table[k].matches_played + '</td>');
        var wins = $('<td class="number hide-on-med-and-down">' + table[k].wins + '</td>');
        var draws = $('<td class="number hide-on-med-and-down">' + table[k].draws + '</td>');
        var loses = $('<td class="number hide-on-med-and-down">' + table[k].loses + '</td>');
        var gf = $('<td class="number hide-on-med-and-down">' + table[k].gf + '</td>');
        var ga = $('<td class="number hide-on-med-and-down">' + table[k].ga + '</td>');
        var gd = $('<td class="number hide-on-small-and-down">' + (parseInt(table[k].gf) - parseInt(table[k].ga)) + '</td>');
        var points = $('<td class="number">' + table[k].points + '</td>');

        position.appendTo(line);
        club_emblem.appendTo(line);
        club_name.appendTo(line);
        matches_played.appendTo(line);
        wins.appendTo(line);
        draws.appendTo(line);
        loses.appendTo(line);
        gf.appendTo(line);
        ga.appendTo(line);
        gd.appendTo(line);
        points.appendTo(line);

        line.appendTo(table_element.find('tbody'));
    }

    group_element.find('.table-loading').addClass('hide');
    table_element.appendTo(tables);
    table_element.addClass('active');
    table_element.removeClass('hide');
}

function setLineStyle(line, position, positions) {

    if (!positions || !Array.isArray(positions)) {
        return line;
    }

    for (var i = 0; i < positions.length; i++) {
        if (positions[i].positions.includes(position)) {
            line.attr('style', `background-color: ${positions[i].color};`);
            line.attr('data-label', positions[i].label);
            break;
        }
    }

    return line;
}

function addLine(table, club_name, club_emblem, club_url) {

    var found = false;

    for (var i = 0; i < table.length; i++) {

        if (table[i].club_name === club_name) {
            found = true;
            break;
        }
    }

    if (!found) {
        table.push({
            club_emblem: club_emblem,
            club_name: club_name,
            club_url: club_url,
            matches_played: parseInt(0),
            wins: parseInt(0),
            draws: parseInt(0),
            loses: parseInt(0),
            gf: parseInt(0),
            ga: parseInt(0),
            points: parseInt(0)
        });
    }

    return table;
}

function addMatchPlayed(table, club_name) {

    for (var i = 0; i < table.length; i++) {

        if (table[i].club_name === club_name) {
            table[i].matches_played++;
        }
    }

    return table;

}

function processGameResult(table, game) {

    for (var i = 0; i < table.length; i++) {

        if (table[i].club_name === game.home_club_name) {

            table[i].gf += parseInt(game.home_team_score);
            table[i].ga += parseInt(game.away_team_score);

            if (game.home_team_score > game.away_team_score) {
                table[i].wins++;
                table[i].points += parseInt(3);
            }
            else if (game.home_team_score === game.away_team_score) {
                table[i].draws++;
                table[i].points++;
            } else {
                table[i].loses++;
            }
        }

        if (table[i].club_name === game.away_club_name) {

            table[i].gf += parseInt(game.away_team_score);
            table[i].ga += parseInt(game.home_team_score);

            if (game.home_team_score < game.away_team_score) {
                table[i].wins++;
                table[i].points += parseInt(3);
            }
            else if (game.home_team_score === game.away_team_score) {
                table[i].draws++;
                table[i].points++;
            } else {
                table[i].loses++;
            }
        }

    }

    return table;
}

function orderTable(table, rules_name, group, round) {

    table = orderTableByPoints(table);

    // Check if there's a custom tie breaker script
    if (data.groups[group].tie_breaker_script && data.groups[group].tie_breaker_script.trim() !== '') {
        try {
            // Create a function from the stored script
            const customTieBreaker = new Function('table', 'group', 'round', data.groups[group].tie_breaker_script.trim());
            customTieBreaker(table, group, round);
            return;
        } catch (error) {
            console.error('Error executing custom tie breaker script:', error);
            console.log('Falling back to legacy rules...');
        }
    }

    // Fallback to legacy rules system
    switch (rules_name) {
        case 'Default Points':
            buildTableDOM(table, group, round);
            break;
        case 'afpb_pontos_2018_div1':
        case 'afpb_pontos_2018_div2':
        case 'afpb_pontos_basico':
        case 'afpb_pontos_2019_div1':
        case 'afpb_series_2021':
        case 'afpb_pontos_2023_div1':
        case 'afpb_pontos_series_2023_div2':
            afpb_points_2018(table, group, round);
            break;
        default:
            buildTableDOM(table, group, round);
            break;
    }
}

function orderTableByPoints(table) {

    table.sort(function (a,b) {
        return b.points - a.points;
    });

    return table;
}

function buildLegendElement(gameGroup) {

    if (!gameGroup.positions || !Array.isArray(gameGroup.positions) || gameGroup.positions.length === 0) {
        return buildLegacyStaticLegendElement(gameGroup);
    }

    const positions = gameGroup.positions;
    let legendElement = $(`
        <div class="legend-container" style="margin-top: 20px; padding: 10px; border-radius: 4px; background-color: #f5f5f5; border-left: 4px solid #107db7;">
            <h6 style="margin: 0 0 8px 0; font-size: 14px; font-weight: 500; color: #424242;">Legenda</h6>
            <div class="legend-items" style="display: flex; flex-wrap: wrap; gap: 8px;"></div>
        </div>
    `);
    
    let legendItems = legendElement.find('.legend-items');
    
    for (var i = 0; i < positions.length; i++) {
        let legendItem = $(`
            <div class="legend-item" style="
                display: inline-flex; 
                align-items: center; 
                padding: 4px 8px; 
                border-radius: 12px; 
                font-size: 12px; 
                font-weight: 500; 
                color: #fff; 
                text-shadow: 0 1px 2px rgba(0,0,0,0.3);
                box-shadow: 0 1px 3px rgba(0,0,0,0.2);
                background-color: ${positions[i].color};
            ">
                <span class="legend-dot" style="
                    width: 8px; 
                    height: 8px; 
                    border-radius: 50%; 
                    background-color: rgba(255,255,255,0.3); 
                    margin-right: 6px;
                "></span>
                ${positions[i].label}
            </div>
        `);
        legendItems.append(legendItem);
    }
    
    return legendElement;
}

function buildLegacyStaticLegendElement (group) {
    let legendElement = $(`
        <div class="legend-container" style="margin-top: 20px; padding: 10px; border-radius: 4px; background-color: #f5f5f5; border-left: 4px solid #107db7;">
            <h6 style="margin: 0 0 8px 0; font-size: 14px; font-weight: 500; color: #424242;">Legenda</h6>
            <div class="legend-items" style="display: flex; flex-wrap: wrap; gap: 8px;"></div>
        </div>
    `);

    let legendItems = legendElement.find('.legend-items');

    // if has promotion spots add promotion legend
    if (group.promotes > 0) {
        let promotionLegend = $(`
            <div class="legend-item" style="
                display: inline-flex; 
                align-items: center; 
                padding: 4px 8px; 
                border-radius: 12px; 
                font-size: 12px; 
                font-weight: 500; 
                color: #fff; 
                text-shadow: 0 1px 2px rgba(0,0,0,0.3);
                box-shadow: 0 1px 3px rgba(0,0,0,0.2);
                background-color: #bbdcb1cc;
            ">
            <span class="legend-dot" style="
                    width: 8px; 
                    height: 8px; 
                    border-radius: 50%; 
                    background-color: rgba(255,255,255,0.3); 
                    margin-right: 6px;
                "></span>
                Promoção
            </div>
        `);
        legendItems.append(promotionLegend);
    }

    if (group.promotes == 0) {
        legendItems.append($(`
            <div class="legend-item" style="
                display: inline-flex; 
                align-items: center; 
                padding: 4px 8px; 
                border-radius: 12px; 
                font-size: 12px; 
                font-weight: 500; 
                color: #fff; 
                text-shadow: 0 1px 2px rgba(0,0,0,0.3);
                box-shadow: 0 1px 3px rgba(0,0,0,0.2);
                background-color: #80cf6ecc;
            ">
            <span class="legend-dot" style="
                    width: 8px; 
                    height: 8px; 
                    border-radius: 50%; 
                    background-color: rgba(255,255,255,0.3); 
                    margin-right: 6px;
                "></span>
                Campeão
            </div>
        `));
    }

    if (group.relegates > 0) {
        let relegationLegend = $(`
            <div class="legend-item" style="
                display: inline-flex; 
                align-items: center; 
                padding: 4px 8px; 
                border-radius: 12px; 
                font-size: 12px; 
                font-weight: 500; 
                color: #fff; 
                text-shadow: 0 1px 2px rgba(0,0,0,0.3);
                box-shadow: 0 1px 3px rgba(0,0,0,0.2);
                background-color: #f08d8dcc;
            ">
            <span class="legend-dot" style="
                    width: 8px; 
                    height: 8px; 
                    border-radius: 50%; 
                    background-color: rgba(255,255,255,0.3); 
                    margin-right: 6px;
                "></span>
                Despromoção
            </div>
        `);
        legendItems.append(relegationLegend);
    }

    return legendElement;
}
