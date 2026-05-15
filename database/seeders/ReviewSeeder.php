<?php

namespace Database\Seeders;

use App\Models\Review;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $reviews = [
            ['place_id' => 1, 'rating' => 5, 'comment' => 'Superbe plage avec accès PMR. Les rampes sont bien entretenues.'],
            ['place_id' => 1, 'rating' => 4, 'comment' => 'Accessible mais les toilettes adaptées sont un peu éloignées du parking.'],
            ['place_id' => 1, 'rating' => 4, 'comment' => 'Très belle vue, manège accessible pour fauteuils.'],
            ['place_id' => 3, 'rating' => 4, 'comment' => 'Parc agréable, certaines allées sont pavées et praticables.'],
            ['place_id' => 3, 'rating' => 3, 'comment' => 'Les sentiers sont en terre battue, difficile en fauteuil.'],
            ['place_id' => 4, 'rating' => 4, 'comment' => 'CHU bien équipé, ascenseurs et rampes partout.'],
            ['place_id' => 4, 'rating' => 3, 'comment' => 'Attente longue mais accès possible pour les fauteuils.'],
            ['place_id' => 5, 'rating' => 3, 'comment' => 'Administration, beaucoup de marches à l\'entrée.'],
            ['place_id' => 6, 'rating' => 5, 'comment' => 'Café(super) avec rampe et Terrace accesible. Personnel très aider.'],
            ['place_id' => 6, 'rating' => 4, 'comment' => 'Bon café, mais espace intérieur étroit pour fauteuil.'],
            ['place_id' => 7, 'rating' => 5, 'comment' => 'Hôtel luxe avec TOUT accessible! Ascenseur, piscine PMR, chambre adaptée.'],
            ['place_id' => 7, 'rating' => 5, 'comment' => 'Séjour parfait, personnel formé pour l\'accueil PMR.'],
            ['place_id' => 7, 'rating' => 4, 'comment' => 'Excellent buffet accessible mais le spa est en hauteur.'],
            ['place_id' => 10, 'rating' => 4, 'comment' => 'Très bon resto, sanitaires PMR au rez-de-chaussée.'],
            ['place_id' => 10, 'rating' => 5, 'comment' => 'Cuisine excellente, personnel aux petits soins.'],
            ['place_id' => 12, 'rating' => 3, 'comment' => 'Musée intéressant mais impossible d\'accéder au premier étage sans ascenseur.'],
            ['place_id' => 14, 'rating' => 4, 'comment' => 'Service bancaire adapté, guichet accessible.'],
            ['place_id' => 15, 'rating' => 5, 'comment' => 'Poisson frais, terrace avec vue. Accès fauteuil possible.'],
            ['place_id' => 16, 'rating' => 3, 'comment' => 'Mairie: ascenseur fonctionne, mais pas de toilettes adaptées au public.'],
            ['place_id' => 18, 'rating' => 4, 'comment' => 'Joli jardin, parkings PMR disponibles à l\'entrée.'],
            ['place_id' => 19, 'rating' => 4, 'comment' => 'Musée émouvant, exposition accessible au rez-de-chaussée.'],
            ['place_id' => 20, 'rating' => 5, 'comment' => 'Tajine délicieux! Mais marches à l\'entrée, pas de rampe.'],
            ['place_id' => 2, 'rating' => 2, 'comment' => 'Belle plage mais pas de accès PMR du tout.'],
            ['place_id' => 8, 'rating' => 4, 'comment' => 'Hôtel correct, ascenseur et rampes présents.'],
            ['place_id' => 9, 'rating' => 5, 'comment' => 'Pharmacie très serviable, rampe installée récemment.'],
            ['place_id' => 11, 'rating' => 5, 'comment' => 'Le meilleur restaurant d\'Agadir! Tout accessible.'],
            ['place_id' => 13, 'rating' => 4, 'comment' => 'Banque correcte, guichet accessible.'],
            ['place_id' => 17, 'rating' => 4, 'comment' => 'Café cozy, rampe à l\'entrée.'],
            ['place_id' => 19, 'rating' => 3, 'comment' => 'Musée petit mais escalier à l\'entrée.'],
        ];

        foreach ($reviews as $review) {
            Review::create($review);
        }
    }
}