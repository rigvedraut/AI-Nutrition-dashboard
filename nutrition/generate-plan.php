<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../api/ai-client.php';  


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirectTo('nutrition/preferences-form.php');
}

$profile = $_SESSION['profile'] ?? null;
if (!$profile) {
    redirectTo('profile/profile-form.php');
}

$preferences = [
    'diet_type' => $_POST['diet_type'] ?? 'balanced',
    'goal' => $_POST['goal'] ?? 'maintenance',
    'calories' => (int) ($_POST['calories'] ?? 2200),
    'restrictions' => trim((string) ($_POST['restrictions'] ?? '')),
];

$aiClient = new AiClient();
$plan = $aiClient->generatePlan($profile, $preferences);
// var_dump($_ENV['AI_API_PROVIDER'], $_ENV['AI_API_KEY']);
$_SESSION['nutrition_plan'] = $plan;
redirectTo('index.php#plan');
