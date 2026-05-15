<?php

namespace App\Http\Controllers;

use App\Models\Place;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class PlaceController extends Controller
{
    public function show(int $id): View
    {
        $place = Place::with(['category', 'reviews'])->findOrFail($id);
        return view('places.show', compact('place'));
    }

    public function index(): JsonResponse
    {
        $places = Place::with('category')->get()->map(fn($p) => [
            'id'             => $p->id,
            'name'           => $p->name,
            'category'       => $p->category->name,
            'category_id'    => $p->category_id,
            'lat'            => (float) $p->lat,
            'lng'            => (float) $p->lng,
            'rating'         => (float) $p->rating,
            'wheelchair'     => (bool) $p->wheelchair,
            'ramp'           => (bool) $p->ramp,
            'elevator'       => (bool) $p->elevator,
            'adapted_toilet' => (bool) $p->adapted_toilet,
            'pmr_parking'    => (bool) $p->pmr_parking,
'url' => '/places/' . $p->id,
        ]);

        return response()->json($places);
    }
}