<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

/**
 * Copies every file in the local public disk (storage/app/public) up to the
 * Laravel Cloud bucket, keeping the same paths so the database still points
 * at the right files. Run it on your laptop after putting the bucket's
 * AWS_* credentials in your local .env (they fill the "s3" disk).
 */
class PushStorageToCloud extends Command
{
    protected $signature = 'storage:push-to-cloud {--overwrite : Re-upload files that are already in the bucket}';

    protected $description = 'Upload storage/app/public to the Laravel Cloud bucket';

    public function handle(): int
    {
        if (! config('filesystems.disks.s3.bucket') || ! config('filesystems.disks.s3.key')) {
            $this->error('Bucket credentials missing. Fill AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY, AWS_DEFAULT_REGION, AWS_BUCKET, AWS_ENDPOINT and AWS_URL in .env first.');

            return self::FAILURE;
        }

        $local = Storage::disk('public');
        $cloud = Storage::disk('s3');
        $files = array_filter($local->allFiles(), fn ($path) => ! str_starts_with(basename($path), '.'));

        $this->info('Uploading '.count($files).' files...');
        $uploaded = $skipped = $failed = 0;

        $this->withProgressBar($files, function ($path) use ($local, $cloud, &$uploaded, &$skipped, &$failed) {
            if (! $this->option('overwrite') && $cloud->exists($path)) {
                $skipped++;

                return;
            }

            $stream = $local->readStream($path);
            $ok = $cloud->writeStream($path, $stream, ['visibility' => 'public']);
            if (is_resource($stream)) {
                fclose($stream);
            }

            $ok ? $uploaded++ : $failed++;
        });

        $this->newLine(2);
        $this->info("Done. Uploaded: {$uploaded}, already there: {$skipped}, failed: {$failed}.");

        return $failed ? self::FAILURE : self::SUCCESS;
    }
}
