<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;

class VinInsightService
{
    public function generate(array $vehicleData): array
    {
        $response = Http::withToken(config('services.openai.key'))
            ->timeout(60)
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o-mini',
                'temperature' => 0.4,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are an automotive expert. Respond ONLY with valid JSON. No markdown.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $this->buildPrompt($vehicleData)
                    ],
                ],
            ]);

        // 🚨 OpenAI error (401, 429, 400, etc.)
        if (!$response->successful()) {
            throw new \RuntimeException(
                'OpenAI API error: ' . $response->body()
            );
        }

        // ✅ Safe extraction
        $content = data_get($response->json(), 'choices.0.message.content');

        if (!$content) {
            throw new \RuntimeException(
                'Invalid OpenAI response structure: ' . $response->body()
            );
        }

        $decoded = json_decode($content, true);

        if (!is_array($decoded)) {
            throw new \RuntimeException(
                'AI response is not valid JSON: ' . $content
            );
        }

        return $decoded;
    }

    private function buildPrompt(array $vehicleData): string
    {
        return <<<PROMPT
Vehicle data:
{$this->toJson($vehicleData)}

Return STRICT JSON with:
summary (string)
known_issues (array of {issue, mileage_range, severity})
maintenance_tips (array)
owner_tips (array)
cost_expectations (object with yearly_range, high_cost_parts)
peace_of_mind_score (0-100)
PROMPT;
    }

    private function toJson(array $data): string
    {
        return json_encode($data, JSON_PRETTY_PRINT);
    }
}
