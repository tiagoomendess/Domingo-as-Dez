<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $fillable = [
        'home_team_id',
        'away_team_id',
        'season_id',
        'round',
        'date',
        'playground_id',
        'goals_home',
        'goals_away',
        'penalties_home',
        'penalties_away',
        'game_group_id',
        'finished',
        'visible',
        'postponed',
        'image',
        'generate_image',
        'scoreboard_updates',
    ];

    protected $guarded = [];

    protected $hidden = [];

    public function game_group() {
        return $this->belongsTo(GameGroup::class);
    }

    public function goals() {
        return $this->hasMany(Goal::class);
    }

    public function playground() {
        return $this->belongsTo(Playground::class);
    }

    public function homeTeam() {
        return $this->belongsTo(Team::class, 'home_team_id');
    }

    public function home_team() {
        return $this->homeTeam();
    }

    public function awayTeam() {
        return $this->belongsTo(Team::class, 'away_team_id');
    }

    public function away_team() {
        return $this->awayTeam();
    }

    public function referees() {
        return $this->belongsToMany(Referee::class, 'game_referees');
    }

    public function game_referees() {
        return $this->hasMany(GameReferee::class);
    }

    public function decidedByPenalties() {
        if ($this->isDraw()) {
            if (!is_null($this->penalties_home) && !is_null($this->penalties_away))
                return true;
            else
                return false;
        } else {
            return false;
        }
    }

    /**
     * Get total home goals of this game
     */
    public function getTotalHomeGoals() {

        $goals = $this->goals;
        $total_goals = 0;

        foreach ($goals as $goal) {

            if ($goal->team->id == $this->homeTeam->id)
                $total_goals++;

        }

        return $total_goals;
    }

    /**
     * Get total home goals of this game
     */
    public function getTotalAwayGoals() {

        $goals = $this->goals;
        $total_goals = 0;

        foreach ($goals as $goal) {

            if ($goal->team->id == $this->awayTeam->id)
                $total_goals++;

        }

        return $total_goals;
    }

    /**
     * Gets the winner of this game
     *
     * @return Team
     */
    public function winner() {

        if ((!is_null($this->goals_home)) && (!is_null($this->goals_away))) {

            if ($this->goals_home > $this->goals_away)
                return $this->homeTeam;
            else if ($this->goals_home < $this->goals_away)
                return $this->awayTeam;
            else
                return null;

        } else {

            if ($this->getTotalHomeGoals() > $this->getTotalAwayGoals())
                return $this->homeTeam;
            else if ($this->getTotalHomeGoals() < $this->getTotalAwayGoals())
                return $this->awayTeam;
            else
                return null;

        }

    }

    public function isDraw() {

        $isDraw = false;

        if ((!is_null($this->goals_home)) && (!is_null($this->goals_away))) {

            if ($this->goals_home == $this->goals_away)
                $isDraw = true;
            else
                $isDraw = false;

        } else {

            if ($this->getTotalHomeGoals() == $this->getTotalAwayGoals())
                $isDraw = true;

            else
                $isDraw = false;

        }

        return $isDraw;

    }

    public function getPublicUrl() {

        return route('front.games.show', [
            'competition_slug' => str_slug($this->game_group->season->competition->name),
            'season_slug' => str_replace('/', '-', $this->game_group->season->getName()),
            'group_slug' => str_slug($this->game_group->name),
            'round' => $this->round,
            'clubs_slug' => str_slug($this->home_team->club->name) . '-vs-' . str_slug($this->away_team->club->name),
        ]);

    }

    public function started() {

        $start = Carbon::createFromFormat("Y-m-d H:i:s", $this->date);
        $now = Carbon::now();

        if ($now->timestamp > $start->timestamp)
            return true;
        else
            return false;
    }

    //gets the score via total goals or via goals_home/away field
    public function getHomeScore() {

        if (!is_null($this->goals_home))
            return $this->goals_home;
        else
            return $this->getTotalHomeGoals();
    }

    //gets the score via total goals or via goals_home/away field
    public function getAwayScore() {

        if (!is_null($this->goals_away))
            return $this->goals_away;
        else
            return $this->getTotalAwayGoals();
    }

    public function getHomeGoals() {

        $all_goals = $this->goals;
        $return_goals = collect();

        foreach ($all_goals as $goal) {
            if ($goal->team->id == $this->home_team->id)
                $return_goals->push($goal);
        }

        return $return_goals;
    }

    public function getAwayGoals() {

        $all_goals = $this->goals;
        $return_goals = collect();

        foreach ($all_goals as $goal) {
            if ($goal->team->id == $this->away_team->id)
                $return_goals->push($goal);
        }

        return $return_goals;
    }

    public static function getLiveGames() {

        $now = Carbon::now();
        $begin_today = Carbon::create($now->year, $now->month, $now->day, 0,0,0);
        $end_today = Carbon::create($now->year, $now->month, $now->day, 23,59,59);

        $today_games = Game::where('date', '>', $begin_today)->where('date', '<', $end_today)->get();

        $games = collect();

        foreach ($today_games as $game) {
            if ($game->postponed)
                continue;

            $game_date = Carbon::createFromFormat("Y-m-d H:i:s", $game->date);
            $game_warmup_date = Carbon::createFromTimestamp($game_date->timestamp);
            $game_warmup_date->subMinutes(30);
            $game_end_date = Carbon::createFromTimestamp($game_date->timestamp);
            $game_end_date->addHours(2)->addMinutes(30);

            if ($now->timestamp > $game_warmup_date->timestamp && $now->timestamp < $game_end_date->timestamp)
                $games->push($game);

        }

        return $games;
    }

    public function isMvpVoteOpen(): bool
    {
        $now = new Carbon();
        $gameSecondHalf = new Carbon($this->date);
        $gameSecondHalf->addMinutes(65);
        $endTime = new Carbon($this->date);
        $endTime->addMinutes(240);

        if ($now->timestamp > $gameSecondHalf->timestamp && $now->timestamp < $endTime->timestamp)
            return true;
        else
            return false;
    }

    public function allowScoreReports(): bool {

        $now = Carbon::now();
        $kickOffAt = Carbon::createFromFormat("Y-m-d H:i:s", $this->date);
        $kickOffAt->addHours(3);

        return $this->started() && $now < $kickOffAt->addHours(3);
    }
}
