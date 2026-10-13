<?php

namespace App\Console\Commands;

use Aws\S3\S3Client;
use Illuminate\Console\Command;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use UnexpectedValueException;

class UploadStorageToR2 extends Command
{
    /**
     * Keys mirror the public URL (/storage/...) so paths stored in the DB stay valid.
     */
    private const KEY_PREFIX = 'storage';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:upload-to-r2
                            {--dry-run : List files that would be uploaded without uploading}
                            {--force : Upload even when the object already exists in the bucket}
                            {--source= : Local directory to upload (default: storage/app/public)}
                            {--skip-tmp : Skip the tmp/ directory under the source}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Upload every file under public storage (/storage) to a Cloudflare R2 bucket';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $source = $this->option('source') ?: storage_path('app/public');
        $source = rtrim(str_replace('\\', '/', $source), '/');

        if (!is_dir($source)) {
            $this->error("Source directory does not exist: {$source}");

            return 1;
        }

        $dryRun = (bool) $this->option('dry-run');
        $force = (bool) $this->option('force');
        $skipTmp = (bool) $this->option('skip-tmp');

        $files = $this->collectFiles($source, $skipTmp);

        if (empty($files)) {
            $this->warn('No files found to upload.');

            return 0;
        }

        $this->info(sprintf(
            '%s %d file(s) from %s to R2 bucket "%s" under "%s/"',
            $dryRun ? 'Would upload' : 'Uploading',
            count($files),
            $source,
            env('R2_BUCKET'),
            self::KEY_PREFIX
        ));

        $client = null;
        $bucket = null;

        if (!$dryRun) {
            try {
                $client = $this->makeR2Client();
                $bucket = env('R2_BUCKET');
            } catch (UnexpectedValueException $e) {
                $this->error($e->getMessage());

                return 1;
            }
        }

        $uploaded = 0;
        $skipped = 0;
        $failed = 0;

        $bar = $this->output->createProgressBar(count($files));
        $bar->start();

        foreach ($files as $absolutePath) {
            $relative = ltrim(str_replace('\\', '/', substr($absolutePath, strlen($source))), '/');
            $key = self::KEY_PREFIX . '/' . $relative;

            try {
                if ($dryRun) {
                    if ($this->output->isVerbose()) {
                        $this->line("\n  {$relative} -> {$key}");
                    }
                    $uploaded++;
                    $bar->advance();
                    continue;
                }

                if (!$force && $client->doesObjectExist($bucket, $key)) {
                    $skipped++;
                    $bar->advance();
                    continue;
                }

                $body = fopen($absolutePath, 'rb');
                if ($body === false) {
                    throw new UnexpectedValueException("Unable to open file: {$absolutePath}");
                }

                try {
                    $params = [
                        'Bucket' => $bucket,
                        'Key' => $key,
                        'Body' => $body,
                    ];

                    $mime = $this->guessMimeType($absolutePath);
                    if ($mime) {
                        $params['ContentType'] = $mime;
                    }

                    // R2 rejects S3 object ACLs; do not set ACL / visibility here.
                    $client->putObject($params);
                } finally {
                    if (is_resource($body)) {
                        fclose($body);
                    }
                }

                $uploaded++;
            } catch (\Throwable $e) {
                $failed++;
                $this->line('');
                $this->error("Failed: {$relative} — {$e->getMessage()}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->line('');
        $this->line('');

        $this->info("Done. uploaded={$uploaded} skipped={$skipped} failed={$failed}");

        return $failed > 0 ? 1 : 0;
    }

    /**
     * @return string[]
     */
    private function collectFiles(string $source, bool $skipTmp): array
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        $files = [];

        /** @var SplFileInfo $file */
        foreach ($iterator as $file) {
            if (!$file->isFile()) {
                continue;
            }

            $absolute = str_replace('\\', '/', $file->getPathname());
            $relative = ltrim(substr($absolute, strlen($source)), '/');

            if ($relative === '.gitignore') {
                continue;
            }

            if ($this->shouldSkipRelativePath($relative, $skipTmp)) {
                continue;
            }

            $files[] = $absolute;
        }

        sort($files);

        return $files;
    }

    private function makeR2Client(): S3Client
    {
        $key = env('R2_ACCESS_KEY_ID');
        $secret = env('R2_SECRET_ACCESS_KEY');
        $bucket = env('R2_BUCKET');
        $endpoint = env('R2_ENDPOINT');
        $region = env('R2_REGION', 'auto');

        $missing = [];
        foreach ([
            'R2_ACCESS_KEY_ID' => $key,
            'R2_SECRET_ACCESS_KEY' => $secret,
            'R2_BUCKET' => $bucket,
            'R2_ENDPOINT' => $endpoint,
        ] as $name => $value) {
            if (empty($value)) {
                $missing[] = $name;
            }
        }

        if (!empty($missing)) {
            throw new UnexpectedValueException(
                'Missing R2 configuration: ' . implode(', ', $missing) . '. See .env.example.'
            );
        }

        return new S3Client([
            'version' => 'latest',
            'region' => $region ?: 'auto',
            'endpoint' => $endpoint,
            'use_path_style_endpoint' => filter_var(
                env('R2_USE_PATH_STYLE_ENDPOINT', false),
                FILTER_VALIDATE_BOOLEAN
            ),
            'credentials' => [
                'key' => $key,
                'secret' => $secret,
            ],
            // This app runs PHP 7.4; the SDK only warns, but it throws unless suppressed.
            'suppress_php_deprecation_warning' => true,
        ]);
    }

    private function guessMimeType(string $path): ?string
    {
        if (function_exists('mime_content_type')) {
            $mime = @mime_content_type($path);
            if (is_string($mime) && $mime !== '') {
                return $mime;
            }
        }

        return null;
    }

    private function shouldSkipRelativePath(string $relative, bool $skipTmp): bool
    {
        $excluded = ['exports', 'user_profile_downloads'];
        if ($skipTmp) {
            $excluded[] = 'tmp';
        }

        foreach ($excluded as $folder) {
            if ($relative === $folder || strpos($relative, $folder . '/') === 0) {
                return true;
            }
        }

        return false;
    }
}
