<?php

namespace App\Http\Controllers;

use App\Models\Obstacle;
use Illuminate\Http\JsonResponse;

class ObstacleController extends Controller
{
    public function index(): JsonResponse
    {
        $obstacles = Obstacle::where('status', 'approved')
            ->get(['id', 'lat', 'lng', 'type', 'severity']);

        return response()->json($obstacles);
    }
}