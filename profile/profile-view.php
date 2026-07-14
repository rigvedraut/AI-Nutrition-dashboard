<?php
require_once __DIR__ . '/../config.php';
require_once APP_ROOT . '/includes/header.php';

$profile = $_SESSION['profile'] ?? null;
?>
<h1>Your Profile Summary</h1>
<?php if ($profile): ?>
    <div class="card">
        <p><strong>Name:</strong> <?= sanitizeInput($profile['name']) ?></p>
        <p><strong>Age:</strong> <?= (int) $profile['age'] ?></p>
        <p><strong>Gender:</strong> <?= sanitizeInput($profile['gender']) ?></p>
        <p><strong>Height:</strong> <?= (float) $profile['height_cm'] ?> cm</p>
        <p><strong>Weight:</strong> <?= (float) $profile['weight_kg'] ?> kg</p>
        <p><strong>Ideal Body Weight:</strong> <?= (float) $profile['ibw'] ?> kg</p>
        <p><strong>BMI:</strong> <?= calculateBMI((float) $profile['weight_kg'], (float) $profile['height_cm']) ?></p>
        <p><strong>BMR:</strong> <?= calculateBMR((string) $profile['gender'], (float) $profile['height_cm'], (float) $profile['weight_kg'], (int) $profile['age']) ?> kcal/day</p>
        <p><strong>Health Note:</strong> Keep protein intake steady and review your plan with a professional if your BMI is outside a healthy range.</p>
    </div>
    <p><a href="<?= appUrl('nutrition/preferences-form.php') ?>">Continue to nutrition preferences</a></p>
<?php else: ?>
    <p>No profile saved yet. <a href="<?= appUrl('profile/profile-form.php') ?>">Create one</a>.</p>
<?php endif; ?>
<?php require_once APP_ROOT . '/includes/footer.php'; ?>
