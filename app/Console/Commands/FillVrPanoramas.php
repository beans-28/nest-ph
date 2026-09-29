<?php

namespace App\Console\Commands;

use App\Models\VrScene;
use App\Services\PanoramaFillService;
use Illuminate\Console\Command;

/**
 * Paints the soft ceiling/floor onto phone panoramas (and shrinks oversized
 * full 360 photos to a phone-safe size) for scenes that were uploaded
 * before that feature existed. New uploads get it automatically, so this only
 * needs to run once (or again with --all to redo everything).
 */
class FillVrPanoramas extends Command
{
    protected $signature = 'vr:fill-panoramas {--all : Rebuild every scene, not only ones missing a filled copy}';

    protected $description = 'Add a painted ceiling and floor to phone panoramas in VR tours';

    public function handle(PanoramaFillService $filler): int
    {
        $query = VrScene::query();

        if (! $this->option('all')) {
            $query->whereNull('filled_path');
        }

        $scenes = $query->get();

        if ($scenes->isEmpty()) {
            $this->info('Nothing to do — every phone panorama already has a filled copy.');
            return self::SUCCESS;
        }

        foreach ($scenes as $scene) {
            $filler->fill($scene);
            $scene->refresh();
            $status = $scene->filled_path ? '  done   ' : ((float) $scene->vaov >= 179 ? '  ok     ' : '  FAILED ');
            $this->line($status . "#{$scene->id} {$scene->title}");
        }

        $this->info('Finished ' . $scenes->count() . ' scene(s).');

        return self::SUCCESS;
    }
}
