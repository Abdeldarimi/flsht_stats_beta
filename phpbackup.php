// etudiants_diplomé.php
<?php
$division = $_GET['division'] ?? '';
$annee = $_GET['annee'] ?? '';
$sexe = $_GET['sexe'] ?? '';

$q = "SELECT * FROM etudiants_diplomé WHERE 1";
$params = [];

if ($division) {
    $q .= " AND Filiere = ?";
    $params[] = $division;
}
if ($annee) {
    $q .= " AND NI = ?";
    $params[] = $annee;
}
if($sexe){
    $q .= " AND Sexe = ?";
    $params[] = $sexe;
}
$stmt = $pdo->prepare($q);
$stmt->execute($params);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$divs = $pdo->query("SELECT DISTINCT Filiere FROM etudiants_diplomé")->fetchAll(PDO::FETCH_COLUMN);
$annes = $pdo->query("SELECT DISTINCT NI FROM etudiants_diplomé ORDER BY NI DESC")->fetchAll(PDO::FETCH_COLUMN);
$sexe = $pdo->query("SELECT DISTINCT Sexe FROM etudiants_diplomé")->fetchAll(PDO::FETCH_COLUMN);

$ni = $pdo->query("SELECT DISTINCT NI FROM  etudiants ORDER BY NI DESC")->fetchAll(PDO::FETCH_COLUMN);
foreach ($ni as $y) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM etudiants WHERE NI = ? AND Sexe = 'M'");
    $stmt->execute([$y]);
    $count_M = $stmt->fetchColumn();


}
$total_diplomé = $pdo->query("SELECT COUNT(*) FROM etudiants_diplomé ")->fetchColumn();
?>