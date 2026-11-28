<?php

namespace App\Console\Commands;

use App\Models\RoomPhoto;
use App\Support\ImageShrinker;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Shrinks room photos that were uploaded before uploads were shrunk
 * automatically. Safe to run more than once: photos that are already small
 * are skipped. Run on Laravel Cloud from the Commands tab:
 *   php artisan images:shrink-room-photos
 */
class ShrinkRoomPhotos extends Command
{
    protected $signature = 'images:shrink-room-photos';

    protected $description = 'Make existing room photos smaller so room pages load faster';

    public function handle(): int
    {
        $disk = Storage::disk('public');
        $saved = 0;

        foreach (RoomPhoto::all() as $photo) {
            if (! $photo->path || ! $disk->exists($photo->path)) {
                $this->line("  missing  #{$photo->id} {$photo->path}");
                continue;
            }

            $before = $disk->get($photo->path);
            $result = ImageShrinker::shrink($before, 1920);

            if ($result === null) {
                $this->line("  ok       #{$photo->id} (already small)");
                continue;
            }

            [$bytes, $ext] = $result;
            $newPath = 'room-photos/' . Str::random(40) . '.' . $ext;
            $disk->put($newPath, $bytes);

            $old = $photo->path;
            $photo->update(['path' => $newPath]);
            $disk->delete($old);

            $saved += strlen($before) - strlen($bytes);
            $this->line(sprintf('  shrunk   #%d %s → %s', $photo->id, self::mb(strlen($before)), self::mb(strlen($bytes))));
        }

        $this->info('Done. Saved ' . self::mb($saved) . ' in total.');

        return self::SUCCESS;
    }

    private static function mb(int $bytes): string
    {
        return number_format($bytes / 1048576, 2) . ' MB';
    }
}
