<?php
require 'db.php';
require 'auth.php';
require_login();
$stmt = $pdo->query("SELECT * FROM professeurs ORDER BY id DESC");
$rows = $stmt->fetchAll();
include 'header.php';

include 'layout_top.php';

?>
<div class="max-w-6xl mx-auto">
<a href="index.php" class="hover:text-blue-500">عودة للوحة</a>
<h1 class="text-2xl mb-4">قائمة الأساتذة</h1>
<table class="w-full text-right">
    <thead class="bg-blue-50">
        <tr><th class="p-2 border">الاسم</th><th class="p-2 border">اللقب</th><th class="p-2 border">المصلحة</th></tr>
    </thead>
    <tbody>
        <?php foreach($rows as $r): ?>
            <tr class="hover:bg-blue-50">
                <td class="p-2 border"><?= htmlspecialchars($r['prenom']) ?></td>
                <td class="p-2 border"><?= htmlspecialchars($r['nom']) ?></td>
                <td class="p-2 border"><?= htmlspecialchars($r['departement']) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</div>
<?php include 'layout_bottom.php'; ?>
