<?php
require 'db.php';
if(isset($_POST['login'])){
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // recherche de l'utilisateur
    $stmt = $pdo->prepare('SELECT * FROM utilisateurs WHERE nom_utilisateur = ? LIMIT 1');
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if($user && password_verify($password, $user['mot_de_passe'])){
        $_SESSION['admin_logged'] = true;
        $_SESSION['admin_user'] = [
            'id' => $user['id'],
            'username' => $user['nom_utilisateur']
        ];
        header('Location: index.php'); 
        exit;
    } else {
        $error = 'بيانات الدخول غير صحيحة';
    }
}
?>

<!doctype html>
<html lang="ar" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>تسجيل الدخول</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-white flex items-center justify-center min-h-screen">
<div class="w-full max-w-md p-6 border rounded shadow-sm">
    <h2 class="text-2xl mb-4 text-center font-bold">تسجيل الدخول</h2>
    <?php if(!empty($error)): ?>
        <div class="p-3 mb-3 rounded bg-red-50 text-red-700"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post" class="space-y-3">
        <input name="username" placeholder="اسم المستخدم" class="w-full border p-2 rounded" required>
        <input name="password" type="password" placeholder="كلمة السر" class="w-full border p-2 rounded" required>
        <button name="login" class="w-full py-2 rounded bg-blue-500 hover:bg-blue-400 text-white transition">دخول</button>
    </form>
    <p class="text-sm mt-3 text-gray-600">استعمل create_admin.php لإنشاء حساب Admin</p>
</div>
</body>
</html>
