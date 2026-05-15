<?php

namespace Database\Seeders;

use App\Models\Obstacle;
use Illuminate\Database\Seeder;

class ObstacleSeeder extends Seeder
{
    public function run(): void
    {
        $obstacles = [
            [
                'lat' => 30.4285,
                'lng' => -9.5878,
                'type' => 'escalier_bloquant',
                'description' => 'Escalier de 5 marches sans rampe à l\'entrée du magasin',
                'severity' => 'high',
                'status' => 'approved',
            ],
            [
                'lat' => 30.4198,
                'lng' => -9.5756,
                'type' => 'trottoir_casse',
                'description' => 'Trottoir effondré sur 3 mètres, passage difficile',
                'severity' => 'medium',
                'status' => 'approved',
            ],
            [
                'lat' => 30.4356,
                'lng' => -9.5689,
                'type' => 'pente_forte',
                'description' => 'Pente de 15% pour accéder au parking',
                'severity' => 'high',
                'status' => 'approved',
            ],
            [
                'lat' => 30.4102,
                'lng' => -9.5623,
                'type' => 'travaux',
                'description' => 'Travaux de voirie, passage bloqué depuis 2 mois',
                'severity' => 'high',
                'status' => 'approved',
            ],
            [
                'lat' => 30.4223,
                'lng' => -9.5898,
                'type' => 'absence_rampe',
                'description' => 'Pas de rampe pour accéder au restaurant',
                'severity' => 'medium',
                'status' => 'approved',
            ],
            [
                'lat' => 30.4412,
                'lng' => -9.5598,
                'type' => 'route_dangereuse',
                'description' => 'Route sans trottoir, circulation dense',
                'severity' => 'medium',
                'status' => 'approved',
            ],
            [
                'lat' => 30.4167,
                'lng' => -9.5934,
                'type' => 'trottoir_casse',
                'description' => 'Nids de poule sur le trottoir menant à la kasbah',
                'severity' => 'low',
                'status' => 'approved',
            ],
            [
                'lat' => 30.4245,
                'lng' => -9.5812,
                'type' => 'escalier_bloquant',
                'description' => 'Escalier menant au souk sans alternative PMR',
                'severity' => 'high',
                'status' => 'approved',
            ],
        ];

        foreach ($obstacles as $obstacle) {
            Obstacle::create($obstacle);
        }
    }
}