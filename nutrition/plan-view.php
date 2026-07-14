<?php
require_once __DIR__ . '/../config.php';
require_once APP_ROOT . '/includes/header.php';

$plan = $_SESSION['nutrition_plan'] ?? null;
?>
<h1>Your Nutrition Plan</h1>
<?php if ($plan): ?>
    <div class="card hero-card">
        <p><strong>Plan for:</strong> <?= sanitizeInput($plan['profile']['name']) ?></p>
        <p><strong>Diet Type:</strong> <?= sanitizeInput(getDietLabel($plan['diet_type'])) ?></p>
        <p><strong>Goal:</strong> <?= sanitizeInput(getGoalLabel($plan['goal'])) ?></p>
        <p><strong>Daily Target:</strong> <?= (int) $plan['calories'] ?> kcal</p>
        <p><strong>IBW:</strong> <?= (float) $plan['ibw'] ?> kg | <strong>BMI:</strong> <?= (float) $plan['bmi'] ?></p>
        <p><strong>Summary:</strong> <?= sanitizeInput($plan['summary']) ?></p>
    </div>

    <div class="card">
        <h2>Daily Nutrition Summary</h2>
        <div class="stats-grid">
            <div class="stat-box"><strong data-summary="calories"><?= (int) $plan['totals']['calories'] ?></strong><span>Calories</span></div>
            <div class="stat-box"><strong data-summary="protein"><?= (int) $plan['totals']['protein'] ?>g</strong><span>Protein</span></div>
            <div class="stat-box"><strong data-summary="carbs"><?= (int) $plan['totals']['carbs'] ?>g</strong><span>Carbs</span></div>
            <div class="stat-box"><strong data-summary="fat"><?= (int) $plan['totals']['fat'] ?>g</strong><span>Fat</span></div>
        </div>
        <p><strong>Target Macros:</strong> Protein <?= (int) $plan['macros']['protein'] ?>g · Carbs <?= (int) $plan['macros']['carbs'] ?>g · Fat <?= (int) $plan['macros']['fat'] ?>g</p>
    </div>

    <div class="card">
        <h2>Meal Plan</h2>
        <div class="meal-list">
            <?php foreach ($plan['meals'] as $index => $meal): ?>
                <div class="meal-item" data-meal-index="<?= $index ?>">
                    <div class="meal-header">
                        <h3><?= sanitizeInput($meal['name']) ?></h3>
                        <span data-field="time"><?= sanitizeInput($meal['time']) ?></span>
                    </div>
                    <div class="meal-view">
                        <p><strong>Food:</strong> <span data-field="food"><?= sanitizeInput($meal['food']) ?></span></p>
                        <p><strong>Quantity:</strong> <span data-field="quantity"><?= sanitizeInput($meal['quantity']) ?></span></p>
                        <p><strong>Calories:</strong> <span data-field="calories"><?= (int) $meal['calories'] ?></span> kcal</p>
                        <p><strong>Protein:</strong> <span data-field="protein"><?= (int) $meal['protein'] ?></span>g · Carbs <span data-field="carbs"><?= (int) $meal['carbs'] ?></span>g · Fat <span data-field="fat"><?= (int) $meal['fat'] ?></span>g</p>
                        <button type="button" data-edit-meal="<?= $index ?>">Edit Meal</button>
                    </div>
                    <form class="meal-edit-form" style="display:none;">
                        <label>
                            Food name
                            <input type="text" name="food" value="<?= sanitizeInput($meal['food']) ?>">
                        </label>
                        <label>
                            Quantity
                            <input type="text" name="quantity" value="<?= sanitizeInput($meal['quantity']) ?>">
                        </label>
                        <label>
                            Time
                            <input type="time" name="time" value="<?= sanitizeInput($meal['time']) ?>">
                        </label>
                        <div class="form-actions">
                            <button type="submit" class="save-btn">Save</button>
                            <button type="button" class="cancel-btn">Cancel</button>
                        </div>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="card">
        <h2>Recommendations</h2>
        <ul>
            <?php foreach ($plan['recommendations'] as $recommendation): ?>
                <li><?= sanitizeInput($recommendation) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>

    <p><a href="<?= appUrl('pdf/export-plan.php') ?>">Export as PDF</a></p>
<?php else: ?>
    <p>No plan generated yet. <a href="<?= appUrl('nutrition/preferences-form.php') ?>">Create one</a>.</p>
<?php endif; ?>
<?php require_once APP_ROOT . '/includes/footer.php'; ?>
