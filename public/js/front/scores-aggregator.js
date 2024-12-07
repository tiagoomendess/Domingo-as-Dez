document.addEventListener("DOMContentLoaded", function() {

    let lastAutoHighlightedMatchPosition = 0;
    const scoreChangedAt = []
    const highlightMatchDiv = document.getElementById("highlighted_match");
    const sponsorDiv = document.getElementById("sponsor");
    const allMatchesDiv = document.getElementById("all_matches");
    let isFirstLoad = true;

    // API endpoint to fetch matches data
    const API_URL = "/api/games/live";

    // State for highlighted match
    let highlightedMatch = null;
    let highlightedMatchId = null;

    // Function to fetch matches
    async function fetchMatches() {
        try {
            const response = await fetch(API_URL);
            const data = await response.json();
            return data.data; // Return the array of competitions
        } catch (error) {
            console.error("Error fetching match data:", error);
            return [];
        }
    }

    // Function to display a highlighted match
    function displayHighlightedMatch(match, recentGoal = false) {
        if (!match) return;

        highlightedMatchId = match.id;
        const goalClass = recentGoal ? "goal" : "";

        highlightMatchDiv.innerHTML = `
                <div class="">
                    <div style="width: 100%">
                       <h2 style="text-align: center">${match.home_club_name} vs ${match.away_club_name}</h2>
                    </div>
                    <div class="main-game">
                        <div><img src="${match.home_club_emblem}" alt="${match.home_club_name}" width="100px"></div>
                        <div style="margin: 0 20px"><p style="font-size: 3.5rem" class="${goalClass}">${match.home_score}-${match.away_score}</p></div>
                        <div><img src="${match.away_club_emblem}" alt="${match.away_club_name}" width="100px"></div>
                    </div>
                </div>
            `;
    }

    // Function to display all matches in individual marquee elements for each competition
    function displayAllMatches(competitions) {
        if (!isFirstLoad) {
            hotReload(competitions)
            return;
        }

        allMatchesDiv.innerHTML = ""; // Clear previous matches

        competitions.forEach(competition => {
            const competitionContainer = document.createElement("div");
            competitionContainer.classList.add("competition-container");

            const competitionTitle = document.createElement("h2");
            competitionTitle.textContent = `${competition.competition_name} - ${competition.game_group_name}`;
            competitionContainer.appendChild(competitionTitle);

            const marquee = document.createElement("marquee");
            marquee.setAttribute("behavior", "scroll");
            marquee.setAttribute("direction", "left");
            marquee.setAttribute("scrollamount", "7");

            competition.games.forEach(match => {
                const matchElement = document.createElement('div');
                matchElement.classList.add('footer-match');
                matchElement.innerHTML = `
                    <div class="inner-match">
                        <div class="home-team">
                            <span style="margin-right: 10px">${match.home_club_name}</span>
                            <img style="width: 50px" src="${match.home_club_emblem}">
                        </div>
                        <div class="score" id="score_${match.id}">${match.home_score}-${match.away_score}</div>
                        <div class="away-team">
                            <img style="width: 50px" src="${match.away_club_emblem}">
                            <span style="margin-left: 10px">${match.away_club_name}</span>
                        </div>
                    </div>
                `;
                matchElement.addEventListener('click', () => {
                    highlightedMatch = match;
                    displayHighlightedMatch(match);
                });
                marquee.appendChild(matchElement);
            });

            competitionContainer.appendChild(marquee);
            allMatchesDiv.appendChild(competitionContainer);
        });

        isFirstLoad = false;
    }

    // Function to rotate highlighted matches if none is manually selected
    function rotateHighlightedMatch(competitions) {
        if (highlightedMatch) {
            return;
        }

        const allGames = competitions.flatMap(competition => competition.games);

        // Run every match once before repeating instead of random
        displayHighlightedMatch(allGames[lastAutoHighlightedMatchPosition]);
        lastAutoHighlightedMatchPosition = lastAutoHighlightedMatchPosition + 1 >= allGames.length ? 0 : lastAutoHighlightedMatchPosition + 1;
    }

    // Function to update matches periodically
    async function updateMatches() {
        const competitions = await fetchMatches();

        // Display all matches in footer
        displayAllMatches(competitions);

        rotateHighlightedMatch(competitions);

        setTimeout(updateMatches, 4000);
    }

    function hotReload(competitions) {
        let changedAtLeastOne = false;
        competitions.forEach(competition => {
            competition.games.forEach(match => {
                const matchId = match.id;
                const scoreElement = document.getElementById(`score_${matchId}`);
                if (scoreElement) {
                    // Get current score from element
                    const currentScore = scoreElement.textContent.trim();
                    const newScore = `${match.home_score}-${match.away_score}`;
                    scoreElement.textContent = newScore;
                    if (currentScore !== newScore) {
                        // save timestamp of the last change
                        scoreChangedAt[matchId] = Date.now();
                        scoreElement.classList.add('goal');
                        changedAtLeastOne = true;
                    }

                    // remove goal indicator after 30 seconds
                    if (scoreChangedAt[matchId] && Date.now() - scoreChangedAt[matchId] > 30000) {
                        scoreElement.classList.remove('goal');
                        delete scoreChangedAt[matchId];
                    }

                    if (matchId === highlightedMatchId) {
                        displayHighlightedMatch(match, newScore !== newScore);
                    }
                }
            });
        });

        if (changedAtLeastOne) {
            playGoalSound();
        }
    }

    function playGoalSound() {
        const goalSound = new Audio('/sounds/notification_1.mp3'); // Replace with the correct path
        goalSound.play()
            .catch(error => {
                console.info("Playback failed:", error);
            });
    }

    // Initial load
    updateMatches();
});
