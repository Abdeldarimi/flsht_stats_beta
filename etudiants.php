<?php
require 'db.php';
require 'auth.php';
require_login();

$division = $_GET['division'] ?? '';
$annee = $_GET['annee'] ?? '';

$limit = 12;

$page = isset($_GET['page']) && $_GET['page'] > 0 ? (int)$_GET['page'] : 1;

$offset = ($page - 1) * $limit;

$q = "SELECT * FROM etudiants WHERE 1";
$params = [];

if ($division) {
    $q .= " AND division = ?";
    $params[] = $division;
}
if ($annee) {
    $q .= " AND annee = ?";
    $params[] = $annee;
}

$countQ = str_replace('*', 'COUNT(*)', $q);
$countStmt = $pdo->prepare($countQ);
$countStmt->execute($params);
$total_rows = $countStmt->fetchColumn();
$total_pages = ceil($total_rows / $limit);
$total_etuds = $pdo->query("SELECT COUNT(*) FROM etudiants ")->fetchColumn();
$ni = $pdo->query("SELECT DISTINCT NI FROM  etudiants ORDER BY NI DESC")->fetchAll(PDO::FETCH_COLUMN);


$q .= " LIMIT $limit OFFSET $offset";

$stmt = $pdo->prepare($q);
$stmt->execute($params);
$rows = $stmt->fetchAll();


// get distinct divisions and annees for filters
// $divs = $pdo->query("SELECT DISTINCT division FROM etudiants")->fetchAll(PDO::FETCH_COLUMN);
// $annes = $pdo->query("SELECT DISTINCT annee FROM etudiants")->fetchAll(PDO::FETCH_COLUMN);
include 'header.php';

include 'layout_top.php';

?>
<div class="max-w-100 mx-auto">
    <header class=" flex  items-center">
        <h1 class="text-xl font-semibold ">قائمة الطلبة </h1>
    </header>
    <div class="bg-lighblue shadow rounded p-1 flex flex-col items-center">
        <div class="text-gray-500 mb-1">اجمالي الطلاب </div>
        <div class="text-3xl font-bold text-blue-600"><?php echo $total_etuds; ?></div>
    </div>

    <div class="menu">
        <div class="search">
            <input type="text" id="searchInput" onkeyup="" placeholder="ابحث عن طالب..." class="border p-1 rounded w-64">
        </div>
        
        <div class="btns">
            <a href="">liste d'étudiants</a>
            <a href="" >Diplome</a>
            <a href="">Sexe</a>
            <a href="">NI</a>
            <a href="">Inscrit</a>

        </div>
        
    </div>


<div id="tab-diplome" class="tab-content hidden">
    <a href="export_filire.php" class="red" >
                Telecharger fichier excel
            </a>
    <div class="bg-white shadow rounded p-1 flex flex-col items-center">
        <div class="text-gray-500 mb-1">عدد الدبلومات </div>
        <div class="text-3xl font-bold text-blue-600">
        <?php
            $diplomes_count = $pdo->query("SELECT COUNT(DISTINCT Diplome) FROM etudiants")->fetchColumn();
            echo  $diplomes_count;
        ?></div>
    </div>

  <div class="cards" >
    <?php
        $diplomes = $pdo->query("SELECT DISTINCT Diplome FROM etudiants")->fetchAll(PDO::FETCH_COLUMN);

        foreach($diplomes as $diplome){
            $total = $pdo->prepare("SELECT COUNT(*) FROM etudiants WHERE Diplome=?");
            $total->execute([$diplome]);
            $count = $total->fetchColumn();

            $totalM = $pdo->prepare("SELECT COUNT(*) FROM etudiants WHERE Diplome=? AND Sexe='M'");
            $totalM->execute([$diplome]);
            $countM = $totalM->fetchColumn();

            $totalF = $pdo->prepare("SELECT COUNT(*) FROM etudiants WHERE Diplome=? AND Sexe='F'");
            $totalF->execute([$diplome]);
            $countF = $totalF->fetchColumn();

            $totalInscrit = $pdo->prepare("SELECT COUNT(*) FROM etudiants WHERE Diplome=? AND Inscrit='oui'");
            $totalInscrit->execute([$diplome]);
            $countInscrit = $totalInscrit->fetchColumn();

            $totalNonInscrit = $pdo->prepare("SELECT COUNT(*) FROM etudiants WHERE Diplome=? AND (Inscrit!='oui' OR Inscrit IS NULL)");
            $totalNonInscrit->execute([$diplome]);
            $countNonInscrit = $totalNonInscrit->fetchColumn();

            echo "<div class='diplome-card p-4 border rounded shadow bg-white'>";
            echo "<h3 class='font-semibold text-blue-600 mb-2'>".htmlspecialchars($diplome)."</h3>";
            echo "<p>إجمالي الطلبة: $count</p>";
            echo "<p>الذكور: $countM | الإناث: $countF</p>";
            echo "<p>المسجلين: $countInscrit | غير المسجلين: $countNonInscrit</p>";
            echo "</div>";
        }
    ?>
  </div>
</div>



<div id="tab-sexe" class="tab-content hidden">
  <div class="bg-white p-4 rounded shadow mt-2 flex flex-col items-center">
    <h2 class="text-lg font-semibold text-pink-600 mb-2">إحصائيات حسب الجنس</h2>
    <!-- <p class="text-gray-600">عدد الذكور والإناث من الطلبة المتخرجين.</p> -->
    <div id="sexeStats" class="text-gray-700">
        <!-- Male -->
        <?php
            $totalM = $pdo->query("SELECT COUNT(*) FROM etudiants_diplomé WHERE Sexe='M'")->fetchColumn();
            $totalF = $pdo->query("SELECT COUNT(*) FROM etudiants_diplomé WHERE Sexe='F'")->fetchColumn();
            echo "<p>الذكور: $totalM</p>";
            echo "<p>الإناث: $totalF</p>";
        ?>
         
    </div>
  </div>
</div>

<div id="tab-ni" class="tab-content hidden">
    <a href="export_ni.php" class="red" >
                Telecharger fichier excel ni
            </a>
  <div class="bg-white p-4 rounded shadow mt-2" >
    <h2 class="text-lg font-semibold text-blue-600 mb-1">الإحصائيات حسب السنة (NI)</h2>
    <!-- <p class="text-gray-600">يمكنك هنا عرض أعداد الطلبة حسب السنة الدراسية أو رقم NI.</p> -->
        <table class="w-full text-sm text-center border-collapse" style="direction: ltr;">
            <thead class="bg-blue-50 sticky top-0">
            <tr>
                <th class="p-2 border">NI</th>
                <th class="p-2 border ">totale etudiants</th>
                <th class="p-2 border">totale feminins</th>
                <th class="p-2 border">totale masculins</th>
                <th class="p-2 border">totale inscrits</th>
                <th class="p-2 border">totale non-inscrit</th>
            </tr>
        </thead>
        <tbody>
        <?php if (count($ni)): ?>

            <?php foreach ($ni as $y):
                  $M = $pdo->prepare("SELECT COUNT(*) FROM etudiants WHERE NI = ? AND Sexe = 'M'");
                    $M->execute([$y]);
                     $F = $pdo->prepare("SELECT COUNT(*) FROM etudiants WHERE NI = ? AND Sexe = 'F'");
                    $F->execute([$y]);
                    $insc = $pdo->prepare("SELECT COUNT(*) FROM etudiants WHERE NI = ? AND Inscrit = 'OUI'");
                    $insc->execute([$y]);
                    $non_insc = $pdo->prepare("SELECT COUNT(*) FROM etudiants WHERE NI = ? AND Inscrit = 'NON'");
                    $non_insc->execute([$y]);
                    $count_M = $M->fetchColumn();
                    $count_F = $F->fetchColumn();
                    $count_insc = $insc->fetchColumn();
                    $count_non_insc = $non_insc->fetchColumn();
                    $total_MF = $count_M + $count_F

                ?>
                
            <tr>
                <td class="p-1 border"> <?= htmlspecialchars($y)?></td>
                <td class="p-1 border"><?= htmlspecialchars($total_MF)?></td>
                <td class="p-1 border text-pink-600"><?= htmlspecialchars($count_F)?></td>
                <td class="p-1 border text-blue-600"><?= htmlspecialchars($count_M)?></td>
                <td class="p-1 border"><?= htmlspecialchars($count_insc)?></td>
                <td class="p-1 border"><?= htmlspecialchars($count_non_insc)?></td>

            </tr>
            <?php endforeach; ?>
             <?php else: ?>
                    <tr><td colspan="14" class="p-4 text-center text-gray-500">لا توجد بيانات</td></tr>
                <?php endif; ?>
        </tbody>
     </table>
  </div>
</div>


<div id="tab-inscrit" class="tab-content hidden">
    <a href="export_inscrit.php" class="red" >
                Telecharger fichier excel
            </a>
  <div class="bg-white p-4 rounded shadow mt-2">
    <h2 class="text-lg font-semibold text-blue-600 mb-2">المسجلين</h2>
    <table class="w-full text-sm text-center border-collapse" style="direction: ltr;">
        <thead class="bg-blue-50 sticky top-0">
            <tr>
            <th class="p-2 border">Total inscrits</th>
            <th class="p-2 border">Total non inscrits</th>
            <th class="p-2 border">Total non inscrits féminins</th>
            <th class="p-2 border">Total non inscrits masculins</th>
            <th class="p-2 border">Total inscrits féminins</th>
            <th class="p-2 border">Total inscrits masculins</th>
            </tr>
        </thead>
        <tbody>
              
                    <?php 
                    $inscrit = $pdo->query("SELECT COUNT(*) FROM etudiants WHERE Inscrit = 'OUI'")->fetchColumn();
                    $non_inscrit = $pdo->query("SELECT COUNT(*) FROM etudiants WHERE Inscrit = 'NON'")->fetchColumn();
                    $non_inscrit_f = $pdo->query("SELECT COUNT(*) FROM etudiants WHERE Inscrit = 'NON' AND Sexe = 'F'")->fetchColumn();
                    $non_inscrit_m = $pdo->query("SELECT COUNT(*) FROM etudiants WHERE Inscrit = 'NON' AND Sexe = 'M'")->fetchColumn();
                    $inscrit_f = $pdo->query("SELECT COUNT(*) FROM etudiants WHERE Inscrit = 'OUI' AND Sexe = 'F'")->fetchColumn();
                    $inscrit_m = $pdo->query("SELECT COUNT(*) FROM etudiants WHERE Inscrit = 'OUI' AND Sexe = 'M'")->fetchColumn();
                    ?>
                    <tr>
                <?php if ($inscrit + $non_inscrit > 0): ?>
                    <td class="p-1 border"><?= htmlspecialchars($inscrit) ?></td>
                    <td class="p-1 border"><?= htmlspecialchars($non_inscrit) ?></td>
                    <td class="p-1 border text-pink-600"><?= htmlspecialchars($non_inscrit_f) ?></td>
                    <td class="p-1 border text-blue-600"><?= htmlspecialchars($non_inscrit_m) ?></td>
                    <td class="p-1 border text-pink-600"><?= htmlspecialchars($inscrit_f) ?></td>
                    <td class="p-1 border text-blue-600"><?= htmlspecialchars($inscrit_m) ?></td>
                    </tr>
                 <?php else: ?>
                    <tr><td colspan="14" class="p-4 text-center text-gray-500">لا توجد بيانات</td></tr>
                <?php endif; ?>
        </tbody>
    </table>

    <!-- <p class="text-gray-600">عرض الطلبة المسجلين أو تفاصيل أخرى تتعلق بالتسجيل.</p> -->
  </div>
</div>

<div class="max-w-6xl mx-auto">
<!-- <header class="mb-4">
    <a href="index.php" class="hover:text-blue-500">عودة للوحة</a>
</header>
<header class=" flex  items-center">
        <h1 class="text-xl font-semibold ">قائمة الطلبة </h1>
    </header> -->

<!-- <form method="get" class="flex gap-3 mb-4">
    <select name="division" class="border p-2 rounded">
        <option value="">كل الشعب</option>
        <?php foreach($divs as $d): ?>
            <option value="<?= htmlspecialchars($d) ?>" <?= $d===$division? 'selected':'' ?>><?= htmlspecialchars($d) ?></option>
        <?php endforeach; ?>
    </select>
    <select name="annee" class="border p-2 rounded">
        <option value="">كل السنوات</option>
        <?php foreach($annes as $a): ?>
            <option value="<?= htmlspecialchars($a) ?>" <?= $a===$annee? 'selected':'' ?>><?= htmlspecialchars($a) ?></option>
        <?php endforeach; ?>
    </select>
    <button type="submit" class="px-4 py-2 rounded bg-blue-500 text-white hover:bg-blue-400 transition">فلتر</button>
</form> -->

    <div class="border rounded-lg shadow overflow-y-auto " style="max-height: 750px; direction: ltr">
        <a href="list_reset.php" class="red m-2 inline-block">
            vider la liste
        </a>
    <table class="w-full text-sm text-left border-collapse">
            <thead class="bg-blue-50 sticky top-0">
                <tr>
                    <th class="p-2 border">CODAPO</th>
                    <th class="p-2 border">CNE</th>
                    <th class="p-2 border">Nom</th>
                    <th class="p-2 border">Prenom</th>
                    <th class="p-2 border">CNI</th>
                    <th class="p-2 border">Date Nais</th>
                    <th class="p-2 border">Sexe</th>
                    <th class="p-2 border">Lieux de Nais</th>
                    <th class="p-2 border">NI</th>
                    <th class="p-2 border">Inscrit</th>
                    <th class="p-2 border">Diplome</th>
                    <th class="p-2 border">إجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($rows)): ?>
                    <?php foreach ($rows as $r): ?>
                        <tr class="hover:bg-blue-50">
                            <td class="p-2 border"><?= htmlspecialchars($r['CODAPO']) ?></td>
                            <td class="p-2 border"><?= htmlspecialchars($r['cne']) ?></td>
                            <td class="p-2 border"><?= htmlspecialchars($r['Nom']) ?></td>
                            <td class="p-2 border"><?= htmlspecialchars($r['Prenom']) ?></td>
                            <td class="p-2 border"><?= htmlspecialchars($r['CIN']) ?></td>
                            <td class="p-2 border"><?= htmlspecialchars($r['Date_de_nais']) ?></td>
                            <td class="p-2 border"><?= htmlspecialchars($r['Sexe']) ?></td>
                            <td class="p-2 border"><?= htmlspecialchars($r['Lieu_de_nais']) ?></td>
                            <td class="p-2 border"><?= htmlspecialchars($r['NI']) ?></td>
                            <td class="p-2 border"><?= htmlspecialchars($r['Inscrit']) ?></td>
                            <td class="p-2 border"><?= htmlspecialchars($r['Diplome']) ?></td>
                            <td class="p-2 border text-center">
                                <a href="edit_etudiant.php?APO=<?= $r['CODAPO'] ?>" class="px-2 py-1 rounded hover:text-blue-500"> تعديل</a>
                                <a href="delete_etudiant.php?APO=<?= $r['CODAPO'] ?>" class="px-2 py-1 rounded hover:text-red-500" onclick="return confirm('هل أنت متأكد؟')"> حذف</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="14" class="p-4 text-center text-gray-500">لا توجد بيانات</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
        <?php if ($total_pages > 1): ?>
            <div class="flex justify-center items-center gap-2 mt-6 mb-4">
                <?php if ($page > 1): ?>
                    <a href="?page=<?= $page - 1 ?>&division=<?= urlencode($division) ?>&annee=<?= urlencode($annee) ?>"
                    class="px-3 py-1 border rounded hover:bg-blue-100">← Précédent</a>
                <?php endif; ?>

                <?php
                $start = max(1, $page - 3);
                $end = min($total_pages, $page + 3);
                for ($i = $start; $i <= $end; $i++):
                ?>
                    <a href="?page=<?= $i ?>&division=<?= urlencode($division) ?>&annee=<?= urlencode($annee) ?>"
                    class="px-3 py-1 border rounded <?= $i == $page ? 'bg-blue-500 text-white' : 'hover:bg-blue-100' ?>">
                    <?= $i ?>
                    </a>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <a href="?page=<?= $page + 1 ?>&division=<?= urlencode($division) ?>&annee=<?= urlencode($annee) ?>"
                    class="px-3 py-1 border rounded hover:bg-blue-100">Suivant →</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

</div>
<script>
    document.addEventListener('DOMContentLoaded', () => {
  const buttons = document.querySelectorAll('.btns a');
  const tabs = document.querySelectorAll('.tab-content');
  const tableContainer = document.querySelector('.border.rounded-lg.shadow');
//   const Filtre = document.querySelector('form');

  const lastTab = localStorage.getItem('activeTab');

  function showTab(tabName) {
    tabs.forEach(div => div.classList.add('hidden'));
    const activeDiv = document.getElementById(`tab-${tabName}`);
    if (activeDiv) activeDiv.classList.remove('hidden');
  }

  buttons.forEach(btn => {
    btn.addEventListener('click', e => {
      e.preventDefault();
      buttons.forEach(b => b.classList.remove('active-btn'));
      btn.classList.add('active-btn');

      const tabName = btn.textContent.trim().toLowerCase();
      localStorage.setItem('activeTab', tabName);

      showTab(tabName);

      if (tabName.includes("liste")) {
        tableContainer.style.display = "block";
        // Filtre.style.display = "block";
      } else {
        tableContainer.style.display = "none";
        // Filtre.style.display = "none";
      }
    });
  });

  if (lastTab) {
    buttons.forEach(btn => {
      const tabName = btn.textContent.trim().toLowerCase();
      if (tabName === lastTab) {
        btn.classList.add('active-btn');
        showTab(tabName);

        if (tabName.includes("liste")) {
          tableContainer.style.display = "block";
        //   Filtre.style.display = "block";
        } else {
          tableContainer.style.display = "none";
        //   Filtre.style.display = "none";
        }
      }
    });
  } else {
    buttons[0].classList.add('active-btn');
    showTab(buttons[0].textContent.trim().toLowerCase());
    tableContainer.style.display = "block";
    // Filtre.style.display = "block";
  }
});
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('searchInput');
    const table = document.querySelector('.border.rounded-lg.shadow tbody');

    searchInput.addEventListener('keyup', () => {
        const value = searchInput.value.toLowerCase();
        const rows = table.querySelectorAll('tr');

        rows.forEach(row => {
            const apo = row.cells[0]?.textContent.toLowerCase() || '';
            const nom = row.cells[2]?.textContent.toLowerCase() || '';
            const prenom = row.cells[3]?.textContent.toLowerCase() || '';
            const cne = row.cells[1]?.textContent.toLowerCase() || '';

            // Show only rows that match
            if (apo.includes(value) || nom.includes(value) || prenom.includes(value) || cne.includes(value)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
});
// document.addEventListener('DOMContentLoaded', () => {
//   const select = document.getElementById('diplomeSelect');
//   const statsDiv = document.getElementById('diplomeStats');

//   select.addEventListener('change', () => {
//     const diplome = select.value;

//     if(diplome === "") {
//       statsDiv.textContent = "اختر الدبلوم أعلاه ليظهر الإحصائيات.";
//       return;
//     }

//     // نعمل طلب AJAX صغير لـ PHP باش نجيب العدد
//     fetch('get_diplome_count.php?diplome=' + encodeURIComponent(diplome))
//       .then(res => res.json())
//       .then(data => {
//         statsDiv.textContent = diplome + " - " + data.count + " طالب";
//       })
//       .catch(err => {
//         statsDiv.textContent = "حدث خطأ، حاول مرة أخرى.";
//         console.error(err);
//       });
//   });
// });
</script>
<?php include 'layout_bottom.php'; ?>
