<?php

namespace App;


use Illuminate\Support\Facades\Storage;
use TCG\Voyager\Facades\Voyager;

class Club extends SearchableModel
{
    protected $fillable = ['name', 'emblem', 'website', 'visible'];

    protected $guarded = [];

    protected $hidden = [];

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

    public function playgrounds()
    {
        return $this->hasMany('App\Playground');
    }

    public function teams()
    {
        return $this->hasMany('App\Team');
    }

    /**
     * Gets the emblem of the club if it has one, or the default icon
     */
    public function getEmblem()
    {
        if ($this->emblem)
            return Voyager::image($this->emblem);
        else
            return config('custom.default_emblem');
    }

    public function getFirstPlayground()
    {

        return $this->playgrounds->first();

    }

    public static function findByNameSlug($slug)
    {
        $clubs = Club::all();

        foreach ($clubs as $club) {
            if (str_slug($club->name) == $slug)
                return $club;
        }

        return null;
    }

    public function getPublicURL()
    {
        return route('front.club.show', ['club_slug' => str_slug($this->name)]);
    }
}
