<?php

namespace App\Http\Controllers;

use App\Models\Obstacle;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ObstacleController extends Controller
{
    public function index(): JsonResponse
    {
        $obstacles = Obstacle::where('status', 'approved')
            ->get()
            ->map(fn($o) => [
                'id' => $o->id,
                'lat' => (float) $o->lat,
                'lng' => (float) $o->lng,
                'type' => $o->type,
                'description' => $o->description,
                'severity' => $o->severity,
            ]);

        return response()->json($obstacles);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'type' => 'required|string',
            'description' => 'nullable|string|max:500',
            'severity' => 'required|in:low,medium,high',
        ]);

        $obstacle = Obstacle::create([
            'lat' => $validated['lat'],
            'lng' => $validated['lng'],
            'type' => $validated['type'],
            'description' => $validated['description'] ?? null,
            'severity' => $validated['severity'],
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Obstacle signalé avec succès. Il sera affiché après modération.',
            'obstacle' => $obstacle,
        ]);
    }
}