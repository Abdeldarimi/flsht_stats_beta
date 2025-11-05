<?php
require 'vendor/autoload.php';
    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
if(isset($_GET['download_template'])){
    


    $mode = $_GET['mode'] ?? 'etudiants';
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    if($mode === 'etudiants'){
        $headers = ['CODAPO','CNE','Nom','Prenom','CIN','Date_de_nais','Sexe','Lieu_de_nais','NI','Inscrit','Diplome'];
    } else if($mode === 'etudiants_diplomé'){
        $headers = ['CODAPO','CNE','Nom','Prenom','CIN','DATE_NAIS','Sexe','Lieux_de_nais','NI','Filiere','Diplome','Pays','Hand'];
    } else {
        $headers = ['etudiant_id','Matiere','Note','Annee']; // notes
    }

    $col = 'A';
    foreach($headers as $header){
        $sheet->setCellValue($col.'1', $header);
        $col++;
    }

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="liste_'.$mode.'.xlsx"');
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}
?>