<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$envPath = __DIR__ . '/.env';
if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (str_starts_with(trim($line), '#')) {
            continue;
        }
        [$name, $value] = array_pad(explode('=', $line, 2), 2, '');
        $name = trim($name);
        $value = trim($value);
        if ($name !== '' && !array_key_exists($name, $_ENV)) {
            putenv("{$name}={$value}");
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}

const APP_NAME = 'AI Nutrition Dashboard';
const APP_ROOT = __DIR__;

$baseUrl = getenv('APP_BASE_URL') ?: 'http://localhost/ai-nutrition-dashboard';
define('APP_BASE_URL', $baseUrl);
$basePath = parse_url(APP_BASE_URL, PHP_URL_PATH) ?: '/';
$basePath = rtrim($basePath, '/') ?: '/';
define('APP_BASE_PATH', $basePath);
define('AI_PROVIDER', getenv('AI_PROVIDER') ?: 'mock');
define('AI_API_KEY', getenv('AI_API_KEY') ?: '');

require_once __DIR__ . '/includes/functions.php';
