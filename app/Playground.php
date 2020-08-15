<?php

namespace App;

use TCG\Voyager\Facades\Voyager;

class Playground extends SearchableModel
{
    protected $fillable = ['club_id', 'name', 'surface', 'width', 'height', 'capacity', 'picture', 'visible'];

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
            return Voyager::image($this->picture);
        else
            return Media::getPlaceholder('16:9', $this->id);
    }
}
