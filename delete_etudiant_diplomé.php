<?php
require 'db.php';
require 'auth.php';
require_login();

if (isset($_GET['APO'])) {
    $id = intval($_GET['APO']);

    $stmt = $pdo->prepare("DELETE FROM etudiants_diplomé WHERE CODAPO = ?");
    if ($stmt->execute([$id])) {
        header("Location: etudiantS_diplomé.php?msg=deleted");
        exit;
    } else {
        echo "Erreur: " . implode(", ", $stmt->errorInfo());
    }
} else {
    echo "ID manquant";
}
?>
