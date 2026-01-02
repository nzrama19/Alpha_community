<?php

/**
 * Logique métier pour admin/login.php
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/Admin.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (!empty($username) && !empty($password)) {
        $adminModel = new Admin();
        $admin = $adminModel->authenticate($username, $password);

        if ($admin) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['username'] = $admin['username'];
            $_SESSION['photo'] = $admin['photo'];
            $_SESSION['user_type'] = 'admin';

            redirect('admin/dashboard.php');
        } else {
            $error = 'Identifiants administrateur incorrects';
        }
    } else {
        $error = 'Veuillez remplir tous les champs';
    }
}
