<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Competition extends Model
{
    protected $fillable = ['name', 'competition_type', 'picture', 'visible', 'priority'];

    protected $guarded = [];

    protected $hidden = [];

    public function seasons() {
        return $this->hasMany(Season::class);
    }

    /**
     * Visible competitions ordered by priority (highest first), then id.
     */
    public function scopeOrderedForFrontend($query)
    {
        return $query->orderByDesc('priority')->orderBy('id');
    }

    /**
     * Visible competitions for public navigation/listings.
     */
    public static function visibleOrdered()
    {
        return self::where('visible', true)->orderedForFrontend()->get();
    }

    /**
     * Gets the competition by the slug provided, null if not found
     *
     * @param $slug string
     * @return Competition|null
    */
    public static function getCompetitionBySlug($slug) {

        $competitions = Competition::all();
        $comp = null;

        foreach ($competitions as $competition) {

            if(str_slug($competition->name) == $slug) {
                $comp = $competition;
                break;
            }

        }

        return $comp;

    }

    /**
     * Find competition by a public display slug for its latest visible season,
     * falling back to the canonical competition name slug.
     *
     * @return array{competition: Competition, season: Season}|null
     */
    public static function findLatestByDisplaySlug(string $slug): ?array
    {
        $seasons = Season::with('competition')
            ->where('visible', true)
            ->whereHas('competition', function ($q) {
                $q->where('visible', true);
            })
            ->orderByDesc('start_year')
            ->orderByDesc('id')
            ->get();

        foreach ($seasons as $season) {
            if ($season->getDisplaySlug() === $slug) {
                return [
                    'competition' => $season->competition,
                    'season' => $season,
                ];
            }
        }

        $competition = self::getCompetitionBySlug($slug);
        if (!$competition || !$competition->getAttribute('visible')) {
            return null;
        }

        $season = $competition->seasons()
            ->where('visible', true)
            ->orderByDesc('start_year')
            ->orderByDesc('id')
            ->first();

        if (!$season) {
            return null;
        }

        return [
            'competition' => $competition,
            'season' => $season,
        ];
    }

    /**
     * Latest visible season for this competition.
     */
    public function getLatestVisibleSeason(): ?Season
    {
        return $this->seasons()
            ->where('visible', true)
            ->orderByDesc('start_year')
            ->orderByDesc('id')
            ->first();
    }

    /**
     * Public display name for the latest season (or canonical name).
     */
    public function getCurrentDisplayName(): string
    {
        $season = $this->getLatestVisibleSeason();
        if ($season) {
            return $season->getDisplayName();
        }

        return $this->name;
    }

    /**
     * Public logo for the latest season (or canonical picture).
     */
    public function getCurrentDisplayPicture(): ?string
    {
        $season = $this->getLatestVisibleSeason();
        if ($season) {
            return $season->getDisplayPicture();
        }

        return $this->picture;
    }

    /**
     * Gets the season that starts and ends in the provided years
     *
     * @param $start_year int
     * @param $end_year int|null
     *
     * @return Season|null
    */
    public function getSeasonByYears($start_year, $end_year = null){

        $season = null;

        foreach ($this->seasons as $s) {

            if (!$end_year) {
                if ($s->start_year == $start_year && $s->start_year == $s->end_year)
                    $season = $s;
            } else {
                if ($s->start_year == $start_year && $s->end_year == $end_year)
                    $season = $s;
            }
        }

        return $season;
    }


    public function getPublicUrl(?Season $season = null) {
        if (!$season) {
            $season = $this->getLatestVisibleSeason();
        }

        if ($season) {
            return $season->getPublicUrl();
        }

        return route('competitions');
    }
}
