<?php
require_once __DIR__ . '/../config/config.php';

// Déconnecter l'admin uniquement
unset($_SESSION['admin_id']);
unset($_SESSION['username']);
unset($_SESSION['photo']);
unset($_SESSION['user_type']);
session_destroy();
redirect('admin/login.php');
