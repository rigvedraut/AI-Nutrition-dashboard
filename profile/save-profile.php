<?php
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirectTo('profile/profile-form.php');
}

$errors = validate_profile($_POST);
if (!$errors) {
    $name = trim($_POST['name']);
    $age = (int) $_POST['age'];
    $gender = strtolower(trim($_POST['gender']));
    $heightCm = (float) $_POST['height_cm'];
    $weightKg = (float) $_POST['weight_kg'];
    $ibw = calculateIBW($gender, $heightCm);

    $_SESSION['profile'] = [
        'name' => $name,
        'age' => $age,
        'gender' => $gender,
        'height_cm' => $heightCm,
        'weight_kg' => $weightKg,
        'ibw' => $ibw,
    ];

    $_SESSION['profile_form'] = $_POST;

    redirectTo('index.php#preferences');
}

$_SESSION['profile_errors'] = $errors;
$_SESSION['profile_form'] = $_POST;
redirectTo('profile/profile-form.php');
