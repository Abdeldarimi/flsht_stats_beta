<?php 
require 'db.php';


$pdo->exec("TRUNCATE TABLE etudiants_diplomé;");

header("Location: etudiants_diplomé.php?msg=reset");
exit;
?>