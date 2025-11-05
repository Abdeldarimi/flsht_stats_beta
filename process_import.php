<?php
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');

require 'db.php';
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

$mode = $_POST['mode'] ?? 'etudiants';
$file = $_FILES['excel']['tmp_name'] ?? '';

if (!$file) {
    echo "data: ERROR|لم يتم اختيار ملف\n\n";
    exit;
}

$reader = new Xlsx();
$reader->setReadDataOnly(true);
$spreadsheet = $reader->load($file);
$sheet = $spreadsheet->getActiveSheet();
$rows = $sheet->toArray();
$total = count($rows);
$count = 0;

foreach ($rows as $i => $row) {
    if ($i === 0) continue; // skip header


    $count++;

    $percent = intval(($count / $total) * 100);
    echo "data: $percent\n\n"; // إرسال النسبة إلى JS
    ob_flush();
    flush();
}

echo "data: 100\n\n"; // نهاية
flush();
