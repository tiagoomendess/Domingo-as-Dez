<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Support\Str;

class Player extends SearchableModel
{
    protected $fillable = ['name', 'picture', 'association_id', 'nickname', 'phone', 'email', 'facebook_profile', 'birth_date', 'obs', 'position', 'visible'];

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
        'association_id' => [
            'name' => 'association_id',
            'type' => 'string',
            'trans' => 'Nº Associação',
            'allowSearch' => true,
            'compare' => 'like',
            'validation' => 'nullable|min:3|max:30|string'
        ],
        'nickname' => [
            'name' => 'nickname',
            'type' => 'string',
            'trans' => 'Alcunha',
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
        'position' => [
            'name' => 'position',
            'type' => 'enum',
            'trans' => 'Posição',
            'allowSearch' => true,
            'compare' => '=',
            'validation' => 'nullable|in:none,striker,midfielder,defender,goalkeeper',
            'enumItems' => [
                [
                    'name' => 'Nenhuma',
                    'value' => 'none'
                ],
                [
                    'name' => 'Avançado',
                    'value' => 'striker'
                ],
                [
                    'name' => 'Médio',
                    'value' => 'midfielder'
                ],
                [
                    'name' => 'Defesa',
                    'value' => 'defender'
                ],
                [
                    'name' => 'Guarda Redes',
                    'value' => 'goalkeeper'
                ]
            ]
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

    public function goals()
    {
        return $this->hasMany('App\Goal');
    }

    public function teams()
    {
        return $this->belongsToMany('App\Team', 'transfers');
    }

    public function transfers()
    {
        return $this->hasMany(Transfer::class);
    }

    public function getTeam()
    {

        $last_transfer = $this->getLastTransfer();

        if ($last_transfer != null)
            return $last_transfer->team;
        else
            return null;
    }

    public function getClub()
    {

        $team = $this->getTeam();

        if ($team)
            return $team->club;
        else
            return null;

    }

    public function getLastTransfer()
    {

        return Transfer::where('player_id', $this->id)->orderBy('date', 'desc')->first();
    }

    public function getPreviousTeam()
    {

        $transfers = Transfer::where('player_id', $this->id)->orderBy('date', 'desc')->limit(2)->get();

        if ($transfers->count() < 2)
            return null;

        if ($transfers->last()->team)
            return $transfers->last()->team;
        else
            return null;

    }

    /**
     * Gets the emblem of the club if it has one, or the default icon
     */
    public function getPicture()
    {
        if ($this->picture)
            return $this->picture;
        else
            return config('custom.default_profile_pic');
    }

    /**
     * Gets the picture if the player has more than 18 years old, or the default icon if not
     */
    public function getAgeSafePicture()
    {
        if (has_permission('players')) {
            return $this->getPicture();
        }

        if ($this->getAge() >= 18)
            return $this->getPicture();
        else
            return config('custom.default_profile_pic');
    }

    /**
     * Gets the name and nickname if exists
     */
    public function displayName()
    {
        if ($this->nickname)
            return $this->name . ' (' . $this->nickname . ')';
        else
            return $this->name;
    }

    public function getPublicURL()
    {
        return route('front.player.show', ['id' => $this->id, 'name_slug' => Str::slug($this->name)]);
    }

    public function getAge()
    {
        if (!$this->birth_date)
            return null;

        $now = Carbon::now();
        $birth = Carbon::createFromFormat("Y-m-d H:i:s", $this->birth_date);

        $age = $now->year - $birth->year;

        if ($now->month < $birth->month) {
            $age--;
        } else if ($now->month == $birth->month) {
            if ($now->day < $birth->day)
                $age--;
        }

        return $age;
    }
}
