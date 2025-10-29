<?php
// db.php 
session_start();
$host = 'localhost';
$db   = 'statistiques';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     http_response_code(500);
     echo 'Erreur de connexion DB: ' . htmlspecialchars($e->getMessage());
     exit;
}

// flash helper
function flash($name='', $message='') {
    if($name && $message) { $_SESSION['flash'][$name] = $message; }
    if($name && empty($message) && isset($_SESSION['flash'][$name])) {
        $m = $_SESSION['flash'][$name];
        unset($_SESSION['flash'][$name]);
        return $m;
    }
    return null;
}
?>