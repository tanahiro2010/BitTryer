<?php
require_once '../functions/Accounts.php';

$Accounts = new Accounts('../database/database.json', 'bitcoin');

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $redirect_url = $_GET['redirect_url'] ?? '/';
    $Accounts->logout();

    header('Location: ' . $redirect_url);
} else {
    header("HTTP/1.0 405 Method Not Allowed");
}