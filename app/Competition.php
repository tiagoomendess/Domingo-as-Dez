<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Facades\Voyager;

class Competition extends Model
{
    protected $fillable = ['name', 'competition_type', 'picture', 'visible'];

    protected $guarded = [];

    protected $hidden = [];

    public function seasons() {
        return $this->hasMany(Season::class);
    }

    /**
     * Gets the competition by the slug provided, null if not found
     *
     * @param $slug string
     * @return Competition
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
     * Gets the serason that starts and ends in the provided years
     *
     * @param $start_year int
     * @param $end_year int
     *
     * @return Season
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


    public function getPublicUrl() {
        return route('competition', ['slug' => str_slug($this->name)]);
    }

    public function getPicture()
    {
        return Voyager::image($this->picture);
    }
}
