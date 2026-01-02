<?php
require_once __DIR__ . '/config/config.php';

// Supprimer tous les tokens "Remember Me" de l'utilisateur
if (isset($_SESSION['user_id'])) {
    deleteAllUserTokens($_SESSION['user_id']);
}

// Supprimer le cookie
if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, '/');
}

session_destroy();
redirect('index.php');
