<?php
require 'vendor/autoload.php';
require 'db.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

$spreadsheet = new Spreadsheet();

// ---------- TABLE 1 : PAR DIPLÔME ----------
$sheet1 = $spreadsheet->getActiveSheet();
$sheet1->setTitle('Par Diplôme');

$headers = ['Diplôme', 'Total', 'M', 'F'];
$col = 'A';
foreach ($headers as $header) {
    $sheet1->setCellValue($col.'1', $header);
    $sheet1->getStyle($col.'1')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
    $sheet1->getStyle($col.'1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('4F81BD');
    $sheet1->getStyle($col.'1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet1->getStyle($col.'1')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
    $col++;
}

$diplomes = $pdo->query("SELECT DISTINCT Diplome FROM etudiants_diplomé")->fetchAll(PDO::FETCH_COLUMN);
$row = 2;

foreach ($diplomes as $diplome) {
    $total = $pdo->prepare("SELECT COUNT(*) FROM etudiants_diplomé WHERE Diplome=?");
    $total->execute([$diplome]);
    $totalCount = $total->fetchColumn();

    $countM = $pdo->prepare("SELECT COUNT(*) FROM etudiants_diplomé WHERE Diplome=? AND Sexe='M'");
    $countM->execute([$diplome]);
    $totalM = $countM->fetchColumn();

    $countF = $pdo->prepare("SELECT COUNT(*) FROM etudiants_diplomé WHERE Diplome=? AND Sexe='F'");
    $countF->execute([$diplome]);
    $totalF = $countF->fetchColumn();

    $sheet1->setCellValue("A$row", $diplome);
    $sheet1->setCellValue("B$row", $totalCount);
    $sheet1->setCellValue("C$row", $totalM);
    $sheet1->setCellValue("D$row", $totalF);

    foreach (range('A','D') as $col) {
        $sheet1->getStyle($col.$row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet1->getStyle($col.$row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    }

    $sheet1->getStyle("C$row")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D9E1F2');
    $sheet1->getStyle("D$row")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F4CCCC');

    $row++;
}

// AutoSize
foreach (range('A','D') as $col) {
    $sheet1->getColumnDimension($col)->setAutoSize(true);
}

// ---------- TABLE 2 : PAR FILIÈRE ----------
$sheet2 = $spreadsheet->createSheet();
$sheet2->setTitle('Par Filière');

$headers2 = ['Filière', 'Total', 'M', 'F'];
$col = 'A';
foreach ($headers2 as $header) {
   $sheet2->setCellValue($col.'1', $header);
    $sheet2->getStyle($col.'1')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
    $sheet2->getStyle($col.'1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('4F81BD');
    $sheet2->getStyle($col.'1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet2->getStyle($col.'1')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
    $col++;
}

$filieres = $pdo->query("SELECT DISTINCT Filiere FROM etudiants_diplomé")->fetchAll(PDO::FETCH_COLUMN);
$row = 2;

foreach ($filieres as $filiere) {
    $total = $pdo->prepare("SELECT COUNT(*) FROM etudiants_diplomé WHERE Filiere=?");
    $total->execute([$filiere]);
    $totalCount = $total->fetchColumn();

    $countM = $pdo->prepare("SELECT COUNT(*) FROM etudiants_diplomé WHERE Filiere=? AND Sexe='M'");
    $countM->execute([$filiere]);
    $totalM = $countM->fetchColumn();

    $countF = $pdo->prepare("SELECT COUNT(*) FROM etudiants_diplomé WHERE Filiere=? AND Sexe='F'");
    $countF->execute([$filiere]);
    $totalF = $countF->fetchColumn();

    $sheet2->setCellValue("A$row", $filiere);
    $sheet2->setCellValue("B$row", $totalCount);
    $sheet2->setCellValue("C$row", $totalM);
    $sheet2->setCellValue("D$row", $totalF);

    foreach (range('A','D') as $col) {
        $sheet2->getStyle($col.$row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet2->getStyle($col.$row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    }
    
    $sheet2->getStyle("C$row")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D9E1F2');
    $sheet2->getStyle("D$row")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F4CCCC');

    $row++;
}

foreach (range('A','D') as $col) {
    $sheet2->getColumnDimension($col)->setAutoSize(true);
}

// ---------- EXPORT ----------
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="etudiants_diplomes.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
