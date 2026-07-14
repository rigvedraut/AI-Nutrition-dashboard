<?php
require_once __DIR__ . '/config.php';
require_once APP_ROOT . '/includes/header.php';

$errors = [];
if (!empty($_SESSION['profile_errors'])) {
    $errors = $_SESSION['profile_errors'];
    unset($_SESSION['profile_errors']);
}

$defaults = $_SESSION['profile_form'] ?? [];
$profile = $_SESSION['profile'] ?? null;
$plan = $_SESSION['nutrition_plan'] ?? null;
$defaultCalories = $profile ? estimate_daily_calories($profile, 'maintenance') : 2200;
?>
<section class="hero-grid">
    <div class="hero-copy">
        <p class="eyebrow">Personalized nutrition, made simple</p>
        <h1>Your personal AI.</h1>
        <h1>Nutrition Dashboard.</h1>
        <p>Get your personalized nutrition plan and achive your fitness goals.</p>
        <a href="#assessment" class="btn btn-primary" data-scroll-target="#assessment">Start Your Assessment</a>
    </div>
<br>
    <div class="card hero-preview">
        <h2>What you will get</h2>
        <ul>
            <li>Daily calorie guidance based on your profile</li>
            <li>A balanced meal plan with meal timing</li>
            <li>A downloadable PDF summary</li>
        </ul>
    </div>
</section>


<section id="assessment" class="assessment-section">
    <div class="section-heading">
        <h2>1. Tell us about you</h2>
        <p>We use your details to estimate your ideal daily energy needs.</p>
    </div>

    <form action="<?= appUrl('profile/save-profile.php') ?>" method="post" class="card">
        <div class="form-grid">
            <label>
                Full name
                <input type="text" name="name" value="<?= getFormValue($defaults, 'name') ?>" required>
                <?php if (!empty($errors['name'])): ?><span class="field-error"><?= sanitizeInput($errors['name']) ?></span><?php endif; ?>
            </label>
            <label>
                Age
                <input type="number" name="age" min="1" max="120" value="<?= getFormValue($defaults, 'age') ?>" required>
                <?php if (!empty($errors['age'])): ?><span class="field-error"><?= sanitizeInput($errors['age']) ?></span><?php endif; ?>
            </label>
            <label>
                Gender
                <select name="gender" required>
                    <option value="">Select</option>
                    <option value="male" <?= (getFormValue($defaults, 'gender') === 'male') ? 'selected' : '' ?>>Male</option>
                    <option value="female" <?= (getFormValue($defaults, 'gender') === 'female') ? 'selected' : '' ?>>Female</option>
                </select>
                <?php if (!empty($errors['gender'])): ?><span class="field-error"><?= sanitizeInput($errors['gender']) ?></span><?php endif; ?>
            </label>
            <label>
                Height (cm)
                <input type="number" step="0.1" name="height_cm" value="<?= getFormValue($defaults, 'height_cm') ?>" placeholder="Height in cm, e.g. 175" min="100" max="250" required>
                <?php if (!empty($errors['height_cm'])): ?><span class="field-error"><?= sanitizeInput($errors['height_cm']) ?></span><?php endif; ?>
            </label>
            <label>
                Weight (kg)
                <input type="number" step="0.1" name="weight_kg" value="<?= getFormValue($defaults, 'weight_kg') ?>" min="30" max="300" required>
                <?php if (!empty($errors['weight_kg'])): ?><span class="field-error"><?= sanitizeInput($errors['weight_kg']) ?></span><?php endif; ?>
            </label>
        </div>
        <button type="submit">Save Profile</button>
    </form>

    <?php if ($profile): ?>
        <div id="preferences" class="section-heading">
            <h2>2. Choose your nutrition preferences</h2>
            <p>These choices help shape the plan you receive.</p>
        </div>
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

    <?php if ($plan): ?>
        <div id="plan" class="section-heading">
            <h2>3. Your nutrition plan</h2>
            <p>Your plan is ready, and you can edit meals or export it as a PDF.</p>
        </div>
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

        <p><a href="<?= appUrl('pdf/export-plan.php') ?>" class="btn btn-primary">Export as PDF</a></p>
    <?php elseif ($profile): ?>
        <div class="card">
            <h2>3. Your plan will appear here</h2>
            <p>Once you choose your preferences and generate your plan, the completed nutrition plan and PDF export will appear right below.</p>
        </div>
    <?php endif; ?>
</section>
<?php require_once APP_ROOT . '/includes/footer.php'; ?>
