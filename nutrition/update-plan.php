<?php
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json');

$plan = $_SESSION['nutrition_plan'] ?? null;
if (!$plan) {
    echo json_encode(['success' => false, 'message' => 'No plan available']);
    exit;
}

$mealIndex = (int) ($_POST['meal_index'] ?? -1);
$food = trim((string) ($_POST['food'] ?? ''));
$quantity = trim((string) ($_POST['quantity'] ?? ''));
$time = trim((string) ($_POST['time'] ?? ''));
$calories = (int) ($_POST['calories'] ?? 0);
$protein = (int) ($_POST['protein'] ?? 0);
$carbs = (int) ($_POST['carbs'] ?? 0);
$fat = (int) ($_POST['fat'] ?? 0);

if ($mealIndex >= 0 && isset($plan['meals'][$mealIndex])) {
    $plan['meals'][$mealIndex]['food'] = $food;
    $plan['meals'][$mealIndex]['quantity'] = $quantity;
    $plan['meals'][$mealIndex]['time'] = $time;
    $plan['meals'][$mealIndex]['calories'] = $calories;
    $plan['meals'][$mealIndex]['protein'] = $protein;
    $plan['meals'][$mealIndex]['carbs'] = $carbs;
    $plan['meals'][$mealIndex]['fat'] = $fat;

    $totals = ['calories' => 0, 'protein' => 0, 'carbs' => 0, 'fat' => 0];
    foreach ($plan['meals'] as $meal) {
        $totals['calories'] += (int) $meal['calories'];
        $totals['protein'] += (int) $meal['protein'];
        $totals['carbs'] += (int) $meal['carbs'];
        $totals['fat'] += (int) $meal['fat'];
    }
    $plan['totals'] = $totals;
    $_SESSION['nutrition_plan'] = $plan;

    echo json_encode(['success' => true, 'plan' => $plan]);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid meal']);
