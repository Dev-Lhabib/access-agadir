<?php

namespace App\Http\Controllers;

use App\Models\Place;
use App\Models\Obstacle;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class AiController extends Controller
{
    public function recommend(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'place_id' => 'required|integer',
            'origin_lat' => 'nullable|numeric',
            'origin_lng' => 'nullable|numeric',
        ]);

        $place = Place::with('category')->findOrFail($validated['place_id']);

        $accessibility = [];
        if ($place->wheelchair) $accessibility[] = 'Accessible fauteuil roulant';
        if ($place->ramp) $accessibility[] = 'Rampe d\'accès';
        if ($place->elevator) $accessibility[] = 'Ascenseur';
        if ($place->adapted_toilet) $accessibility[] = 'Toilettes adaptées';
        if ($place->pmr_parking) $accessibility[] = 'Parking PMR';

        $obstaclesText = '';
        if ($validated['origin_lat'] && $validated['origin_lng']) {
            $obstacles = Obstacle::where('status', 'approved')
                ->selectRaw('*, (6371 * acos(cos(radians(?)) * cos(radians(lat)) * cos(radians(lng) - radians(?)) + sin(radians(?)) * sin(radians(lat)))) AS distance', [$validated['origin_lat'], $validated['origin_lng'], $validated['origin_lat']])
                ->having('distance', '<', 0.5)
                ->orderBy('distance')
                ->limit(5)
                ->get();

            if ($obstacles->count() > 0) {
                $obstaclesText = "\nObstacles à proximité (500m): ";
                foreach ($obstacles as $obs) {
                    $obstaclesText .= "- " . str_replace('_', ' ', $obs->type) . " (sévérité: {$obs->severity}) à " . round($obs->distance, 1) . "km\n";
                }
            }
        }

        $apiKey = config('services.openai.key');
        if (!$apiKey) {
            return response()->json(['error' => 'Clé API OpenAI non configurée'], 500);
        }

        $prompt = "Tu es un assistant expert en accessibilité pour personnes à mobilité réduite (PMR) à Agadir, Maroc.
Analyse ce lieu et donne des conseils en français (3-5 phrases).
Catégorie: {$place->category->name}
Nom: {$place->name}
Adresse: {$place->address}
Équipements accessibles: " . (count($accessibility) > 0 ? implode(', ', $accessibility) : 'Aucun signalé') . "
Note: {$place->rating}/5{$obstaclesText}
" . ($validated['origin_lat'] ? "Point de départ: lat {$validated['origin_lat']}, lng {$validated['origin_lng']}" : '') . "
Réponds de manière concise et utile pour une personne en fauteuil roulant.";

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o',
                'messages' => [
                    ['role' => 'system', 'content' => 'Tu es un assistant expert en accessibilité PMR à Agadir. Réponds en français de manière concise (3-5 phrases).'],
                    ['role' => 'user', 'content' => $prompt]
                ],
                'max_tokens' => 200,
                'temperature' => 0.7,
            ]);

            $data = $response->json();
            $recommendation = $data['choices'][0]['message']['content'] ?? 'Désolé, aucune recommandation disponible.';
        } catch (\Exception $e) {
            $recommendation = "Ce lieu ({$place->name}) dispose de " . (count($accessibility) > 0 ? implode(', ', $accessibility) : 'peu d\'équipements accessibles') . ". Il est noté {$place->rating}/5. Nous vous recommandons de vérifier l'accessibilité sur place.";
        }

        return response()->json(['recommendation' => $recommendation]);
    }
}