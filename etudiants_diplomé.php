
<?php
require 'db.php';
require 'auth.php';
require_login();

// فلترة المدخلات
$division = $_GET['division'] ?? '';
$annee = $_GET['annee'] ?? '';

// الاستعلام الأساسي
$q = "SELECT * FROM etudiants_diplomé WHERE 1";
$params = [];

if ($division) {
    $q .= " AND Filiere = ?";
    $params[] = $division;
}
if ($annee) {
    $q .= " AND NI = ?";
    $params[] = $annee;
}

$stmt = $pdo->prepare($q);
$stmt->execute($params);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// القيم الفريدة للفلاتر
$divs = $pdo->query("SELECT DISTINCT Filiere FROM etudiants_diplomé")->fetchAll(PDO::FETCH_COLUMN);
$annes = $pdo->query("SELECT DISTINCT NI FROM etudiants_diplomé")->fetchAll(PDO::FETCH_COLUMN);



$total_diplomé = $pdo->query("SELECT COUNT(*) FROM etudiants_diplomé ")->fetchColumn();

include 'header.php';
include 'layout_top.php';
?>
<!-- Loading Screen -->
<!-- <div id="loading-screen" class="fixed inset-0 bg-white z-50 flex flex-col items-center justify-center">
    <div class="animate-spin rounded-full h-20 w-20 border-t-4 border-blue-500 border-b-4 border-gray-200 mb-4"></div>
    <span class="text-gray-600 font-semibold text-lg">جارٍ تحميل الموقع…</span>
</div> -->

<div class="max-w-100 mx-auto">
    <header class=" flex  items-center">
        <h1 class="text-xl font-semibold ">قائمة الطلبة المتخرجين</h1>
    </header>
    <div class="bg-lighblue shadow rounded p-1 flex flex-col items-center">
        <div class="text-gray-500 mb-1">اجمالي الطلاب المتخرجين</div>
        <div class="text-3xl font-bold text-blue-600"><?php echo $total_diplomé; ?></div>
    </div>

    <!-- فورم الفلترة -->
    <form method="get" class="flex flex-wrap gap-1 ">
        <select name="division" class="border p-1 rounded text-sm">
            <option value="">كل الشعب</option>
            <?php foreach ($divs as $d): ?>
                <option value="<?= htmlspecialchars($d) ?>" <?= $d === $division ? 'selected' : '' ?>>
                    <?= htmlspecialchars($d) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="annee" class="border p-2 rounded">
            <option value="">كل السنوات</option>
            <?php foreach ($annes as $a): ?>
                <option value="<?= htmlspecialchars($a) ?>" <?= $a === $annee ? 'selected' : '' ?>>
                    <?= htmlspecialchars($a) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit" class="px-4 py-2 rounded bg-blue-500 text-white hover:bg-blue-400 transition">
            فلتر
        </button>
    </form>

    <!-- جدول مع Scroll داخلي -->
    <div class="border rounded-lg shadow overflow-y-auto " style="max-height: 650px; direction: ltr">
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
                    <th class="p-2 border">Filiere</th>
                    <th class="p-2 border">Diplome</th>
                    <th class="p-2 border">Pays</th>
                    <th class="p-2 border">HAND</th>
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
                            <td class="p-2 border"><?= htmlspecialchars($r['DATE_NAIS']) ?></td>
                            <td class="p-2 border"><?= htmlspecialchars($r['Sexe']) ?></td>
                            <td class="p-2 border"><?= htmlspecialchars($r['Lieux_de_nais']) ?></td>
                            <td class="p-2 border"><?= htmlspecialchars($r['NI']) ?></td>
                            <td class="p-2 border"><?= htmlspecialchars($r['Filiere']) ?></td>
                            <td class="p-2 border"><?= htmlspecialchars($r['Diplome']) ?></td>
                            <td class="p-2 border"><?= htmlspecialchars($r['Pays']) ?></td>
                            <td class="p-2 border"><?= htmlspecialchars($r['Hand']) ?></td>
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
    </div>
</div>


<script src="script.js"></script>

<?php include 'layout_bottom.php'; ?>
