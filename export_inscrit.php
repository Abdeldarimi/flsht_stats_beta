<?php
require 'vendor/autoload.php';
require 'db.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$headers = ['Total Inscrits', 'Total Non Inscrits', 'Total Non inscrit M', 'Total Non inscrit F', 'Total Inscrit F', 'Total Non inscrit M'];
 
$col = 'A';
foreach ($headers as $header) {
    $sheet->setCellValue($col.'1', $header);
    $sheet->getStyle($col.'1')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
    $sheet->getStyle($col.'1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('4F81BD'); 
    $sheet->getStyle($col.'1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle($col.'1')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
    $col++;
}

$diplomes = $pdo->query("SELECT DISTINCT Diplome FROM etudiants")->fetchAll(PDO::FETCH_COLUMN);

$row = 2; 

    $inscrit = $pdo->query("SELECT COUNT(*) FROM etudiants WHERE Inscrit = 'OUI'")->fetchColumn();
    $non_inscrit = $pdo->query("SELECT COUNT(*) FROM etudiants WHERE Inscrit = 'NON'")->fetchColumn();
    $non_inscrit_f = $pdo->query("SELECT COUNT(*) FROM etudiants WHERE Inscrit = 'NON' AND Sexe = 'F'")->fetchColumn();
    $non_inscrit_m = $pdo->query("SELECT COUNT(*) FROM etudiants WHERE Inscrit = 'NON' AND Sexe = 'M'")->fetchColumn();
    $inscrit_f = $pdo->query("SELECT COUNT(*) FROM etudiants WHERE Inscrit = 'OUI' AND Sexe = 'F'")->fetchColumn();
    $inscrit_m = $pdo->query("SELECT COUNT(*) FROM etudiants WHERE Inscrit = 'OUI' AND Sexe = 'M'")->fetchColumn();

    $sheet->setCellValue("A$row", $inscrit);
    $sheet->setCellValue("B$row", $non_inscrit);
    $sheet->setCellValue("C$row", $non_inscrit_f);
    $sheet->setCellValue("D$row", $non_inscrit_m);
    $sheet->setCellValue("E$row", $inscrit_f);
    $sheet->setCellValue("F$row", $inscrit_m);

    foreach (range('A','F') as $col) {
        $sheet->getStyle($col.$row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle($col.$row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    }

    $sheet->getStyle("C$row")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D9E1F2'); 
    $sheet->getStyle("D$row")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F4CCCC'); 
    $sheet->getStyle("E$row")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D9E1F2'); 
    $sheet->getStyle("F$row")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F4CCCC'); 

    $row++;


foreach (range('A','F') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="etudiants_inscrits.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
