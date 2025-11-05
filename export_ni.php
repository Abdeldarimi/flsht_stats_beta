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

$headers = ['NI', 'Total_etudiants', 'M', 'F', 'Inscrit', 'Non Inscrit'];

// Entêtes
$col = 'A';
foreach ($headers as $header) {
    $sheet->setCellValue($col.'1', $header);
    $sheet->getStyle($col.'1')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
    $sheet->getStyle($col.'1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('4F81BD'); 
    $sheet->getStyle($col.'1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle($col.'1')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
    $col++;
}

$NI_list = $pdo->query("SELECT DISTINCT NI FROM etudiants ORDER BY NI DESC")->fetchAll(PDO::FETCH_COLUMN);

$row = 2; 
foreach ($NI_list as $y) {
    $M = $pdo->prepare("SELECT COUNT(*) FROM etudiants WHERE NI = ? AND Sexe = 'M'");
    $M->execute([$y]);
    $F = $pdo->prepare("SELECT COUNT(*) FROM etudiants WHERE NI = ? AND Sexe = 'F'");
    $F->execute([$y]);
    $insc = $pdo->prepare("SELECT COUNT(*) FROM etudiants WHERE NI = ? AND Inscrit = 'OUI'");
    $insc->execute([$y]);
    $non_insc = $pdo->prepare("SELECT COUNT(*) FROM etudiants WHERE NI = ? AND (Inscrit != 'OUI' OR Inscrit IS NULL)");
    $non_insc->execute([$y]);

    $count_M = $M->fetchColumn();
    $count_F = $F->fetchColumn();
    $count_insc = $insc->fetchColumn();
    $count_non_insc = $non_insc->fetchColumn();
    $total_MF = $count_M + $count_F;

    $sheet->setCellValue("A$row", $y);
    $sheet->setCellValue("B$row", $total_MF);
    $sheet->setCellValue("C$row", $count_M);
    $sheet->setCellValue("D$row", $count_F);
    $sheet->setCellValue("E$row", $count_insc);
    $sheet->setCellValue("F$row", $count_non_insc);

    foreach (range('A','F') as $col) {
        $sheet->getStyle($col.$row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle($col.$row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    }

    $sheet->getStyle("C$row")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D9E1F2'); 
    $sheet->getStyle("D$row")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F4CCCC'); 

    $row++;
}

foreach (range('A','F') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="etudiants_ni.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
