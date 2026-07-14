<?php
require_once __DIR__ . '/../config.php';
require_once APP_ROOT . '/vendor/autoload.php';

$plan = $_SESSION['nutrition_plan'] ?? null;
if (!$plan) {
    redirectTo('nutrition/plan-view.php');
}

ob_start();
require APP_ROOT . '/pdf/pdf-template.php';
$html = ob_get_clean();

$mpdf = new \Mpdf\Mpdf([
    'tempDir' => sys_get_temp_dir() . '/mpdf'
]);
$mpdf->WriteHTML($html);
$filename = 'nutrition-plan-' . date('Ymd-His') . '.pdf';
$mpdf->Output($filename, \Mpdf\Output\Destination::DOWNLOAD);
