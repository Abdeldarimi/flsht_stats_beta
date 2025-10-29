<?php
require 'db.php';
require 'auth.php';
require_login();

// jointure notes avec étudiants
$q = "SELECT n.id, n.matiere, n.note,  e.prenom, e.nom, e.division 
      FROM notes n 
      LEFT JOIN etudiants e ON e.id = n.etudiant_id";
$stmt = $pdo->query($q);
$rows = $stmt->fetchAll();

include 'layout_top.php';
?>

<div class="max-w-6xl mx-auto">
    <a href="index.php" class="hover:text-blue-500 mb-4 inline-block">عودة للوحة</a>
    <h1 class="text-2xl mb-4">قائمة النقاط</h1>

    <table class="w-full text-right border-collapse">
        <thead class="bg-blue-50">
            <tr>
                <th class="p-2 border">الاسم</th>
                <th class="p-2 border">اللقب</th>
                <th class="p-2 border">الشعبة</th>
                <th class="p-2 border">المادة</th>
                <th class="p-2 border">النقطة</th>
                <th class="p-2 border">السنة</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($rows as $r): ?>
                <tr class="hover:bg-blue-50">
                    <td class="p-2 border"><?= htmlspecialchars($r['prenom']) ?></td>
                    <td class="p-2 border"><?= htmlspecialchars($r['nom']) ?></td>
                    <td class="p-2 border"><?= htmlspecialchars($r['division']) ?></td>
                    <td class="p-2 border"><?= htmlspecialchars($r['matiere']) ?></td>
                    <td class="p-2 border"><?= htmlspecialchars($r['note']) ?></td>
                    <td class="p-2 border"><?= htmlspecialchars($r['annee']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include 'layout_bottom.php'; ?>
