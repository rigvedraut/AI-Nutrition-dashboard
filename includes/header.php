<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= appUrl('assets/css/style.css') ?>">
    <script src="<?= appUrl('assets/js/validation.js') ?>" defer></script>
    <script src="<?= appUrl('assets/js/main.js') ?>" defer></script>
</head>
<body>
<header class="site-header">
    <div class="container">
        <a class="brand" href="<?= appUrl('index.php') ?>">AI Nutrition Dashboard</a>
        <!-- <nav class="top-nav">
            <a href="<?= appUrl('profile/profile-form.php') ?>">Profile</a>
            <a href="<?= appUrl('nutrition/preferences-form.php') ?>">Preferences</a>
            <a href="<?= appUrl('nutrition/plan-view.php') ?>">Plan</a>
        </nav> -->
    </div>
</header>
<main class="container">
<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$step = 1;
if ($currentPage === 'preferences-form.php') {
    $step = 2;
} elseif ($currentPage === 'plan-view.php' || $currentPage === 'generate-plan.php') {
    $step = 3;
}
?>
<div class="progress-bar" aria-label="Progress">
    <span class="progress-step <?= $step >= 1 ? 'active' : '' ?>">Step 1 of 3 · Profile</span>
    <span class="progress-step <?= $step >= 2 ? 'active' : '' ?>">Step 2 of 3 · Preferences</span>
    <span class="progress-step <?= $step >= 3 ? 'active' : '' ?>">Step 3 of 3 · Plan</span>
</div>
