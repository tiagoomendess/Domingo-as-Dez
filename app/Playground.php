<?php

namespace App;

use Illuminate\Support\Facades\DB;

class Playground extends SearchableModel
{
    protected $fillable = ['club_id', 'name', 'surface', 'width', 'height', 'capacity', 'picture', 'visible', 'priority', 'location'];

    protected $guarded = [];

    protected $hidden = [];

    protected $geometry = ['location'];

    protected $geometryAsText = true;

    public const SEARCH_FIELDS = [
        'name' => [
            'name' => 'name',
            'type' => 'string',
            'trans' => 'Nome',
            'allowSearch' => true,
            'compare' => 'like',
            'validation' => 'nullable|min:3|max:30|string'
        ],
        'id' => [
            'name' => 'id',
            'type' => 'integer',
            'trans' => 'Id',
            'allowSearch' => true,
            'compare' => '=',
            'validation' => 'nullable|integer'
        ],
        'created_at' => [
            'name' => 'created_at',
            'type' => 'date',
            'trans' => 'Data de Criação',
            'allowSearch' => false
        ],
        'updated_at' => [
            'name' => 'updated_at',
            'type' => 'date',
            'trans' => 'Ultima Atualização',
            'allowSearch' => false
        ]
    ];

    public function club()
    {
        return $this->belongsTo('App\Club');
    }

    public function games()
    {
        return $this->hasMany('App\Game');
    }

    public function getPicture()
    {
        if ($this->picture)
            return $this->picture;
        else
            return Media::getPlaceholder('16:9', $this->id);
    }

    public function getLatitude()
    {
        if (empty($this->location))
            return null;

        $both = str_replace(['POINT(', ')'], '', $this->location);
        $inArray = explode(' ', $both);

        if (count($inArray) != 2)
            return null;

        return (float) $inArray[0];
    }

    public function getLongitude()
    {
        if (empty($this->location))
            return null;

        $both = str_replace(['POINT(', ')'], '', $this->location);
        $inArray = explode(' ', $both);

        if (count($inArray) != 2)
            return null;

        return (float) $inArray[1];
    }

    public function toPoint($latitude, $longitude)
    {
        if (empty($latitude) || empty($longitude))
            return null;

        $pointStr = "POINT($latitude $longitude)";

        return DB::raw("ST_GeomFromText('$pointStr')");
    }

    public function getGoogleMapsLink()
    {
        $latitude = $this->getLatitude();
        $longitude = $this->getLongitude();

        if (empty($latitude) || empty($longitude))
            return null;

        return "https://www.google.com/maps/search/?api=1&query=$latitude,$longitude";
    }

    public function getWazeLink()
    {
        $latitude = $this->getLatitude();
        $longitude = $this->getLongitude();

        if (empty($latitude) || empty($longitude))
            return null;

        return "https://www.waze.com/ul?ll=$latitude,$longitude&navigate=yes";
    }
}
