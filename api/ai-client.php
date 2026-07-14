<?php
function call_gemini(string $prompt): string
{
    $apiKey = getenv('AI_API_KEY');
    if (empty($apiKey)) {
        return 'API key not configured';
    }

    // $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=' . urlencode($apiKey);
    // $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . urlencode($apiKey);
    $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-3.5-flash:generateContent?key=' . urlencode($apiKey);
    $payload = json_encode([
        'contents' => [
            [
                'parts' => [
                    [
                        'text' => $prompt
                    ]
                ]
            ]
        ]
    ]);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if (!empty($curlError)) {
        return 'cURL error: ' . $curlError;
    }

    if ($httpCode !== 200) {
        return 'API error: HTTP ' . $httpCode . ' | Response: ' . substr($response, 0, 200);
    }

    $parsedResponse = json_decode($response, true);
    if (!empty($parsedResponse['candidates'][0]['content']['parts'][0]['text'])) {
        return $parsedResponse['candidates'][0]['content']['parts'][0]['text'];
    }

    return 'No response from API: ' . $response;
}


function call_ai_api(string $prompt): string
{
    $provider = getenv('AI_API_PROVIDER') ?: 'mock';

    if ($provider === 'gemini') {
        return call_gemini($prompt);
    }

    return '';
}

function generate_ai_nutrition_plan(array $profile, array $preferences): array
{
    $restrictions = $preferences['restrictions'] ?? '';
    $diet_type = $preferences['diet_type'] ?? 'balanced';
    $goal = $preferences['goal'] ?? 'maintenance';
    $calories = $preferences['calories'] ?? 2200;

    $prompt = "Generate a detailed JSON nutrition plan for the following profile:\n" .
        "Profile: Name={$profile['name']}, Age={$profile['age']}, Gender={$profile['gender']}, " .
        "Height={$profile['height_cm']}cm, Weight={$profile['weight_kg']}kg\n" .
        "Preferences: Diet Type=$diet_type, Goal=$goal, Daily Calories=$calories, Restrictions=$restrictions\n" .
        "Return ONLY valid JSON (no markdown, no code blocks) with this exact structure:\n" .
        "{\"meals\": [{\"name\": \"Breakfast\", \"food\": \"eggs and toast\", \"quantity\": \"2 eggs, 2 slices\", \"time\": \"07:00\", \"calories\": 300, \"protein\": 12, \"carbs\": 35, \"fat\": 12}], \"recommendations\": [\"Stay hydrated\", \"Eat protein at each meal\"]}";

    $aiResponse = call_ai_api($prompt);
    // echo '<pre>RAW: ' . htmlspecialchars($aiResponse) . '</pre>';
    // exit;   


    if (empty($aiResponse) || strpos($aiResponse, 'error') !== false || strpos($aiResponse, 'API') !== false) {
        return buildNutritionPlan($profile, $preferences);
    }

    $parsed = json_decode($aiResponse, true);
    if (!$parsed || empty($parsed['meals'])) {
        return buildNutritionPlan($profile, $preferences);
    }

    $meals = $parsed['meals'];
    $totals = ['calories' => 0, 'protein' => 0, 'carbs' => 0, 'fat' => 0];
    foreach ($meals as $meal) {
        $totals['calories'] += $meal['calories'] ?? 0;
        $totals['protein'] += $meal['protein'] ?? 0;
        $totals['carbs'] += $meal['carbs'] ?? 0;
        $totals['fat'] += $meal['fat'] ?? 0;
    }

    $bmi = calculateBMI($profile['weight_kg'], $profile['height_cm']);
    $macros = calculateMacros($calories, $goal);
    return [
        'profile' => $profile,
        'diet_type' => $diet_type,
        'goal' => $goal,
        'calories' => $calories,
        'ibw' => $profile['ibw'] ?? 0,
        'bmi' => $bmi,
        'macros' => $macros,
        'meals' => $meals,
        'totals' => $totals,
        'summary' => $parsed['recommendations'][0] ?? 'Personalized nutrition plan based on your profile and preferences.',
        'recommendations' => $parsed['recommendations'] ?? [],
    ];
}

class AiClient
{
    public function generatePlan(array $profile, array $preferences): array
    {
        $provider = getenv('AI_API_PROVIDER') ?: 'mock';
        if ($provider === 'gemini' && !empty(getenv('AI_API_KEY'))) {
            return generate_ai_nutrition_plan($profile, $preferences);
        }
        return buildNutritionPlan($profile, $preferences);
    }
}
