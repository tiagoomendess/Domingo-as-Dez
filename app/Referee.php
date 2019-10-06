<?php

namespace App;


class Referee extends SearchableModel
{
    protected $fillable = ['name', 'picture', 'association', 'obs'];

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

    public function games() {
        return $this->belongsToMany(Game::class, 'game_referees');
    }

    /**
     * Gets the emblem of the club if it has one, or the default icon
     */
    public function getPicture() {
        if($this->picture)
            return $this->picture;
        else
            return config('custom.default_profile_pic');
    }

    public function getPublicURL() {
        return route('front.referee.show', ['id' => $this->id, 'name_slug' => str_slug($this->name)]);
    }

}
