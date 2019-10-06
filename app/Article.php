<?php

namespace App;

use Carbon\Carbon;

class Article extends SearchableModel
{

    /**
    protected $fillable = [];

    protected $guarded = [];

    protected $hidden = [];
    **/

    protected $fillable = ['media_id', 'title', 'description', 'text', 'user_id', 'date', 'tags', 'visible'];

    protected $guarded = [];

    protected $hidden = [];

    public const SEARCH_FIELDS = [
        'title' => [
            'name' => 'title',
            'type' => 'string',
            'trans' => 'Titulo',
            'allowSearch' => true,
            'compare' => 'like',
            'validation' => 'nullable|min:3|max:128|string'
        ],
        'tags' => [
            'name' => 'tags',
            'type' => 'string',
            'trans' => 'Tags',
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
        'date' => [
            'name' => 'date',
            'type' => 'date',
            'trans' => 'Data de Publicação',
            'allowSearch' => false
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

    public function user() {
        return $this->belongsTo('App\User');
    }

    public function  media() {
        return $this->belongsTo(Media::class);
    }

    /**
     * Gets the public url for this article
     *
     * @return string
    */
    public function getPublicUrl() {

        $slug = str_slug($this->title);
        $carbon = Carbon::createFromFormat("Y-m-d H:i:s", $this->date);

        return route('news.show', [
            'year' => $carbon->format("Y"),
            'month' => $carbon->format("m"),
            'day' => $carbon->format("d"),
            'slug' => $slug,
        ]);
    }

    public function getThumbnail() {

        if (!is_null($this->media)) {
            if (!is_null($this->thumbnail_url))
                return $this->thumbnail_url;
            else
                $this->media->generateThumbnail();
                return $this->media->thumbnail_url;
        } else
            return Media::getPlaceholder('16:9', $this->id);
    }
}
