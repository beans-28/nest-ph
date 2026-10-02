<?php

namespace App\Services;

use App\Models\VrScene;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Turns a phone panorama (a flat band with nothing above or below it) into an
 * image that covers the full up-down range, so visitors can look at the
 * ceiling and floor without hitting black.
 *
 * The missing ceiling/floor are "painted" from the photo's own top and bottom
 * edges: the edge colours are stretched outward and fade into one average
 * colour at the very top and bottom. It's soft and blurry by design — it
 * suggests a ceiling and floor rather than inventing details that aren't real.
 *
 * The original upload is never touched; the filled copy is a separate file
 * (filled_path) that only the public viewer uses.
 */
class PanoramaFillService
{
    /**
     * Largest side of the filled image. Many phones can't show a panorama
     * texture wider than 4096px, so staying under this keeps tours working on
     * mobile.
     */
    private const MAX_SIDE = 4096;

    /** How many rows at the photo's edge get blended into the painted area. */
    private const FEATHER_RATIO = 0.08;

    /**
     * Builds (or rebuilds) the filled copy for a scene and saves its path.
     * Full 360 photos (e.g. from the 360 Photo Cam app) need no painting, but
     * if they're wider than phones can display they get a shrunk copy
     * instead. Failures are logged and ignored — the
     * viewer then falls back to the old "keep the camera inside the band"
     * behaviour, so a tour is never broken by this step.
     */
    public function fill(VrScene $scene): void
    {
        $this->deleteFilled($scene);

        try {
            $path = (float) $scene->vaov >= 179
                ? $this->shrinkIfTooBig($scene)
                : $this->render($scene);
            $scene->update(['filled_path' => $path]);
        } catch (\Throwable $e) {
            Log::warning('Panorama fill failed for scene ' . $scene->id . ': ' . $e->getMessage());
            $scene->update(['filled_path' => null]);
        }
    }

    public function deleteFilled(VrScene $scene): void
    {
        if ($scene->filled_path) {
            Storage::disk('public')->delete($scene->filled_path);
        }
    }

    /**
     * Returns a path to a phone-safe copy of a full 360 photo, or null if the
     * original is already small enough to show as-is.
     */
    private function shrinkIfTooBig(VrScene $scene): ?string
    {
        // Read through the disk (not a file path) so this works when uploads
        // live in cloud storage instead of on the server.
        $bytes = Storage::disk('public')->get($scene->panorama_path);
        $size = $bytes ? @getimagesizefromstring($bytes) : false;

        if (! $size || $size[0] <= self::MAX_SIDE) {
            return null;
        }

        $photo = @imagecreatefromstring($bytes);
        if (! $photo) {
            throw new \RuntimeException('Could not read the panorama image.');
        }

        $small = imagescale($photo, self::MAX_SIDE, (int) round(self::MAX_SIDE * $size[1] / $size[0]), IMG_BICUBIC_FIXED);

        return $this->saveJpeg($small, 88);
    }

    private function render(VrScene $scene): string
    {
        $bytes = Storage::disk('public')->get($scene->panorama_path);
        $photo = $bytes ? @imagecreatefromstring($bytes) : false;

        if (! $photo) {
            throw new \RuntimeException('Could not read the panorama image.');
        }

        $vaov = max(1.0, (float) $scene->vaov);
        $vOffset = (float) $scene->v_offset;

        // The finished image spans the full 180° up-down, so it is taller than
        // the photo by 180 / vaov. Scale everything down to fit MAX_SIDE.
        $srcW = imagesx($photo);
        $srcH = imagesy($photo);
        $fullH = $srcH * 180 / $vaov;
        $scale = min(1, self::MAX_SIDE / $srcW, self::MAX_SIDE / $fullH);

        $outW = max(1, (int) round($srcW * $scale));
        $outH = max(2, (int) round($fullH * $scale));
        $bandH = max(1, (int) round($srcH * $scale));

        // Where the photo sits vertically: its centre is at pitch = v_offset,
        // and the top of the image is pitch +90°.
        $bandTop = (int) round((90 - ($vOffset + $vaov / 2)) / 180 * $outH);
        $bandTop = max(0, min($outH - $bandH, $bandTop));
        $bandBottom = $bandTop + $bandH;

        $out = imagecreatetruecolor($outW, $outH);
        imagecopyresampled($out, $photo, 0, $bandTop, 0, 0, $outW, $bandH, $srcW, $srcH);

        $feather = max(2, (int) round($bandH * self::FEATHER_RATIO));

        // Ceiling: sample a strip just inside the photo's top edge.
        $this->paintCap($out, $bandTop, $bandTop, $feather, true);
        // Floor: sample a strip just inside the photo's bottom edge.
        $this->paintCap($out, $bandBottom, $outH - $bandBottom, $feather, false);

        return $this->saveJpeg($out, 85);
    }

    /**
     * Saves an image as a JPEG on the public disk and returns its path. The
     * JPEG is built in memory first so it works with cloud storage too.
     */
    private function saveJpeg(\GdImage $image, int $quality): string
    {
        ob_start();
        imagejpeg($image, null, $quality);
        $jpeg = ob_get_clean();

        $path = 'vr-scenes/filled/' . Str::uuid() . '.jpg';
        Storage::disk('public')->put($path, $jpeg);

        return $path;
    }

    /**
     * Paints the empty area above (or below) the photo.
     *
     * $edgeY is the photo's edge row, $capH is how tall the empty area is.
     * The work is done on a small low-resolution image and then stretched up,
     * which both keeps it fast and makes it naturally soft.
     */
    private function paintCap(\GdImage $out, int $edgeY, int $capH, int $feather, bool $isTop): void
    {
        if ($capH <= 0) {
            return;
        }

        $outW = imagesx($out);

        // 1. Average the edge strip into a short row of colours (this is the
        //    blur), then smooth neighbours together so no single object
        //    (a TV, a door) makes a streak.
        $cols = 128;
        $stripH = max(1, $feather * 2);
        $stripY = $isTop ? $edgeY : max(0, $edgeY - $stripH);
        $strip = imagecreatetruecolor($cols, 1);
        imagecopyresampled($strip, $out, 0, 0, 0, $stripY, $cols, 1, $outW, $stripH);

        $raw = [];
        for ($x = 0; $x < $cols; $x++) {
            $c = imagecolorat($strip, $x, 0);
            $raw[$x] = [($c >> 16) & 0xFF, ($c >> 8) & 0xFF, $c & 0xFF];
        }

        $radius = 4;
        $edge = [];
        $sum = [0, 0, 0];
        for ($x = 0; $x < $cols; $x++) {
            $acc = [0, 0, 0];
            for ($k = -$radius; $k <= $radius; $k++) {
                $n = $raw[($x + $k + $cols) % $cols]; // wraps around the circle
                $acc[0] += $n[0];
                $acc[1] += $n[1];
                $acc[2] += $n[2];
            }
            $span = 2 * $radius + 1;
            $edge[$x] = [$acc[0] / $span, $acc[1] / $span, $acc[2] / $span];
            $sum[0] += $edge[$x][0];
            $sum[1] += $edge[$x][1];
            $sum[2] += $edge[$x][2];
        }
        $avg = [$sum[0] / $cols, $sum[1] / $cols, $sum[2] / $cols];

        // 2. Build a smaller cap: edge colours near the photo, fading to the
        //    average colour at the very top/bottom (where all directions meet).
        //    Colours between samples are blended so there are no hard steps.
        $capW = min($outW, 512);
        $rows = 64;
        $cap = imagecreatetruecolor($capW, $rows);
        for ($x = 0; $x < $capW; $x++) {
            $pos = ($x + 0.5) / $capW * $cols - 0.5;
            $i0 = (int) floor($pos);
            $f = $pos - $i0;
            $c0 = $edge[($i0 + $cols) % $cols];
            $c1 = $edge[($i0 + 1) % $cols];
            $col = [
                $c0[0] + ($c1[0] - $c0[0]) * $f,
                $c0[1] + ($c1[1] - $c0[1]) * $f,
                $c0[2] + ($c1[2] - $c0[2]) * $f,
            ];

            for ($y = 0; $y < $rows; $y++) {
                // t = 1 next to the photo, 0 at the pole.
                $t = $isTop ? ($y + 0.5) / $rows : 1 - ($y + 0.5) / $rows;
                $t = $t * $t; // edge colour fades out quickly, so the pole looks calm
                $r = (int) round($avg[0] + ($col[0] - $avg[0]) * $t);
                $g = (int) round($avg[1] + ($col[1] - $avg[1]) * $t);
                $b = (int) round($avg[2] + ($col[2] - $avg[2]) * $t);
                imagesetpixel($cap, $x, $y, ($r << 16) | ($g << 8) | $b);
            }
        }

        // 3. Enlarge it into place with smooth (bilinear) blending.
        $big = imagescale($cap, $outW, $capH, IMG_BILINEAR_FIXED);
        imagecopy($out, $big, 0, $isTop ? 0 : $edgeY, 0, 0, $outW, $capH);

        // 4. Soften the seam: fade the painted colour a little way into the
        //    photo, strongest right at the edge.
        $bandRow = imagecreatetruecolor($outW, 1);
        for ($i = 0; $i < $feather; $i++) {
            $pct = (int) round(90 * (1 - ($i + 0.5) / $feather));
            if ($pct <= 0) {
                continue;
            }
            // The painted row touching the photo is the colour we fade in.
            $srcY = $isTop ? $edgeY - 1 : $edgeY;
            if ($srcY < 0 || $srcY >= imagesy($out)) {
                break;
            }
            $dstY = $isTop ? $edgeY + $i : $edgeY - 1 - $i;
            imagecopy($bandRow, $out, 0, 0, 0, $srcY, $outW, 1);
            imagecopymerge($out, $bandRow, 0, $dstY, 0, 0, $outW, 1, $pct);
        }
    }
}
