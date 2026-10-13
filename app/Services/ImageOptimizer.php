<?php

namespace App\Services;

use Intervention\Image\Image;

/**
 * Rules for every image the site saves: cap the size, fix phone rotation and pick a compact format.
 * Re-encoding with GD also drops EXIF metadata, so uploaded photos lose their GPS location.
 */
class ImageOptimizer
{
    /** Longest side of content images: multimedia, playgrounds, partners, pages, CKEditor, logos. */
    const CONTENT_MAX_SIZE = 2048;

    /** Players, referees, team agents and club emblems. Social-media images draw them at up to 750px. */
    const SQUARE_SIZE = 800;

    /** User avatars only appear small. */
    const AVATAR_SIZE = 400;

    /** Uploaded photos. */
    const JPEG_QUALITY = 82;

    /** Images the site generates itself: thumbnails, game and poll images. */
    const GENERATED_QUALITY = 80;

    /** GD would lose the animation or cannot read the format, so these are stored as uploaded. */
    const KEEP_ORIGINAL = ['gif', 'svg'];

    /**
     * Applies the EXIF rotation phone cameras use instead of rotating the pixels.
     * Only for JPEG files: for anything else Intervention re-encodes the image to JPEG just to look for EXIF,
     * which also marks it as a JPEG and hides its transparency.
     */
    public static function orientate(Image $image): Image
    {
        if ($image->mime() === 'image/jpeg' && $image->dirname && function_exists('exif_read_data')) {
            try {
                $image->orientate();
            } catch (\Throwable $e) {
                // No readable EXIF data: keep the image as it is
            }
        }

        return $image;
    }

    /**
     * Fits the image inside a $max x $max box, keeping its proportions and never enlarging it.
     */
    public static function limitSize(Image $image, int $max = self::CONTENT_MAX_SIZE): Image
    {
        return $image->resize($max, $max, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
    }

    /**
     * Encodes the image under a new /storage URL and returns it.
     * $format null keeps PNG only for images that use transparency (logos); everything else becomes JPEG.
     */
    public static function store(Image $image, string $folderUrl, ?string $format = null, int $quality = self::JPEG_QUALITY): string
    {
        $format = $format ?: (static::hasTransparency($image) ? 'png' : 'jpg');

        if ($format === 'jpg') {
            $image = static::flatten($image);
            // Progressive JPEGs are slightly smaller and show a preview while loading
            $image->interlace();
        }

        $url = UploadStorage::newUrl($folderUrl, $format);
        UploadStorage::putImage($image, $url, $quality);

        return $url;
    }

    public static function hasTransparency(Image $image): bool
    {
        if ($image->mime() === 'image/jpeg') {
            return false;
        }

        // Resizing averages alpha into the remaining pixels, so a 64x64 copy still shows any transparency
        $core = (clone $image)->resize(64, 64)->getCore();
        for ($x = 0; $x < 64; $x++) {
            for ($y = 0; $y < 64; $y++) {
                if ((imagecolorat($core, $x, $y) >> 24) & 0x7F) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * JPEG has no transparency and GD turns transparent pixels black, so put them on white first.
     */
    private static function flatten(Image $image): Image
    {
        if ($image->mime() === 'image/jpeg') {
            return $image;
        }

        return $image->getDriver()
            ->newImage($image->width(), $image->height(), '#ffffff')
            ->insert($image);
    }
}
