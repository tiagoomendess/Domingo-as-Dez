<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Image;
use InvalidArgumentException;

/**
 * Stores every user/generated file that lives under /storage/...
 *
 * Callers always pass the URL stored in the DB (e.g. /storage/media/images/x.jpg).
 * config('filesystems.uploads') decides where it goes:
 *  - "public": storage/app/public/media/images/x.jpg (served via the public/storage symlink)
 *  - "r2":     R2 key storage/media/images/x.jpg
 */
class UploadStorage
{
    const URL_PREFIX = '/storage/';

    /**
     * Social-media images in here keep a readable name that can be written again, so they are only cached briefly.
     * Everything else is named by newUrl(), never changes, and can be cached for a year.
     */
    const TMP_FOLDER = '/storage/tmp/';
    const CACHE_IMMUTABLE = 'public, max-age=31536000, immutable';
    const CACHE_SHORT = 'public, max-age=3600';

    public static function usingR2(): bool
    {
        return config('filesystems.uploads') === 'r2';
    }

    /**
     * A new, never reused URL in a /storage/... folder, e.g. /storage/media/images/<uuid>.jpg.
     */
    public static function newUrl(string $folderUrl, string $extension): string
    {
        return rtrim($folderUrl, '/') . '/' . Str::uuid() . '.' . strtolower($extension);
    }

    public static function cacheControlFor(string $url): string
    {
        return strpos($url, self::TMP_FOLDER) === 0 ? self::CACHE_SHORT : self::CACHE_IMMUTABLE;
    }

    public static function put(string $url, $contents): void
    {
        static::disk()->put(static::path($url), $contents, static::writeOptions($url));
        static::forgetLocalCopy($url);
    }

    public static function putUploadedFile(string $url, UploadedFile $file): void
    {
        $path = static::path($url);
        static::disk()->putFileAs(dirname($path), $file, basename($path), static::writeOptions($url));
        static::forgetLocalCopy($url);
    }

    public static function putImage(Image $image, string $url, ?int $quality = null): void
    {
        $format = strtolower(pathinfo($url, PATHINFO_EXTENSION)) ?: 'jpg';

        static::put($url, (string) $image->encode($format, $quality));
    }

    public static function get(string $url): string
    {
        return static::disk()->get(static::path($url));
    }

    public static function exists(string $url): bool
    {
        return static::disk()->exists(static::path($url));
    }

    /**
     * Never throws: a missing file must not break the flow that deletes it.
     */
    public static function delete(?string $url): void
    {
        if (!static::isStored($url)) {
            return;
        }

        try {
            static::disk()->delete(static::path($url));
            static::forgetLocalCopy($url);
        } catch (\Throwable $e) {
            Log::warning("UploadStorage: failed to delete $url: " . $e->getMessage());
        }
    }

    /**
     * Absolute URL that third parties (Facebook, Instagram) can fetch.
     */
    public static function publicUrl(string $url): string
    {
        if (static::usingR2()) {
            return static::disk()->url(static::path($url));
        }

        return asset(ltrim($url, '/'));
    }

    /**
     * Something Intervention's Image::make() / ->insert() accepts: a URL, a local path or raw bytes.
     */
    public static function source(?string $url)
    {
        if (preg_match('#^https?://#i', (string) $url)) {
            return $url;
        }

        if (!static::isStored($url)) {
            return public_path(ltrim((string) $url, '/'));
        }

        $localPath = static::localPath($url);

        if (!static::usingR2() || is_file($localPath)) {
            return $localPath;
        }

        return static::get($url);
    }

    /**
     * URLs of the files directly inside a /storage/... folder.
     *
     * @return string[]
     */
    public static function files(string $dirUrl): array
    {
        $prefix = static::usingR2() ? 'storage/' : '';

        return array_map(function ($path) use ($prefix) {
            return self::URL_PREFIX . substr($path, strlen($prefix));
        }, static::disk()->files(static::path($dirUrl)));
    }

    public static function lastModified(string $url): int
    {
        return static::disk()->lastModified(static::path($url));
    }

    public static function isStored(?string $url): bool
    {
        return is_string($url) && strpos($url, self::URL_PREFIX) === 0;
    }

    /**
     * Sent as object metadata on R2; the local disk ignores it.
     */
    private static function writeOptions(string $url): array
    {
        return ['CacheControl' => static::cacheControlFor($url)];
    }

    private static function disk()
    {
        return Storage::disk(static::usingR2() ? 'r2' : 'public');
    }

    /**
     * /storage/media/x.jpg -> "media/x.jpg" on the public disk, "storage/media/x.jpg" on R2.
     */
    private static function path(string $url): string
    {
        if (!static::isStored($url)) {
            throw new InvalidArgumentException("Not an uploads URL: $url");
        }

        $relative = rtrim(substr($url, strlen(self::URL_PREFIX)), '/');

        return static::usingR2() ? 'storage/' . $relative : $relative;
    }

    /**
     * On R2, a leftover migrated copy would shadow the new file in nginx's try_files and in source().
     */
    private static function forgetLocalCopy(string $url): void
    {
        if (!static::usingR2()) {
            return;
        }

        $localPath = static::localPath($url);
        if (is_file($localPath)) {
            @unlink($localPath);
        }
    }

    /**
     * Where a /storage/... URL lives on local disk (public/storage is a symlink to it).
     */
    private static function localPath(string $url): string
    {
        return storage_path('app/public/' . substr($url, strlen(self::URL_PREFIX)));
    }
}
