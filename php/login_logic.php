<?php

/**
 * Logique métier pour login.php
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/User.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $remember = isset($_POST['remember']) && $_POST['remember'] === 'on';

    if (!empty($username) && !empty($password)) {
        $userModel = new User();
        $user = $userModel->authenticate($username, $password);

        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['avatar'] = $user['avatar'];
            $_SESSION['user_type'] = 'user';

            // Si "Se souvenir de moi" est coché, créer un token persistant
            if ($remember) {
                createRememberToken($user['id'], 30); // 30 jours
            }

            redirect('index.php');
        } else {
            $error = 'Identifiants incorrects';
        }
    } else {
        $error = 'Veuillez remplir tous les champs';
    }
}
