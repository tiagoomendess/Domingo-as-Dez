<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $fillable = ['user_id', 'url', 'media_type', 'tags', 'visible'];

    protected $guarded = [];

    protected $hidden = [];

    public function  user() {
        return $this->belongsTo('App\User');
    }

    public function  articles() {
        return $this->hasMany(Article::class);
    }

    public function publicUrl() {
        return str_replace('media', 'app_media', $this->url);
    }

    /**
     *
    <?php
    $str = (string) $article->id;
    $arr = str_split($str); // convert string to an array
    ?>
    <img src="{{ "/images/16_9_placeholder_" . end($arr) . ".jpg" }}" alt="">
    */

    /**
     * Gets a random image placeholder
     *
     * @param $ratio string
     * @param $id int
     *
     * @return string
    */
    public static function getPlaceholder($ratio, $id = null) {

        if(!$id)
            $img_id = (string) random_int(0, 9);
        else {
            $str = (string) $id;
            $arr = str_split($str); // convert string to an array
            $img_id = (string) end($arr);
        }

        $url = null;

        switch ($ratio) {

            case '16:9':
            case '16_9':
                $url = '/images/16_9_placeholder_' . $img_id . '.jpg';
                break;

            case '1:1':
            case '1_1':
                $url = '/images/1_1_placeholder_' . $img_id . '.jpg';
                break;

            default:
                $url = '/images/1_1_placeholder_' . $img_id . '.jpg';
                break;

        }

        return $url;
    }
}
