<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Team;
use Illuminate\Support\Collection;

class Season extends Model
{
    protected $fillable = [
        'competition_id',
        'name',
        'picture',
        'relegates',
        'promotes',
        'start_year',
        'end_year',
        'table_rules',
        'obs',
        'visible',
    ];

    protected $guarded = [];

    protected $hidden = [];

    public function competition() {
        return $this->belongsTo(Competition::class);
    }

    public function game_groups() {
        return $this->hasMany(GameGroup::class);
    }

    public function getName() {

        if ($this->start_year != $this->end_year)
            return $this->start_year . '/' . $this->end_year;
        else
            return $this->start_year;
    }

    public function getGroupBySlug($slug) {

        $group = null;
        $groups = $this->game_groups;

        foreach ($groups as $g) {

            if (str_slug($g->name) == $slug) {
                $group = $g;
                break;
            }

        }
        return $group;
    }

    /**
     * Public display name for this season (override or competition fallback).
     */
    public function getDisplayName(): string
    {
        if (!empty($this->name)) {
            return $this->name;
        }

        return $this->competition->name;
    }

    /**
     * Public logo for this season (override or competition fallback).
     */
    public function getDisplayPicture(): ?string
    {
        if (!empty($this->picture)) {
            return $this->picture;
        }

        return $this->competition->picture;
    }

    /**
     * Slug derived from the public display name.
     */
    public function getDisplaySlug(): string
    {
        return str_slug($this->getDisplayName());
    }

    /**
     * Season URL identifier: 2025-26 or 2025 when start == end.
     */
    public function getNameSlug(): string
    {
        if ($this->start_year == $this->end_year) {
            return (string) $this->start_year;
        }

        return $this->start_year . '-' . substr((string) $this->end_year, -2);
    }

    /**
     * Parse a season URL slug into [start_year, end_year].
     * Accepts: 2025, 2025-26, 2025-2026.
     *
     * @return int[]|null
     */
    public static function parseNameSlug(string $slug): ?array
    {
        if (preg_match('/^(\d{4})$/', $slug, $m)) {
            $year = (int) $m[1];
            return [$year, $year];
        }

        if (preg_match('/^(\d{4})-(\d{4})$/', $slug, $m)) {
            return [(int) $m[1], (int) $m[2]];
        }

        if (preg_match('/^(\d{4})-(\d{2})$/', $slug, $m)) {
            $start = (int) $m[1];
            $endTwo = (int) $m[2];
            $startTwo = $start % 100;

            if ($endTwo < $startTwo) {
                $end = ((int) floor($start / 100) + 1) * 100 + $endTwo;
            } else {
                $end = ((int) floor($start / 100)) * 100 + $endTwo;
            }

            return [$start, $end];
        }

        return null;
    }

    /**
     * Whether $slug is a legacy full-year form (2024-2025) that should redirect.
     */
    public static function isLegacyFullYearSlug(string $slug): bool
    {
        return (bool) preg_match('/^\d{4}-\d{4}$/', $slug);
    }

    /**
     * Find a visible season by years and competition display slug.
     */
    public static function findBySeasonAndCompetitionSlug(string $season_slug, string $competition_slug): ?Season
    {
        $years = self::parseNameSlug($season_slug);
        if (!$years) {
            return null;
        }

        [$start_year, $end_year] = $years;

        $seasons = self::with('competition')
            ->where('visible', true)
            ->where('start_year', $start_year)
            ->where('end_year', $end_year)
            ->get();

        foreach ($seasons as $season) {
            $competition = $season->competition;
            // From inside a Model subclass, ->visible resolves Eloquent's protected
            // $visible array — use getAttribute() for the DB column.
            if (!$competition || !$competition->getAttribute('visible')) {
                continue;
            }

            if ($season->getDisplaySlug() == $competition_slug) {
                return $season;
            }
        }

        // Fallback: match against canonical competition name slug
        foreach ($seasons as $season) {
            $competition = $season->competition;
            if (!$competition || !$competition->getAttribute('visible')) {
                continue;
            }

            if (str_slug($competition->name) == $competition_slug) {
                return $season;
            }
        }

        return null;
    }

    public function getPublicUrl(): string
    {
        return route('competition', [
            'season_slug' => $this->getNameSlug(),
            'competition_slug' => $this->getDisplaySlug(),
        ]);
    }
}
