<?php
function sanitizeInput($value): string
{
    return htmlspecialchars(trim((string) $value), ENT_QUOTES, 'UTF-8');
}

function validateRequired(array $data, array $fields): array
{
    $errors = [];
    foreach ($fields as $field) {
        if (!isset($data[$field]) || trim((string) $data[$field]) === '') {
            $errors[] = ucfirst($field) . ' is required.';
        }
    }
    return $errors;
}

function calculate_ibw(string $gender, float $heightCm): float
{
    $heightInches = $heightCm / 2.54;
    $inchesOver5Feet = max(0, $heightInches - 60);

    if (strtolower($gender) === 'female') {
        return round(45.5 + (2.3 * $inchesOver5Feet), 1);
    }

    return round(50 + (2.3 * $inchesOver5Feet), 1);
}

function calculateIBW(string $gender, float $heightCm): float
{
    return calculate_ibw($gender, $heightCm);
}

function validate_profile(array $data): array
{
    $errors = [];

    $name = trim((string) ($data['name'] ?? ''));
    if ($name === '') {
        $errors['name'] = 'Name is required.';
    }

    $age = filter_var($data['age'] ?? '', FILTER_VALIDATE_INT);
    if ($age === false || $age < 1 || $age > 120) {
        $errors['age'] = 'Age must be between 1 and 120.';
    }

    $gender = strtolower(trim((string) ($data['gender'] ?? '')));
    if (!in_array($gender, ['male', 'female'], true)) {
        $errors['gender'] = 'Please select a valid gender.';
    }

    $heightCm = filter_var($data['height_cm'] ?? '', FILTER_VALIDATE_FLOAT);
    if ($heightCm === false || $heightCm < 100 || $heightCm > 250) {
        $errors['height_cm'] = 'Height must be between 100 and 250 cm.';
    }

    $weightKg = filter_var($data['weight_kg'] ?? '', FILTER_VALIDATE_FLOAT);
    if ($weightKg === false || $weightKg < 30 || $weightKg > 300) {
        $errors['weight_kg'] = 'Weight must be between 30 and 300 kg.';
    }

    return $errors;
}

function calculateBMI(float $weightKg, float $heightCm): float
{
    $heightM = $heightCm / 100;
    if ($heightM <= 0) {
        return 0;
    }

    return round($weightKg / ($heightM * $heightM), 1);
}

function calculateBMR(string $gender, float $heightCm, float $weightKg, int $age): float
{
    $heightCm = max(1, $heightCm);
    $weightKg = max(1, $weightKg);
    $age = max(1, $age);

    if (strtolower($gender) === 'female') {
        return round((10 * $weightKg) + (6.25 * $heightCm) - (5 * $age) - 161, 1);
    }

    return round((10 * $weightKg) + (6.25 * $heightCm) - (5 * $age) + 5, 1);
}

function calculateTargetCalories(float $bmr, string $goal): int
{
    $activityMultiplier = 1.2;

    if ($goal === 'weight-loss') {
        return max(1500, (int) round($bmr * $activityMultiplier - 300));
    }

    if ($goal === 'weight-gain') {
        return (int) round($bmr * $activityMultiplier + 300);
    }

    return (int) round($bmr * $activityMultiplier);
}

function estimate_daily_calories(array $profile, string $goal): int
{
    $heightCm = (float) ($profile['height_cm'] ?? 0);
    $weightKg = (float) ($profile['weight_kg'] ?? 0);
    $age = (int) ($profile['age'] ?? 0);
    $gender = (string) ($profile['gender'] ?? 'female');
    $bmr = calculateBMR($gender, $heightCm, $weightKg, $age);

    return calculateTargetCalories($bmr, $goal);
}

function calculateMacros(int $calories, string $goal): array
{
    if ($goal === 'weight-loss') {
        return [
            'protein' => (int) round($calories * 0.35 / 4),
            'carbs' => (int) round($calories * 0.35 / 4),
            'fat' => (int) round($calories * 0.30 / 9),
        ];
    }

    if ($goal === 'weight-gain') {
        return [
            'protein' => (int) round($calories * 0.30 / 4),
            'carbs' => (int) round($calories * 0.45 / 4),
            'fat' => (int) round($calories * 0.25 / 9),
        ];
    }

    return [
        'protein' => (int) round($calories * 0.30 / 4),
        'carbs' => (int) round($calories * 0.40 / 4),
        'fat' => (int) round($calories * 0.30 / 9),
    ];
}

function getGoalLabel(string $goal): string
{
    $goals = [
        'weight-loss' => 'Weight Loss',
        'weight-gain' => 'Weight Gain',
        'maintenance' => 'Maintain Weight',
    ];

    return $goals[$goal] ?? ucfirst(str_replace('-', ' ', $goal));
}

function getDietLabel(string $dietType): string
{
    $normalized = str_replace('_', '-', strtolower($dietType));
    $diets = [
        'vegetarian' => 'Vegetarian',
        'vegan' => 'Vegan',
        'eggetarian' => 'Eggetarian',
        'non-vegetarian' => 'Non-Vegetarian',
        'balanced' => 'Balanced',
    ];

    return $diets[$normalized] ?? ucfirst(str_replace('-', ' ', $normalized));
}

function getFormValue(array $data, string $field, $default = '')
{
    return isset($data[$field]) ? sanitizeInput($data[$field]) : $default;
}

function appUrl(string $path = ''): string
{
    $path = ltrim($path, '/');
    if (APP_BASE_PATH === '/') {
        return '/' . $path;
    }

    return APP_BASE_PATH . '/' . $path;
}

function redirectTo(string $path): void
{
    header('Location: ' . appUrl($path));
    exit;
}

function buildNutritionPlan(array $profile, array $preferences): array
{
    $goal = $preferences['goal'] ?? 'maintenance';
    $dietType = str_replace('_', '-', strtolower((string) ($preferences['diet_type'] ?? 'balanced')));
    $calories = (int) ($preferences['calories'] ?? 2200);
    $restrictions = trim((string) ($preferences['restrictions'] ?? ''));

    $heightCm = (float) ($profile['height_cm'] ?? 0);
    $weightKg = (float) ($profile['weight_kg'] ?? 0);
    $age = (int) ($profile['age'] ?? 0);
    $gender = (string) ($profile['gender'] ?? 'female');
    $ibw = (float) ($profile['ibw'] ?? calculateIBW($gender, $heightCm));
    $bmi = calculateBMI($weightKg, $heightCm);
    $bmr = calculateBMR($gender, $heightCm, $weightKg, $age);
    $targetCalories = $calories > 0 ? $calories : calculateTargetCalories($bmr, $goal);
    $macros = calculateMacros($targetCalories, $goal);

    $mealTemplates = [
        'vegetarian' => [
            ['name' => 'Breakfast', 'time' => '08:00', 'food' => 'Overnight Oats with Berries', 'quantity' => '1 bowl', 'calories' => 380, 'protein' => 18, 'carbs' => 54, 'fat' => 10],
            ['name' => 'Mid-Morning Snack', 'time' => '10:30', 'food' => 'Greek Yogurt Parfait', 'quantity' => '1 serving', 'calories' => 220, 'protein' => 16, 'carbs' => 24, 'fat' => 7],
            ['name' => 'Lunch', 'time' => '13:00', 'food' => 'Quinoa Chickpea Bowl', 'quantity' => '1 bowl', 'calories' => 510, 'protein' => 20, 'carbs' => 68, 'fat' => 16],
            ['name' => 'Afternoon Snack', 'time' => '16:00', 'food' => 'Trail Mix', 'quantity' => '1 small pack', 'calories' => 180, 'protein' => 5, 'carbs' => 18, 'fat' => 10],
            ['name' => 'Dinner', 'time' => '19:30', 'food' => 'Paneer Stir-Fry with Brown Rice', 'quantity' => '1 plate', 'calories' => 620, 'protein' => 28, 'carbs' => 60, 'fat' => 22],
        ],
        'vegan' => [
            ['name' => 'Breakfast', 'time' => '08:00', 'food' => 'Chia Seed Smoothie Bowl', 'quantity' => '1 bowl', 'calories' => 360, 'protein' => 16, 'carbs' => 44, 'fat' => 13],
            ['name' => 'Mid-Morning Snack', 'time' => '10:30', 'food' => 'Edamame', 'quantity' => '1 cup', 'calories' => 190, 'protein' => 17, 'carbs' => 14, 'fat' => 9],
            ['name' => 'Lunch', 'time' => '13:00', 'food' => 'Tofu Stir-Fry with Rice', 'quantity' => '1 serving', 'calories' => 520, 'protein' => 24, 'carbs' => 58, 'fat' => 17],
            ['name' => 'Afternoon Snack', 'time' => '16:00', 'food' => 'Hummus with Carrots', 'quantity' => '1 plate', 'calories' => 200, 'protein' => 7, 'carbs' => 20, 'fat' => 10],
            ['name' => 'Dinner', 'time' => '19:30', 'food' => 'Lentil Curry with Quinoa', 'quantity' => '1 bowl', 'calories' => 610, 'protein' => 25, 'carbs' => 72, 'fat' => 20],
        ],
        'eggetarian' => [
            ['name' => 'Breakfast', 'time' => '08:00', 'food' => 'Veggie Omelette with Toast', 'quantity' => '2 eggs', 'calories' => 400, 'protein' => 24, 'carbs' => 34, 'fat' => 18],
            ['name' => 'Mid-Morning Snack', 'time' => '10:30', 'food' => 'Fruit Bowl', 'quantity' => '1 bowl', 'calories' => 180, 'protein' => 3, 'carbs' => 38, 'fat' => 1],
            ['name' => 'Lunch', 'time' => '13:00', 'food' => 'Egg Fried Rice Bowl', 'quantity' => '1 serving', 'calories' => 500, 'protein' => 22, 'carbs' => 56, 'fat' => 18],
            ['name' => 'Afternoon Snack', 'time' => '16:00', 'food' => 'Milk and Nuts', 'quantity' => '1 glass', 'calories' => 220, 'protein' => 9, 'carbs' => 16, 'fat' => 10],
            ['name' => 'Dinner', 'time' => '19:30', 'food' => 'Dal with Rice and Salad', 'quantity' => '1 plate', 'calories' => 590, 'protein' => 24, 'carbs' => 70, 'fat' => 16],
        ],
        'non-vegetarian' => [
            ['name' => 'Breakfast', 'time' => '08:00', 'food' => 'Eggs with Oats and Fruit', 'quantity' => '2 eggs', 'calories' => 420, 'protein' => 26, 'carbs' => 40, 'fat' => 15],
            ['name' => 'Mid-Morning Snack', 'time' => '10:30', 'food' => 'Protein Shake', 'quantity' => '1 shake', 'calories' => 220, 'protein' => 24, 'carbs' => 15, 'fat' => 6],
            ['name' => 'Lunch', 'time' => '13:00', 'food' => 'Grilled Chicken Wrap', 'quantity' => '1 wrap', 'calories' => 520, 'protein' => 32, 'carbs' => 50, 'fat' => 17],
            ['name' => 'Afternoon Snack', 'time' => '16:00', 'food' => 'Cottage Cheese Bowl', 'quantity' => '1 bowl', 'calories' => 180, 'protein' => 15, 'carbs' => 12, 'fat' => 8],
            ['name' => 'Dinner', 'time' => '19:30', 'food' => 'Salmon with Rice and Veggies', 'quantity' => '1 plate', 'calories' => 660, 'protein' => 36, 'carbs' => 54, 'fat' => 24],
        ],
    ];

    $template = $mealTemplates[$dietType] ?? $mealTemplates['balanced'] ?? $mealTemplates['vegetarian'];
    $meals = [];
    foreach ($template as $meal) {
        $adjustment = $goal === 'weight-loss' ? 0.9 : ($goal === 'weight-gain' ? 1.1 : 1.0);
        $meals[] = [
            'name' => $meal['name'],
            'time' => $meal['time'],
            'food' => $meal['food'],
            'quantity' => $meal['quantity'],
            'calories' => (int) round($meal['calories'] * $adjustment),
            'protein' => (int) round($meal['protein'] * $adjustment),
            'carbs' => (int) round($meal['carbs'] * $adjustment),
            'fat' => (int) round($meal['fat'] * $adjustment),
        ];
    }

    $totals = ['calories' => 0, 'protein' => 0, 'carbs' => 0, 'fat' => 0];
    foreach ($meals as $meal) {
        $totals['calories'] += $meal['calories'];
        $totals['protein'] += $meal['protein'];
        $totals['carbs'] += $meal['carbs'];
        $totals['fat'] += $meal['fat'];
    }

    $recommendations = [];
    if ($goal === 'weight-loss') {
        $recommendations[] = 'Keep hydration high and prioritize protein at each meal.';
    } elseif ($goal === 'weight-gain') {
        $recommendations[] = 'Add a calorie-dense snack if you feel hungry between meals.';
    } else {
        $recommendations[] = 'Maintain consistent meal timing and portion sizes for steady energy.';
    }

    if ($restrictions !== '') {
        $recommendations[] = 'The plan avoids the following preferences: ' . $restrictions;
    }

    return [
        'profile' => $profile,
        'diet_type' => $dietType,
        'goal' => $goal,
        'calories' => $targetCalories,
        'restrictions' => $restrictions,
        'ibw' => $ibw,
        'bmi' => $bmi,
        'bmr' => $bmr,
        'macros' => $macros,
        'meals' => $meals,
        'totals' => $totals,
        'summary' => 'This plan is tailored to your ' . getGoalLabel($goal) . ' goal and ' . getDietLabel($dietType) . ' preference.',
        'recommendations' => $recommendations,
        'generated_at' => date('Y-m-d H:i:s'),
    ];
}
