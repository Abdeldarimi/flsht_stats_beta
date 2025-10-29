<?php require_once 'auth.php'; require_login(); ?>
<!doctype html><html lang="ar" dir="rtl"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="/assets/css/style.css">
</head><body class="bg-white min-h-screen">
<div class="flex">
    <!-- Sidebar -->
    <aside id="sidebar" class="w-64 bg-white border-r h-screen p-4 hidden md:block">
        <div class="mb-6">
            <h2 class="text-xl font-bold">نظام الإحصائيات</h2>
            <div class="text-sm text-gray-500">مرحبا، <?= htmlspecialchars(current_user()['username'] ?? '') ?></div>
        </div>
        <nav class="space-y-2">
            <a href="index.php" class="block p-2 rounded hover:bg-blue-50">لوحة</a>
            <a href="etudiants_diplomé.php" class="block p-2 rounded hover:bg-blue-50">قائمة الطلبة المتخرجين</a>
            
            <a href="etudiants.php" class="block p-2 rounded hover:bg-blue-50">قائمة الطلبة</a>
            <a href="professeurs.php" class="block p-2 rounded hover:bg-blue-50">قائمة الأساتذة</a>
            <a href="notes.php" class="block p-2 rounded hover:bg-blue-50">النقط</a>
            <a href="upload.php" class="block p-2 rounded hover:bg-blue-50">رفع Excel</a>
            <a href="create_admin.php" class="block p-2 rounded hover:bg-blue-50">إنشاء Admin</a>
            <a href="logout.php" class="block p-2 rounded hover:bg-blue-50 text-red-600">خروج</a>
        </nav>
    </aside>
    <div class="flex-1 p-6">
        <!-- Top navbar for mobile -->
        <div class="md:hidden mb-4 flex items-center justify-between">
            <button id="toggleSidebar" class="p-2 border rounded">القائمة</button>
            <div class="font-bold">نظام الإحصائيات</div>
        </div>
