<?php
require_once __DIR__ . '/../config.php';
require_once APP_ROOT . '/includes/header.php';

$errors = [];
if (!empty($_SESSION['profile_errors'])) {
    $errors = $_SESSION['profile_errors'];
    unset($_SESSION['profile_errors']);
}

$defaults = $_SESSION['profile_form'] ?? [];
?>
<h1>Create Your Profile</h1>
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
<?php require_once APP_ROOT . '/includes/footer.php'; ?>
