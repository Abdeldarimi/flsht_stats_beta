<?php
require 'db.php';
require 'auth.php';
require_login();
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $username = $_POST['username'] ?? 'admin';
    $password = $_POST['password'] ?? 'admin123';
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('INSERT INTO utilisateurs (nom_utilisateur, mot_de_passe) VALUES (?, ?)');
    try{
        $stmt->execute([$username, $hash]);
        $message = 'تم إنشاء حساب المسؤول بنجاح. يرجى حذف create_admin.php لأسباب أمنية.';
    } catch(Exception $e){
        $message = 'خطأ: ' . $e->getMessage();
    }
}
?>

<!doctype html>
<html lang="ar" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>إنشاء حساب مسؤول</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-white flex items-center justify-center min-h-screen">
<div class="w-full max-w-md p-6 border rounded shadow-sm">
    <h2 class="text-2xl mb-4 text-center font-bold">إنشاء حساب مسؤول</h2>

    <?php if(!empty($message)): ?>
        <div class="p-3 mb-3 rounded bg-green-50 text-green-700"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="post" class="space-y-3">
        <input name="username" placeholder="اسم المستخدم" class="w-full border p-2 rounded" required value="admin">
        <input name="password" type="password" placeholder="كلمة المرور" class="w-full border p-2 rounded" required value="admin123">
        <button type="submit" class="w-full py-2 rounded bg-blue-500 hover:bg-blue-400 text-white transition">إنشاء المسؤول</button>
        <a href="login.php" class="text-blue-500 hover:underline">الذهاب لتسجيل الدخول</a>
    </form>
</div>
</body>
</html>
