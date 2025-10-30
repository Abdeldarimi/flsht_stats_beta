<?php
require 'db.php';
require 'auth.php';
require_login();
include 'header.php';
include 'layout_top.php';
?>

<div class="max-w-2xl mx-auto bg-white p-6 rounded shadow">
  <h1 class="text-2xl mb-4 font-semibold">📥 استيراد ملف Excel</h1>

  <form id="uploadForm" enctype="multipart/form-data">
    <label class="block mb-2 font-medium">نوع الاستيراد:</label>
    <select name="mode" id="mode" class="border p-2 rounded mb-3 w-full">
      <option value="etudiants">طلبة</option>
      <option value="etudiant_diplome">طلبة دبلوم</option>
      <option value="notes">نقاط</option>
    </select>

    <input type="file" name="excel" accept=".xlsx,.xls" required class="block mb-3">
    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">بدء الاستيراد</button>
  </form>

  <!-- Progress -->
  

<?php include 'layout_bottom.php'; ?>
