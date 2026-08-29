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
     * Uploads a new panorama as a scene. The first scene added to a room
     * automatically becomes the default (the one the tour opens on), so a
     * tour is never left without an entry point.
     */
    public function storeScene(Request $request, Room $room): JsonResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:100'],
            'panorama' => ['required', 'file', 'mimes:jpg,jpeg,png', 'max:20480'], // 20MB — panoramas are large
        ]);

        $isFirst = ! $room->vrScenes()->exists();

        $scene = $room->vrScenes()->create([
            'title' => $data['title'],
            'panorama_path' => $request->file('panorama')->store('vr-scenes', 'public'),
            'is_default' => $isFirst,
            'sort_order' => ($room->vrScenes()->max('sort_order') ?? -1) + 1,
        ]);

        return response()->json($this->transformScene($scene->load('hotspots.targetScene:id,title')), 201);
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