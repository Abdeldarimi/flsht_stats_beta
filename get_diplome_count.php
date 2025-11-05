<?php
require 'db.php';

$diplome = $_GET['diplome'] ?? '';

if(!$diplome) {
    echo json_encode(['count' => 0]);
    exit;
}

$stmt = $pdo->prepare("SELECT COUNT(*) FROM etudiants WHERE Diplome = ?");
$stmt->execute([$diplome]);
$count = $stmt->fetchColumn();

echo json_encode(['count' => $count]);
