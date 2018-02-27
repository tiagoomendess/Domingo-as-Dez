<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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
}
