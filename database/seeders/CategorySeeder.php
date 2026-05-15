<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Plage', 'icon' => '🏖️'],
            ['name' => 'Parc', 'icon' => '🌳'],
            ['name' => 'Hôpital', 'icon' => '🏥'],
            ['name' => 'Administration', 'icon' => '🏛️'],
            ['name' => 'Café', 'icon' => '☕'],
            ['name' => 'Hôtel', 'icon' => '🏨'],
            ['name' => 'Pharmacie', 'icon' => '💊'],
            ['name' => 'Restaurant', 'icon' => '🍽️'],
            ['name' => 'Musée', 'icon' => '🏛️'],
            ['name' => 'Banque', 'icon' => '🏦'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}