<?php
require 'db.php';
require 'auth.php';
require_login();
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$mode = $_GET['mode'] ?? 'etudiants'; // etudiants or notes
$msg = '';
if(isset($_POST['upload'])){
    if(empty($_FILES['excel']['tmp_name'])){ $_SESSION['flash']['error'] = 'المرجو اختيار ملف'; }
    else{
        $file = $_FILES['excel']['tmp_name'];
        try{
            $spreadsheet = IOFactory::load($file);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();
            $count = 0;
            foreach($rows as $i => $row){
                // skip header if present: check first row has non-numeric in first col
                if($i==0 && (is_string($row[0]) && strtolower(trim($row[0]))== 'prenom')) continue;
                if($mode === 'etudiants'){
                    // expect: prenom, nom, division, annee
                    $prenom = $row[0] ?? '';
                    $nom = $row[1] ?? '';
                    $division = $row[2] ?? '';
                    $annee = $row[3] ?? '';
                    if(trim($prenom)==='') continue;
                    $stmt = $pdo->prepare("INSERT INTO etudiants (prenom, nom, division, annee) VALUES (?, ?, ?, ?)"); 
                    $stmt->execute([$prenom,$nom,$division,$annee]);
                    $count++;
                } else if($mode === 'etudiant_diplomé'){
                    // expect: prenom, nom, division, annee
                    $codeapo = $row[0] ?? '';
                    $cne = $row[1] ?? '';
                    $nom = $row[2] ?? '';
                    $prenom = $row[3] ?? '';
                    $cin = $row[4] ?? '';
                    $date_nais = $row[5] ?? '';
                    $sexe = $row[6] ?? '';
                    $lieu_nais = $row[7] ?? '';
                    $ni = $row[8] ?? '';
                    $filiere = $row[9] ?? '';
                    $diplome = $row[10] ?? '';
                    $pay = $row[11] ?? '';
                    $hand = $row[12] ?? '';
                    if(trim($prenom)==='') continue;
                    $stmt = $pdo->prepare("INSERT INTO etudiants_diplomé (CODAPO, cne, Nom, Prenom, CIN, DATE_NAIS, Sexe, Lieux_de_nais, NI, Filiere, Diplome, Pays, Hand ) VALUES (?, ?, ?, ?,?, ?, ?, ?, ?, ?, ?, ?, ?)"); 
                    $stmt->execute([$codeapo,$cne,$nom,$prenom,$cin,$date_nais,$sexe,$lieu_nais,$ni,$filiere,$diplome,$pay,$hand]);
                    $count++;
                }
                else {
                    // notes mode
                    $first = $row[0] ?? '';
                    $etudiant_id = null;
                    if(is_numeric($first)){
                        $etudiant_id = (int)$first;
                        $matiere = $row[1] ?? '';
                        $note = $row[2] ?? '';
                        $annee = $row[3] ?? '';
                    } else {
                        $prenom = $row[0] ?? '';
                        $nom = $row[1] ?? '';
                        $s = $pdo->prepare("SELECT id FROM etudiants WHERE prenom = ? AND nom = ? LIMIT 1");
                        $s->execute([$prenom,$nom]);
                        $res = $s->fetch();
                        if($res) $etudiant_id = $res['id'];
                        $matiere = $row[2] ?? '';
                        $note = $row[3] ?? '';
                        $annee = $row[4] ?? '';
                    }
                    if(!$etudiant_id) continue;
                    $stmt = $pdo->prepare("INSERT INTO notes (etudiant_id, matiere, note, annee) VALUES (?, ?, ?, ?)"); 
                    $stmt->execute([$etudiant_id,$matiere,(float)$note,$annee]);
                    $count++;
                }
            }
            $_SESSION['flash']['success'] = "تم استيراد: $count سجل(س)";
        } catch(Exception $e){
            $_SESSION['flash']['error'] = 'خطأ أثناء قراءة الملف: '. $e->getMessage();
        }
    }
    header('Location: upload.php?mode='.$mode); exit;
}
include 'header.php';

include 'layout_top.php';
?>
<div class="max-w-4xl mx-auto">
<a href="index.php" class="hover:text-blue-500">عودة للوحة</a>
<h1 class="text-2xl mb-4">رفع ملف Excel</h1>

<div class="mb-4">
    <a href="upload.php?mode=etudiants" class="px-3 py-1 rounded hover:text-blue-500 <?= $mode==='etudiants'?'bg-blue-50':'' ?>">رفع طلاب</a>
    <a href="upload.php?mode=etudiant_diplomé" class="px-3 py-1 rounded hover:text-blue-500 <?= $mode==='etudiant_diplomé'?'bg-blue-50':'' ?>">رفع طلاب diplome</a>
    <a href="upload.php?mode=notes" class="px-3 py-1 rounded hover:text-blue-500 <?= $mode==='notes'?'bg-blue-50':'' ?>">رفع نقاط</a>
</div>

<?php if(!empty($_SESSION['flash']['success'])): ?>
    <div class="p-3 mb-3 rounded bg-green-50 text-green-700"><?= htmlspecialchars($_SESSION['flash']['success']); unset($_SESSION['flash']['success']); ?></div>
<?php endif; ?>
<?php if(!empty($_SESSION['flash']['error'])): ?>
    <div class="p-3 mb-3 rounded bg-red-50 text-red-700"><?= htmlspecialchars($_SESSION['flash']['error']); unset($_SESSION['flash']['error']); ?></div>
<?php endif; ?>

<form method="post" enctype="multipart/form-data" class="p-4 border rounded bg-white">
    <div class="mb-3">
<label class="block mb-1">
    نوع الاستيراد: 
    <strong>
        <?= $mode === 'etudiants' ? 'طلبة' : ($mode === 'etudiant_diplomé' ? 'طلبة دبلوم' : 'نقاط') ?>
    </strong>
</label>
        <input type="file" name="excel" accept=".xlsx,.xls" required>
    </div>
    <button type="submit" name="upload" class="px-4 py-2 rounded bg-blue-500 text-white hover:bg-blue-400 transition">رفع الملف</button>
</form>

<div class="mt-4 text-sm text-gray-600">
    <p>تنسيقات مقترحة للطلاب: <code>prenom, nom, division, annee</code></p>
    <p>تنسيقات مقترحة للنقاط: إما <code>etudiant_id, matiere, note, annee</code> أو <code>prenom, nom, matiere, note, annee</code></p>
</div>
</div>
<?php include 'layout_bottom.php'; ?>
