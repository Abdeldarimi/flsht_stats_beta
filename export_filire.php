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

$headers = ['Diplôme', 'Total', 'M', 'F', 'Inscrit', 'Non Inscrit'];
 
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
foreach ($diplomes as $diplome) {
    $total = $pdo->prepare("SELECT COUNT(*) FROM etudiants WHERE Diplome=?");
    $total->execute([$diplome]);
    $totalCount = $total->fetchColumn();

    $countM = $pdo->prepare("SELECT COUNT(*) FROM etudiants WHERE Diplome=? AND Sexe='M'");
    $countM->execute([$diplome]);
    $totalM = $countM->fetchColumn();

    $countF = $pdo->prepare("SELECT COUNT(*) FROM etudiants WHERE Diplome=? AND Sexe='F'");
    $countF->execute([$diplome]);
    $totalF = $countF->fetchColumn();

    $countInscrit = $pdo->prepare("SELECT COUNT(*) FROM etudiants WHERE Diplome=? AND Inscrit='oui'");
    $countInscrit->execute([$diplome]);
    $totalInscrit = $countInscrit->fetchColumn();

    $countNonInscrit = $pdo->prepare("SELECT COUNT(*) FROM etudiants WHERE Diplome=? AND (Inscrit!='oui' OR Inscrit IS NULL)");
    $countNonInscrit->execute([$diplome]);
    $totalNonInscrit = $countNonInscrit->fetchColumn();

    $sheet->setCellValue("A$row", $diplome);
    $sheet->setCellValue("B$row", $totalCount);
    $sheet->setCellValue("C$row", $totalM);
    $sheet->setCellValue("D$row", $totalF);
    $sheet->setCellValue("E$row", $totalInscrit);
    $sheet->setCellValue("F$row", $totalNonInscrit);

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
header('Content-Disposition: attachment;filename="étudiants_diplomes.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
