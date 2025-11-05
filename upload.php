<?php
ini_set('memory_limit', '3000M');
set_time_limit(300);

require 'db.php';
require 'auth.php';
require_login();
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$mode = $_GET['mode'] ?? 'etudiants';
$msg = '';

if (isset($_FILES['excel'])) {
    $file = $_FILES['excel']['tmp_name'];
    $mode = $_POST['mode'] ?? 'etudiants';
    $response = ['success' => false, 'msg' => ''];

    try {
        $spreadsheet = IOFactory::load($file);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();
        $count = 0;

        foreach ($rows as $i => $row) {
            if ($i === 0) continue;
            if ($mode === 'etudiants') {
                $codeapo = $row[0] ?? '';
                $cne = $row[1] ?? '';
                $nom = $row[2] ?? '';
                $prenom = $row[3] ?? '';
                $cin = $row[4] ?? '';
                $date_nais = $row[5] ?? '';
                $sexe = $row[6] ?? '';
                $lieu_nais = $row[7] ?? '';
                $ni = $row[8] ?? '';
                $diplome = $row[9] ?? '';
                $inscrit = $row[10] ?? '';
                if (trim($prenom) === '') continue;
                $stmt = $pdo->prepare("INSERT INTO etudiants (CODAPO, CNE, Nom, Prenom, CIN, Date_de_nais, Sexe, Lieu_de_nais, NI, Inscrit, Diplome) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$codeapo, $cne, $nom, $prenom, $cin, $date_nais, $sexe, $lieu_nais, $ni, $inscrit, $diplome]);
                $count++;
            } elseif ($mode === 'etudiants_diplomé') {
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
                if (trim($prenom) === '') continue;
                $stmt = $pdo->prepare("INSERT INTO etudiants_diplomé (CODAPO, cne, Nom, Prenom, CIN, DATE_NAIS, Sexe, Lieux_de_nais, NI, Filiere, Diplome, Pays, Hand) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$codeapo, $cne, $nom, $prenom, $cin, $date_nais, $sexe, $lieu_nais, $ni, $filiere, $diplome, $pay, $hand]);
                $count++;
            } else {
                $first = $row[0] ?? '';
                $etudiant_id = null;
                if (is_numeric($first)) {
                    $etudiant_id = (int)$first;
                    $matiere = $row[1] ?? '';
                    $note = $row[2] ?? '';
                    $annee = $row[3] ?? '';
                } else {
                    $prenom = $row[0] ?? '';
                    $nom = $row[1] ?? '';
                    $s = $pdo->prepare("SELECT id FROM etudiants WHERE prenom = ? AND nom = ? LIMIT 1");
                    $s->execute([$prenom, $nom]);
                    $res = $s->fetch();
                    if ($res) $etudiant_id = $res['id'];
                    $matiere = $row[2] ?? '';
                    $note = $row[3] ?? '';
                    $annee = $row[4] ?? '';
                }
                if (!$etudiant_id) continue;
                $stmt = $pdo->prepare("INSERT INTO notes (etudiant_id, matiere, note, annee) VALUES (?, ?, ?, ?)");
                $stmt->execute([$etudiant_id, $matiere, (float)$note, $annee]);
                $count++;
            }
        }

        $response['success'] = true;
        $response['msg'] = "تم استيراد: $count سجل(س)";
    } catch (Exception $e) {
        $response['msg'] = 'خطأ أثناء قراءة الملف: ' . $e->getMessage();
    }

    echo json_encode($response);
    exit;
}
?>

<?php include 'header.php'; ?>
<?php include 'layout_top.php'; ?>

<div class="ht">
<a href="index.php" class="hover:text-blue-500">عودة للوحة</a>
<h1 class="text-xl mb-1">رفع ملف Excel</h1>

<div class="mb-1">
    <a href="?mode=etudiants" class="px-2 py-1 rounded hover:text-blue-500 <?= $mode==='etudiants'?'bg-blue-50':'' ?>">رفع طلاب</a>
    <a href="?mode=etudiants_diplomé" class="px-2 py-1 rounded hover:text-blue-800 <?= $mode==='etudiants_diplomé'?'bg-blue-50':'' ?>">رفع طلاب diplome</a>
    <!-- <a href="?mode=notes" class="px-2 py-1 rounded hover:text-blue-500 <?= $mode==='notes'?'bg-blue-50':'' ?>">رفع نقاط</a> -->

</div>

<form id="uploadForm" enctype="multipart/form-data" class="p-2 border rounded bg-white">
    <input type="hidden" name="mode" value="<?= htmlspecialchars($mode) ?>">
    <div class="mb-2">
        <label class="block mb-1">اختر ملف Excel:</label>
        <input type="file" name="excel" accept=".xlsx,.xls" required>
    </div>
    <button type="submit" class="px-4 py-2 rounded bg-blue-500 text-white hover:bg-blue-400">رفع الملف</button>
</form>

<div id="progressContainer" class="w-full bg-gray-200 rounded mt-3 hidden">
    <div id="progressBar" class="bg-blue-500 text-white text-xs text-center p-0.5 leading-none rounded-l" style="width:0%">0%</div>
</div>

<div id="uploadStatus" class="mt-3 text-sm font-medium"></div>
</div>
<a href="download_temp.php?download_template=1&mode=<?= htmlspecialchars($mode) ?>" 
   class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-400">
   Télécharger template Excel
</a>

<script>
document.getElementById('uploadForm').addEventListener('submit', function(e){
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    const xhr = new XMLHttpRequest();
    const progressContainer = document.getElementById('progressContainer');
    const progressBar = document.getElementById('progressBar');
    const uploadStatus = document.getElementById('uploadStatus');
    progressContainer.classList.remove('hidden');
    progressBar.style.width = '0%';
    progressBar.textContent = '0%';
    uploadStatus.textContent = '';
    xhr.upload.addEventListener('progress', (e) => {
        if (e.lengthComputable) {
            const percent = Math.round((e.loaded / e.total) * 100);
            progressBar.style.width = percent + '%';
            progressBar.textContent = percent + '%';
        }
    });
    xhr.onreadystatechange = function(){
        if(xhr.readyState === XMLHttpRequest.DONE){
            try {
                const res = JSON.parse(xhr.responseText);
                if(res.success){
                    progressBar.classList.replace('bg-blue-500','bg-green-500');
                    uploadStatus.innerHTML = `<span class="text-green-600">${res.msg}</span>`;
                } else {
                    progressBar.classList.replace('bg-blue-500','bg-red-500');
                    uploadStatus.innerHTML = `<span class="text-red-600">${res.msg}</span>`;
                }
            } catch {
                uploadStatus.innerHTML = `<span class="text-red-600">حدث خطأ أثناء رفع الملف.</span>`;
            }
        }
    };
    xhr.open('POST', 'upload.php');
    xhr.send(formData);
});
</script>
<div class="mt-4 text-sm text-gray-600">
    <p>Formats suggérés pour les étudiants : <code>CODAPO, CNE, Nom, Prénom, CIN, Date_de_nais, Sexe, Lieu_de_nais, NI, Diplome, Inscrit(OUI/NON)</code></p>
    <p>Formats suggérés pour les étudiants diplômés : <code>CODAPO, CNE, Nom, Prénom, CIN, Date_Nais, Sexe, Lieu_Nais, NI, Filiere, Diplome, Pays, Hand</code></p>
</div>


<?php include 'layout_bottom.php'; ?>
