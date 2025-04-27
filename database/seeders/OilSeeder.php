<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Oil;

class OilSeeder extends Seeder
{
    public function run(): void
    {
        $oils = [
            [
                'name' => 'Aceite de Oliva Virgen Extra',
                'description' => 'AOVE - El aceite de oliva virgen extra es de la más alta calidad. Se obtiene de aceitunas frescas mediante métodos mecánicos, sin aditivos ni refinado. Su sabor es intenso y afrutado, con baja acidez (máximo 0,8%).',
                'price' => 5.07,
                'photo_url' => 'https://res.cloudinary.com/dfo33gizg/image/upload/v1745738087/aove_l3g8wh.png',
            ],
            [
                'name' => 'Aceite de Oliva Virgen',
                'description' => 'El aceite de oliva virgen se obtiene de aceitunas frescas y tiene una acidez inferior a 2%. Su sabor es más suave que el virgen extra.',
                'price' => 4.87,
                'photo_url' => 'https://res.cloudinary.com/dfo33gizg/image/upload/v1745738087/aov_nroith.png',
            ],
            [
                'name' => 'Aceite de Oliva Lampante',
                'description' => 'El aceite de oliva lampante no es apto para el consumo directo sin antes ser refinado. Tiene una alta acidez y se utiliza para la elaboración de aceites refinados.',
                'price' => 4.60,
                'photo_url' => 'https://res.cloudinary.com/dfo33gizg/image/upload/v1745738087/ao_ptxumg.png',
            ],
        ];

        foreach ($oils as $oil) {
            Oil::create($oil);
        }
    }
}
