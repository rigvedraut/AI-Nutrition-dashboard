<?php
require_once __DIR__ . '/../config.php';
require_once APP_ROOT . '/includes/header.php';

$profile = $_SESSION['profile'] ?? null;
$defaultCalories = $profile ? estimate_daily_calories($profile, 'maintenance') : 2200;
?>
<h1>Nutrition Preferences</h1>
<?php if (!$profile): ?>
    <p>Please complete your profile first.</p>
<?php else: ?>
    <form action="<?= appUrl('nutrition/generate-plan.php') ?>" method="post" class="card">
        <div class="form-grid">
            <label>
                Diet type
                <select name="diet_type" required>
                    <option value="vegetarian">Vegetarian</option>
                    <option value="vegan">Vegan</option>
                    <option value="eggetarian">Eggetarian</option>
                    <option value="non_vegetarian">Non-Vegetarian</option>
                </select>
            </label>
            <label>
                Fitness goal
                <select name="goal" required>
                    <option value="maintenance">Maintain Weight</option>
                    <option value="weight-loss">Weight Loss</option>
                    <option value="weight-gain">Weight Gain</option>
                </select>
            </label>
            <label>
                Daily calories target
                <input type="number" name="calories" min="1200" max="4000" value="<?= (int) $defaultCalories ?>" required>
            </label>
            <label>
                Allergies or dislikes
                <input type="text" name="restrictions" placeholder="e.g. dairy, nuts">
            </label>
        </div>
        <button type="submit">Generate Plan</button>
    </form>
<?php endif; ?>
<?php require_once APP_ROOT . '/includes/footer.php'; ?>
