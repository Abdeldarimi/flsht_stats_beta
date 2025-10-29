<?php
require 'db.php';
require 'auth.php';
require_login();
$division = $_GET['division'] ?? '';
$annee = $_GET['annee'] ?? '';
$q = "SELECT * FROM etudiants WHERE 1";
$params = [];
if($division){ $q .= ' AND division = ?'; $params[] = $division; }
if($annee){ $q .= ' AND annee = ?'; $params[] = $annee; }
$stmt = $pdo->prepare($q);
$stmt->execute($params);
$rows = $stmt->fetchAll();
// get distinct divisions and annees for filters
$divs = $pdo->query("SELECT DISTINCT division FROM etudiants")->fetchAll(PDO::FETCH_COLUMN);
$annes = $pdo->query("SELECT DISTINCT annee FROM etudiants")->fetchAll(PDO::FETCH_COLUMN);
include 'header.php';

include 'layout_top.php';

?>
<div class="max-w-6xl mx-auto">
<header class="mb-4">
    <a href="index.php" class="hover:text-blue-500">عودة للوحة</a>
</header>
<h1 class="text-2xl mb-4">قائمة الطلبة</h1>

<form method="get" class="flex gap-3 mb-4">
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
</form>

<table class="w-full text-right border-collapse rounded overflow-hidden">
    <thead class="bg-blue-50">
        <tr>
            <th class="p-2 border">الاسم</th>
            <th class="p-2 border">اللقب</th>
            <th class="p-2 border">الشعبة</th>
            <th class="p-2 border">السنة</th>
            <th class="p-2 border">إجراءات</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($rows as $r): ?>
            <tr class="hover:bg-blue-50">
                <td class="p-2 border"><?= htmlspecialchars($r['prenom']) ?></td>
                <td class="p-2 border"><?= htmlspecialchars($r['nom']) ?></td>
                <td class="p-2 border"><?= htmlspecialchars($r['division']) ?></td>
                <td class="p-2 border"><?= htmlspecialchars($r['annee']) ?></td>
                <td class="p-2 border">
                    <a href="edit_etudiant.php?id=<?= $r['id'] ?>" class="px-2 py-1 rounded hover:text-blue-500">تعديل</a>
                    <a href="delete_etudiant.php?id=<?= $r['id'] ?>" class="px-2 py-1 rounded hover:text-blue-500" onclick="return confirm('هل انت متأكد؟')">حذف</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</div>
<?php include 'layout_bottom.php'; ?>
