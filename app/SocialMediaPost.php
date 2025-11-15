<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SocialMediaPost extends Model {
    
    public const PLATFORM_FACEBOOK = 'facebook';
    public const PLATFORM_INSTAGRAM = 'instagram';

    public const POST_TYPE_POST = 'post';
    public const POST_TYPE_STORY = 'story';

    public const POST_CONTENT_TYPE_TEXT = 'text';
    public const POST_CONTENT_TYPE_IMAGE = 'image';
    public const POST_CONTENT_TYPE_VIDEO = 'video';

    public const POST_CONTENT_TYPES = [
        self::POST_CONTENT_TYPE_TEXT,
        self::POST_CONTENT_TYPE_IMAGE,
        self::POST_CONTENT_TYPE_VIDEO,
    ];

    public const POST_TYPES = [
        self::POST_TYPE_POST,
        self::POST_TYPE_STORY,
    ];

    public const PLATFORMS = [
        self::PLATFORM_FACEBOOK,
        self::PLATFORM_INSTAGRAM,
    ];

    protected $fillable = [
        'published',
        'publish_at',
        'platform', // facebook or instagram
        'post_type', // post, story, reel
        'post_content_type', // text, image, video
        'text_content',
        'media_path',
        'error_message',
        'media_external_id',
        'post_id',
    ];

    protected $table = 'social_media_posts';
}