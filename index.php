<?php
require_once 'db.php';

$total_etudiants = $pdo->query("SELECT ( (SELECT COUNT(*) FROM etudiants) + (SELECT COUNT(*) FROM etudiants_diplomé)) AS total")->fetchColumn();
$reussi_etudiants = $pdo->query("SELECT COUNT(*) FROM etudiants WHERE reussi=1")->fetchColumn();
$failed_etudiants = $total_etudiants - $reussi_etudiants;

$pass_percentage = ($total_etudiants > 0) ? ($reussi_etudiants / $total_etudiants) * 100 : 0;
$fail_percentage = 100 - $pass_percentage;

include 'header.php';
include 'layout_top.php';
?>

<div class=" grid grid-cols-1 md:grid-cols-3 gap-6"">
    <div class="bg-white shadow rounded p-6 flex flex-col items-center">
        <div class="text-gray-500 mb-2">إجمالي الطلاب</div>
        <div class="text-3xl font-bold text-blue-600"><?php echo $total_etudiants; ?></div>
    </div>

    <div class="bg-white shadow rounded p-6 flex flex-col items-center">
        <div class="text-gray-500 mb-2">نسبة النجاح</div>
        <div class="relative w-24 h-24">
            <svg class="w-full h-full transform -rotate-90">
                <circle cx="50%" cy="50%" r="40%" stroke="#e5e7eb" stroke-width="8" fill="none"/>
                <circle cx="50%" cy="50%" r="40%" stroke="#3b82f6" stroke-width="8" fill="none" stroke-dasharray="251.2" stroke-dashoffset="<?php echo 251.2 * (1 - $pass_percentage / 100); ?>" stroke-linecap="round"/>
            </svg>
            <div class="absolute inset-0 flex items-center justify-center text-blue-600 font-bold">
                <?php echo round($pass_percentage); ?>%
            </div>
        </div>
    </div>

    <div class="bg-white shadow rounded p-6 flex flex-col items-center">
        <div class="text-gray-500 mb-2">نسبة الفشل</div>
        <div class="relative w-24 h-24">
            <svg class="w-full h-full transform -rotate-90">
                <circle cx="50%" cy="50%" r="40%" stroke="#e5e7eb" stroke-width="8" fill="none"/>
                <circle cx="50%" cy="50%" r="40%" stroke="#f87171" stroke-width="8" fill="none" stroke-dasharray="251.2" stroke-dashoffset="<?php echo 251.2 * (1 - $fail_percentage / 100); ?>" stroke-linecap="round"/>
            </svg>
            <div class="absolute inset-0 flex items-center justify-center text-red-500 font-bold">
                <?php echo round($fail_percentage); ?>%
            </div>
        </div>
    </div>
</div>

<?php include 'layout_bottom.php'; ?>
