<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Makes uploaded photos smaller before they are saved, so pages load fast on
 * mobile data. A phone photo is often 4-7 MB; after this it is usually
 * 200-600 KB and still looks sharp on screen.
 *
 * - Shrinks anything wider than $maxWidth (keeps the shape).
 * - Re-saves as JPEG at good quality (PNGs with see-through parts stay PNG).
 * - Turns phone photos the right way up (phones store "rotate me" as a tag,
 *   which is lost when the image is re-saved).
 * - If anything goes wrong, or the image is too big for server memory, the
 *   original file is saved unchanged, so an upload never fails because of this.
 */
class ImageShrinker
{
    /** Store an uploaded image in $dir on the public disk. Returns the path. */
    public static function store(UploadedFile $file, string $dir, int $maxWidth = 1920, int $quality = 82): string
    {
        $shrunk = self::shrink((string) file_get_contents($file->getRealPath()), $maxWidth, $quality, $file->getRealPath());

        if ($shrunk === null) {
            return $file->store($dir, 'public');
        }

        [$bytes, $ext] = $shrunk;
        $path = trim($dir, '/') . '/' . Str::random(40) . '.' . $ext;
        Storage::disk('public')->put($path, $bytes);

        return $path;
    }

    /**
     * Returns [bytes, extension] for a smaller copy, or null to keep the
     * original (not an image, already small, or too big to process safely).
     */
    public static function shrink(string $bytes, int $maxWidth = 1920, int $quality = 82, ?string $pathForExif = null): ?array
    {
        $info = @getimagesizefromstring($bytes);
        if (! $info || ! in_array($info[2], [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_WEBP], true)) {
            return null;
        }
        [$w, $h] = $info;

        $orientation = self::orientation($bytes, $info[2], $pathForExif);
        $tooWide = $w > $maxWidth;
        $tooHeavy = strlen($bytes) > 600 * 1024;
        if (! $tooWide && ! $tooHeavy && $orientation <= 1) {
            return null; // already fine
        }

        // GD needs about 5 bytes per pixel (plus the smaller copy). Skip if
        // that would risk running out of memory, rather than crash the upload.
        if (! self::fitsInMemory($w * $h * 5 * 1.6)) {
            return null;
        }

        try {
            $image = @imagecreatefromstring($bytes);
            if (! $image) {
                return null;
            }

            $image = self::rotate($image, $orientation);

            if (imagesx($image) > $maxWidth) {
                $newH = (int) round($maxWidth * imagesy($image) / imagesx($image));
                $scaled = imagescale($image, $maxWidth, $newH, IMG_BICUBIC_FIXED);
                imagedestroy($image);
                $image = $scaled;
            }

            $keepPng = $info[2] === IMAGETYPE_PNG && self::hasTransparency($image);

            ob_start();
            if ($keepPng) {
                imagesavealpha($image, true);
                imagepng($image, null, 8);
            } else {
                imageinterlace($image, true); // shows a blurry preview while loading
                imagejpeg($image, null, $quality);
            }
            $out = ob_get_clean();
            imagedestroy($image);

            // Only use the new copy if it is actually smaller (or needed rotating).
            if ($out === '' || ($orientation <= 1 && strlen($out) >= strlen($bytes))) {
                return null;
            }

            return [$out, $keepPng ? 'png' : 'jpg'];
        } catch (\Throwable $e) {
            report($e);

            return null;
        }
    }

    private static function orientation(string $bytes, int $type, ?string $path): int
    {
        if ($type !== IMAGETYPE_JPEG || ! function_exists('exif_read_data')) {
            return 1;
        }
        $source = $path ?: 'data://image/jpeg;base64,' . base64_encode($bytes);
        $exif = @exif_read_data($source);

        return (int) ($exif['Orientation'] ?? 1);
    }

    private static function rotate(\GdImage $image, int $orientation): \GdImage
    {
        $rotated = match ($orientation) {
            3 => imagerotate($image, 180, 0),
            6 => imagerotate($image, -90, 0),
            8 => imagerotate($image, 90, 0),
            default => null,
        };
        if ($rotated) {
            imagedestroy($image);

            return $rotated;
        }

        return $image;
    }

    private static function hasTransparency(\GdImage $image): bool
    {
        // Check a grid of sample points instead of every pixel (fast).
        $w = imagesx($image);
        $h = imagesy($image);
        for ($x = 0; $x < $w; $x += max(1, intdiv($w, 40))) {
            for ($y = 0; $y < $h; $y += max(1, intdiv($h, 40))) {
                if ((imagecolorat($image, $x, $y) >> 24) & 0x7F) {
                    return true;
                }
            }
        }

        return false;
    }

    private static function fitsInMemory(float $needed): bool
    {
        $limit = ini_get('memory_limit');
        if ($limit === false || $limit === '' || $limit === '-1') {
            return true;
        }
        $num = (float) $limit;
        $bytes = match (strtolower(substr($limit, -1))) {
            'g' => $num * 1024 ** 3,
            'm' => $num * 1024 ** 2,
            'k' => $num * 1024,
            default => $num,
        };

        return memory_get_usage(true) + $needed < $bytes * 0.85;
    }
}
