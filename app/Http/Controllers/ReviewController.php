<?php

namespace App\Http\Controllers;

use App\Models\Place;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ReviewController extends Controller
{
    public function store(Request $request, int $placeId): JsonResponse
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $place = Place::findOrFail($placeId);

        $review = Review::create([
            'place_id' => $place->id,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
        ]);

        $place->rating = $place->reviews()->avg('rating');
        $place->save();

        return response()->json([
            'success' => true,
            'review' => $review,
            'new_rating' => round($place->rating, 1)
        ]);
    }
}