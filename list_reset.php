<?php 
require 'db.php';


$pdo->exec("TRUNCATE TABLE etudiants;");

header("Location: etudiants.php?msg=reset");
exit;
?>