<?php

namespace Database\Seeders;

use App\Enums\PostStatus;
use App\Models\Post;
use Gemini;
use Illuminate\Database\Seeder;

class AiPostSeeder extends Seeder
{
    public function run(): void
    {
        $apiKey = env('LARAVEL_GEMINI_API_KEY');
        
        if (!$apiKey) {
            echo "Error: No LARAVEL_GEMINI_API_KEY found in .env\n";
            return;
        }

        $client = Gemini::client($apiKey);
        
        echo "Buscando modelos disponibles...\n";
        try {
            $models = $client->models()->list();
            foreach ($models as $model) {
                print_r($model);
            }
        } catch (\Exception $e) {
            echo "No se pudieron listar los modelos: " . $e->getMessage() . "\n";
        }

        $prompt = "Genera un artículo de blog breve (máximo 500 palabras) sobre actividades humanitarias o desarrollo social. Responde estrictamente en formato JSON con los campos: 'title' y 'content' (HTML básico).";

        for ($i = 1; $i <= 3; $i++) {
            echo "Generando artículo $i con IA...\n";
            try {
                // Usar gemini-2.0-flash
                $result = $client->generativeModel('gemini-2.0-flash')->generateContent($prompt);
                $jsonResponse = $result->text();
                $jsonResponse = preg_replace('/^```json\s*|\s*```$/i', '', trim($jsonResponse));
                $data = json_decode($jsonResponse, true);

                if ($data && isset($data['title'])) {
                    Post::create([
                        'title' => $data['title'],
                        'content' => $data['content'],
                        'status' => PostStatus::PUBLISHED,
                        'published_at' => now(),
                        'image' => 'https://image.pollinations.ai/prompt/humanitarian-news-blog?width=800&height=600&seed=' . rand(1, 9999),
                    ]);
                    echo "Éxito: " . $data['title'] . "\n";
                } else {
                    echo "Fallo: Respuesta no válida.\n";
                }
            } catch (\Exception $e) {
                echo "Error: " . $e->getMessage() . "\n";
            }
        }
    }
}
