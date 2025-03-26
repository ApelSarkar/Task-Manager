<?php
if (!file_exists(__DIR__ . '/../config.php')) {
    die("Config file not found!");
}
session_start();
require_once '../models/User.php';

$user = new User();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if ($user->login($_POST['email'], $_POST['password'])) {
        header("Location: ../views/dashboard.php");
    } else {
        echo "Invalid credentials";
    }
}
