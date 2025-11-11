<?php


$total_students = $pdo->query("SELECT COUNT(*) FROM etudiants")->fetchColumn();
$total_masculins = $pdo->query("SELECT COUNT(*) FROM etudiants WHERE UPPER(TRIM(Sexe)) = 'M'")->fetchColumn();
$total_feminins = $pdo->query("SELECT COUNT(*) FROM etudiants WHERE UPPER(TRIM(Sexe)) = 'F'")->fetchColumn();

$percent_m = ($total_students > 0) ? ($total_masculins / $total_students) * 100 : 0;
$percent_f = ($total_students > 0) ? ($total_feminins / $total_students) * 100 : 0;
?>

<div class="bg-white shadow rounded p-6 flex flex-col items-center">
  <div class="text-gray-600 mb-3 font-semibold">Répartition M / F</div>
  <div class="relative w-32 h-32">
    <svg class="w-full h-full transform -rotate-90">

    <circle cx="50%" cy="50%" r="40%" stroke="#0055ffff" stroke-width="20" fill="none"/>


    <circle cx="50%" cy="50%" r="40%" stroke="#1100ffff" stroke-width="0" fill="none"
        stroke-dasharray="251.2"
        stroke-dashoffset="<?php echo 251.2 * (1 - $percent_m / 100); ?>"
        stroke-linecap="round"/>

      <circle cx="50%" cy="50%" r="40%" stroke="#ec4899" stroke-width="20" fill="none"
        stroke-dasharray="<?php echo 251.2 * ($percent_f / 100); ?>"
        stroke-dashoffset="0"
        stroke-linecap="round"/>
    </svg>

    <div class="absolute inset-0 flex flex-col items-center justify-center">
      <span class="text-blue-500 font-bold"><?php echo round($percent_m); ?>% M</span>
      <span class="text-pink-500 font-bold"><?php echo round($percent_f); ?>% F</span>
    </div>
  </div>
</div>
