<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Intervention\Image\Facades\Image;

class Media extends Model
{
    protected $fillable = ['user_id', 'url', 'thumbnail_url', 'media_type', 'tags', 'visible'];

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

    public function generateThumbnail() {

        if (!is_null($this->thumbnail_url))
            return false;

        switch ($this->media_type) {
            case "image":
                $img = Image::make(public_path($this->url));
                break;
            case "youtube":
                $play_btn = Image::make(public_path('/images/play_button.png'));

                preg_match("/watch\?v\=[a-zA-z0-9\-\_]+/", $this->url,$matches);

                if (count($matches) < 1)
                    return false;

                $video_id = str_replace("watch?v=", "", $matches[0]);
                $img = Image::make("https://img.youtube.com/vi/" . $video_id ."/maxresdefault.jpg");
                $img->insert($play_btn, 'center');

                break;

            default:

                $img = Image::canvas(900,900, "#107db7");

                preg_match('/[a-zA-z0-9]{3}$/', $this->url, $matches);
                $name = count($matches) > 0 ? $matches[0] : str_random(3);

                $img = $img->text(strtoupper($name), 400, 400, function($font) {
                    $font->file(public_path('/Roboto-Black.ttf'));
                    $font->size(400);
                    $font->color('#ffffff');
                    $font->align('center');
                    $font->valign('center');
                });

                break;
        }

        $img->resize(900, null, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        $path = config('custom.media_thumbnails') . '/' . $this->id . '.jpg';
        $img->save(public_path($path));
        $this->thumbnail_url = $path;
        $this->save();

        return true;

    }
}
