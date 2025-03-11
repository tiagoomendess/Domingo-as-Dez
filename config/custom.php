<?php

return [
    'author' => 'Tiago Mendes',
    'author_website' => 'http://mendes.com.pt',
    'site_name' => 'Domingo às Dez',
    'site_domain' => 'domingoasdez.com',
    'site_url' => 'http://domingoasdez.com',
    'site_email' => 'geral@domingoasdez.com',
    'register_enable' => true,
    'login_enable' => true,
    'social_logins' => true,
    'default_profile_pic' => '/images/default-profile.png',
    'site_logo' => '/images/domingo.png',
    'results_per_page' => 25,
    'default_emblem' => '/images/default-emblem.png',
    'media_images_folder' => '/storage/media/images',
    'media_videos_folder' => '/storage/media/videos',
    'media_other_folder' => '/storage/media/files',
    'media_thumbnails' => '/storage/media/thumbnails',
    'dashboard_sidenav_image' => '/images/sidenav_image.jpg',
    'site_sidenav_image' => '/images/sidenav_image.jpg',
    'site_sidenav_image_no_login' => '/images/sidenav_image_blur.jpg',
    'user_avatars_path' => '/storage/uploads/users/avatars',
    'send_exception_to_mail' => env('SEND_EXCEPTION_TO_MAIL', 'false'),
    'exception_notification_email' => env('EXCEPTION_NOTIFICATION_EMAIL', 'tiagoomendess@gmail.com')
];