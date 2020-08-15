<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ImageFix extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec("
            UPDATE clubs SET emblem = REPLACE(emblem, '/storage', '');
            UPDATE user_profiles SET picture = REPLACE(picture, '/storage', '');
            UPDATE competitions SET picture = REPLACE(picture, '/storage', '');            
            UPDATE media SET url = REPLACE(url, '/storage', '');
            UPDATE media SET thumbnail_url = REPLACE(thumbnail_url, '/storage', '');
            UPDATE players SET picture = REPLACE(picture, '/storage', '');
            UPDATE playgrounds SET picture = REPLACE(picture, '/storage', '');
            UPDATE referees SET picture = REPLACE(picture, '/storage', '');
        ");

        //User avatars copy
        DB::connection()->getPdo()->exec("
            UPDATE users u
            INNER JOIN user_profiles up on u.id = up.user_id
            SET u.avatar = up.picture;
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::connection()->getPdo()->exec("
            UPDATE clubs SET emblem = IF(emblem IS NOT NULL, CONCAT('/storage', emblem), NULL);
            UPDATE user_profiles SET picture = IF(picture IS NOT NULL, CONCAT('/storage', picture), NULL);
            UPDATE competitions SET picture = IF(picture IS NOT NULL, CONCAT('/storage', picture), NULL);
            UPDATE media SET thumbnail_url = IF(thumbnail_url IS NOT NULL, CONCAT('/storage', thumbnail_url), NULL);
            UPDATE media SET url = IF(media_type = 'image', CONCAT('/storage', url), url);
            UPDATE players SET picture = IF(picture IS NOT NULL, CONCAT('/storage', picture), NULL);
            UPDATE playgrounds SET picture = IF(picture IS NOT NULL, CONCAT('/storage', picture), NULL);
            UPDATE referees SET picture = IF(picture IS NOT NULL, CONCAT('/storage', picture), NULL);
        ");

        DB::connection()->getPdo()->exec("
            UPDATE users SET avatar = 'users/default.png';
        ");
    }
}
