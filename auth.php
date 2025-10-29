<?php
require_once 'db.php';
function require_login(){
    if(empty($_SESSION['admin_logged'])){
        header('Location: login.php'); exit;
    }
}
function current_user(){
    return $_SESSION['admin_user'] ?? null;
}
?>