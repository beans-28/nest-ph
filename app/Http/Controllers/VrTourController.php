<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\VrHotspot;
use App\Models\VrScene;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class VrTourController extends Controller
{
    /**
     * Typical vertical field of view of a phone camera sweeping a panorama.
     * Used to estimate how wide a partial panorama's sweep was, since the
     * photo itself carries no such metadata.
     */
    private const PHONE_VERTICAL_FOV = 60.0;

    /**
     * Uploads a new panorama as a scene. The first scene added to a room
     * automatically becomes the default (the one the tour opens on), so a
     * tour is never left without an entry point.
     *
     * Accepts both true 360 photos and ordinary phone panoramas — the coverage
     * angles are estimated from the image's shape so a non-technical admin can
     * upload straight from their phone's Panorama mode without a second app.
     */
    public function storeScene(Request $request, Room $room): JsonResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:100'],
            'panorama' => ['required', 'file', 'mimes:jpg,jpeg,png', 'max:20480'], // 20MB — panoramas are large
        ]);

        $file = $request->file('panorama');
        $fov = $this->estimateFieldOfView($file->getRealPath());

        $isFirst = ! $room->vrScenes()->exists();

        $scene = $room->vrScenes()->create([
            'title' => $data['title'],
            'panorama_path' => $file->store('vr-scenes', 'public'),
            'is_default' => $isFirst,
            'sort_order' => ($room->vrScenes()->max('sort_order') ?? -1) + 1,
            'haov' => $fov['haov'],
            'vaov' => $fov['vaov'],
            'v_offset' => 0,
            'is_partial' => $fov['is_partial'],
        ]);

        return response()->json($this->transformScene($scene->load('hotspots.targetScene:id,title')), 201);
    }

    /**
     * Works out how much of the world a panorama covers, from its shape alone.
     *
     * A true equirectangular 360 photo is always 2:1, so anything close to that
     * is treated as full coverage. Anything wider and flatter is a partial
     * sweep — a phone panorama — where the horizontal coverage is estimated by
     * assuming a typical phone's vertical field of view and scaling by the
     * image's aspect ratio. It's an estimate, not metadata, which is why the
     * admin can adjust it with the sweep slider afterwards.
     */
    private function estimateFieldOfView(string $path): array
    {
        $size = @getimagesize($path);

        if (! $size || $size[1] == 0) {
            return ['haov' => 360.0, 'vaov' => 180.0, 'is_partial' => false];
        }

        $ratio = $size[0] / $size[1];

        // 2:1 (within tolerance) means a genuine full-sphere panorama.
        if ($ratio >= 1.9 && $ratio <= 2.1) {
            return ['haov' => 360.0, 'vaov' => 180.0, 'is_partial' => false];
        }

        $haov = min(360.0, round($ratio * self::PHONE_VERTICAL_FOV, 2));
        $vaov = round($haov / $ratio, 2);

        return ['haov' => $haov, 'vaov' => $vaov, 'is_partial' => true];
    }

    /**
     * Lets the admin correct the estimated sweep by dragging a slider while
     * watching a live preview — no angle maths required on their part.
     */
    public function updateSceneView(Request $request, VrScene $scene): JsonResponse
    {
        $data = $request->validate([
            'haov' => ['required', 'numeric', 'between:30,360'],
            'v_offset' => ['nullable', 'numeric', 'between:-90,90'],
        ]);

        $size = @getimagesize(Storage::disk('public')->path($scene->panorama_path));
        $ratio = ($size && $size[1] != 0) ? $size[0] / $size[1] : 2;

        $haov = (float) $data['haov'];
        $vaov = min(180.0, round($haov / $ratio, 2));

        $scene->update([
            'haov' => $haov,
            'vaov' => $vaov,
            'v_offset' => $data['v_offset'] ?? 0,
            'is_partial' => $haov < 359.5,
        ]);

        return response()->json($this->transformScene($scene->fresh('hotspots.targetScene')));
    }

    public function updateScene(Request $request, VrScene $scene): JsonResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:100'],
        ]);

        $scene->update(['title' => $data['title']]);

        return response()->json($this->transformScene($scene->fresh('hotspots.targetScene')));
    }

    /**
     * Makes this scene the tour's starting point. Only one scene per room can
     * be default, so the previous one is cleared in the same transaction.
     */
    public function setDefaultScene(VrScene $scene): JsonResponse
    {
        DB::transaction(function () use ($scene) {
            VrScene::where('room_id', $scene->room_id)->update(['is_default' => false]);
            $scene->update(['is_default' => true]);
        });

        return response()->json(['message' => 'Starting scene updated.']);
    }

    /**
     * Deletes a scene, its panorama file, and every hotspot pointing at it.
     * If it was the default, the next remaining scene is promoted so the tour
     * still has a valid entry point.
     */
    public function destroyScene(VrScene $scene): JsonResponse
    {
        $roomId = $scene->room_id;
        $wasDefault = $scene->is_default;

        DB::transaction(function () use ($scene) {
            // Arrows in other scenes that lead here would otherwise dead-end.
            $scene->incomingHotspots()->delete();
            Storage::disk('public')->delete($scene->panorama_path);
            $scene->delete();
        });

        if ($wasDefault) {
            $next = VrScene::where('room_id', $roomId)->orderBy('sort_order')->first();
            $next?->update(['is_default' => true]);
        }

        return response()->json(['message' => 'Scene deleted.']);
    }

    /**
     * Places an arrow in a scene. pitch/yaw come from the admin clicking
     * directly on the panorama preview — Pannellum reports the 3D angle of
     * the click, so the position is always physically correct in the sphere.
     */
    public function storeHotspot(Request $request, VrScene $scene): JsonResponse
    {
        $data = $request->validate([
            'target_scene_id' => ['required', 'integer', 'exists:vr_scenes,id'],
            'pitch' => ['required', 'numeric', 'between:-90,90'],
            'yaw' => ['required', 'numeric', 'between:-180,180'],
            'label' => ['nullable', 'string', 'max:100'],
        ]);

        $target = VrScene::findOrFail($data['target_scene_id']);

        // A hotspot must stay inside its own room's tour — linking to another
        // room's scene would jump the visitor somewhere unrelated.
        if ($target->room_id !== $scene->room_id) {
            return response()->json([
                'message' => 'That destination belongs to a different room.',
            ], 422);
        }

        if ($target->id === $scene->id) {
            return response()->json([
                'message' => 'A hotspot cannot lead back to the same scene.',
            ], 422);
        }

        $hotspot = $scene->hotspots()->create([
            'target_scene_id' => $target->id,
            'pitch' => $data['pitch'],
            'yaw' => $data['yaw'],
            'label' => $data['label'] ?: ('Go to ' . $target->title),
        ]);

        return response()->json([
            'id' => $hotspot->id,
            'target_scene_id' => $hotspot->target_scene_id,
            'target_title' => $target->title,
            'pitch' => $hotspot->pitch,
            'yaw' => $hotspot->yaw,
            'label' => $hotspot->label,
        ], 201);
    }

    public function destroyHotspot(VrHotspot $hotspot): JsonResponse
    {
        $hotspot->delete();

        return response()->json(['message' => 'Hotspot removed.']);
    }

    private function transformScene(VrScene $scene): array
    {
        return [
            'id' => $scene->id,
            'title' => $scene->title,
            'panorama_url' => Storage::disk('public')->url($scene->panorama_path),
            'is_default' => $scene->is_default,
            'sort_order' => $scene->sort_order,
            'haov' => $scene->haov,
            'vaov' => $scene->vaov,
            'v_offset' => $scene->v_offset,
            'is_partial' => $scene->is_partial,
            'hotspots' => $scene->hotspots->map(fn ($hotspot) => [
                'id' => $hotspot->id,
                'target_scene_id' => $hotspot->target_scene_id,
                'target_title' => $hotspot->targetScene?->title,
                'pitch' => $hotspot->pitch,
                'yaw' => $hotspot->yaw,
                'label' => $hotspot->label,
            ])->values(),
        ];
    }
}