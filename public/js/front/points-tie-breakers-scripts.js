var local_group;
var max_round;

function afpb_points_2018(table1, group, round) {

    local_group = data.groups[group];
    max_round = round;
    console.log("ROUND: " + round);
    // console.log(local_group);
    console.log(table1);
    console.log(data.groups[group]);
    console.log("--------------------");

    for (var i = 0; i < table1.length; i++) {

        //Check if it is in last position
        if (i + 1 >= table1.length)
            break;

        for (var j = i + 1; j < table1.length; j++) {
            if (table1[i].points === table1[j].points) {
                console.log("Breaking tie in points!" + "(" + (i + 1) + "º " + table1[(i)].club_name + " e " + (i + 2) + "º " + table1[j].club_name + ")");

                //a) Número de pontos alcançados pelos clubes nos jogos disputados entre si;
                var diffInPoints = getPointDiffFromMatchesBetweenTeams(table1[i].club_name, table1[j].club_name);

                if (diffInPoints < 0) {
                    table1 = switchPlacesWithNext(table1, i);
                } else if (diffInPoints === 0) {
                    //b) Maior diferença entre o número de golos marcados e sofridos nos jogos disputados entre os clubes empatados;
                    var diffInGoals = getGoalDiffFromMatchesBetweenTeams(table1[i].club_name, table1[j].club_name);

                    if (diffInGoals < 0) {
                        table1 = switchPlacesWithNext(table1, i);
                    } else if (diffInGoals === 0) {
                        //c) Maior diferença entre os golos marcados e sofridos, durante toda a competição
                        if ((table1[i].gf - table1[i].ga) < (table1[j].gf - table1[j].ga)) {
                            table1 = switchPlacesWithNext(table1, i);
                        } else if ((table1[i].gf - table1[i].ga) === (table1[j].gf - table1[j].ga)) {
                            //d) Maior número de vitórias na competição
                            if (table1[i].wins < table1[j].wins) {
                                table1 = switchPlacesWithNext(table1, i);
                            } else if (table1[i].wins === table1[j].wins) {
                                //e) Maior número de golos marcados na competição
                                if (table1[i].gf < table1[j].gf) {
                                    table1 = switchPlacesWithNext(table1, i);
                                } else if (table1[i].gf === table1[j].gf) {
                                    //f) Menor número de golos sofridos na competição
                                    if (table1[i].ga > table1[j].ga) {
                                        table1 = switchPlacesWithNext(table1, i);
                                    } else if (table1[i].ga === table1[j].ga) {
                                        //End of rules, maybe alphabetically next?
                                        console.log("End of rule list. Cannot resolve conflict!");
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    console.log(table1);

    return table1;
}

function switchPlacesWithNext(table, position) {
    console.log("Switched " + (position + 1) + " (" + table[position].club_name + ") for " + (position + 2) + " (" + table[position + 1].club_name + ")");
    var aux;
    aux = table[position];
    table[position] = table[position + 1];
    table[position + 1] = aux;
    return table;
}

function getPointDiffFromMatchesBetweenTeams(club_1_name, club_2_name) {

    var points_team_1 = 0;
    var points_team_2 = 0;

    for (var i = 0; i < local_group.rounds.length; i++) {

        if (local_group.rounds[i].number > max_round) {
            break;
        }

        for (var j = 0; j < local_group.rounds[i].games.length; j++) {

            if (local_group.rounds[i].games[j].home_club_name === club_1_name &&
                local_group.rounds[i].games[j].away_club_name === club_2_name) {

                if (local_group.rounds[i].games[j].home_team_score > local_group.rounds[i].games[j].away_team_score) {
                    points_team_1 += 3;
                } else if (local_group.rounds[i].games[j].home_team_score < local_group.rounds[i].games[j].away_team_score) {
                    points_team_2 += 3;
                } else {
                    points_team_1 += 1;
                    points_team_2 += 1;
                }

            }

            if (local_group.rounds[i].games[j].away_club_name === club_1_name &&
                local_group.rounds[i].games[j].home_club_name === club_2_name) {

                if (local_group.rounds[i].games[j].home_team_score > local_group.rounds[i].games[j].away_team_score) {
                    points_team_2 += 3;
                } else if (local_group.rounds[i].games[j].home_team_score < local_group.rounds[i].games[j].away_team_score) {
                    points_team_1 += 3;
                } else {
                    points_team_1 += 1;
                    points_team_2 += 1;
                }

            }
        }

    }

    return points_team_1 - points_team_2;
}

function getGoalDiffFromMatchesBetweenTeams(club_1_name, club_2_name) {

    var goals_diff_team_1 = 0;
    var goals_diff_team_2 = 0;

    for (var i = 0; i < local_group.rounds.length; i++) {

        if (local_group.rounds[i].number > max_round) {
            break;
        }

        for (var j = 0; j < local_group.rounds[i].games.length; j++) {

            if (local_group.rounds[i].games[j].home_club_name === club_1_name &&
                local_group.rounds[i].games[j].away_club_name === club_2_name) {

                goals_diff_team_1 += (local_group.rounds[i].games[j].home_team_score - local_group.rounds[i].games[j].away_team_score);
                goals_diff_team_2 += (local_group.rounds[i].games[j].away_team_score - local_group.rounds[i].games[j].home_team_score);
            }

            if (local_group.rounds[i].games[j].away_club_name === club_1_name &&
                local_group.rounds[i].games[j].home_club_name === club_2_name) {

                goals_diff_team_2 += (local_group.rounds[i].games[j].home_team_score - local_group.rounds[i].games[j].away_team_score);
                goals_diff_team_1 += (local_group.rounds[i].games[j].away_team_score - local_group.rounds[i].games[j].home_team_score);

            }
        }

    }

    //console.log("GOALS DIFF: " + goals_diff_team_1 + " | " + goals_diff_team_2);
    return goals_diff_team_1 - goals_diff_team_2;
}