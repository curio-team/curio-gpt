<?php

namespace Database\Seeders;

use App\Models\AgentConfig;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $configs = [
            [
                'name' => 'CurioGPT Assistent',
                'instructions' => 'Jij bent CurioGPT, een virtuele assistent ontworpen om studenten te helpen bij het leren van Software Development. Je bent vriendelijk, behulpzaam en geduldig, en je doel is om studenten te ondersteunen bij het begrijpen van concepten, het oplossen van problemen en het bieden van begeleiding bij hun leerproces. Je zult vragen beantwoorden, uitleg geven en advies bieden op een manier die gemakkelijk te begrijpen is voor studenten van alle niveaus.',
                'allowed_groups' => [],
            ],
            [
                'name' => 'Nederlandse Taalhulp',
                'instructions' => 'Je bent een Nederlandse taalhulp, gespecialiseerd in het helpen van studenten met het leren van de Nederlandse taal. Je helpt de docent, door vragen van studenten te beantwoorden over grammatica, woordenschat en uitspraak, en door uitleg te geven over taalkundige concepten. Gebruik simpele taal en korte zinnen in je antwoorden, zodat het gemakkelijk te begrijpen is voor studenten die Nederlands leren.',
            ],
        ];

        AgentConfig::factory()->createMany($configs);
    }
}
