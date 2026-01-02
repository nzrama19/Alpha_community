<?php

/**
 * Logique métier pour register.php
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/User.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validation
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'Tous les champs sont obligatoires';
    } elseif (strlen($username) < 3) {
        $error = 'Le nom d\'utilisateur doit contenir au moins 3 caractères';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Email invalide';
    } elseif (strlen($password) < 8) {
        $error = 'Le mot de passe doit contenir au moins 8 caractères';
    } elseif (!preg_match('/[A-Z]/', $password)) {
        $error = 'Le mot de passe doit contenir au moins une majuscule';
    } elseif (!preg_match('/[a-z]/', $password)) {
        $error = 'Le mot de passe doit contenir au moins une minuscule';
    } elseif (!preg_match('/[0-9]/', $password)) {
        $error = 'Le mot de passe doit contenir au moins un chiffre';
    } elseif (!preg_match('/[^A-Za-z0-9]/', $password)) {
        $error = 'Le mot de passe doit contenir au moins un caractère spécial (!@#$%^&*...)';
    } elseif ($password !== $confirm_password) {
        $error = 'Les mots de passe ne correspondent pas';
    } else {
        $userModel = new User();

        // Vérifier si l'username ou l'email existe déjà
        if ($userModel->getByUsername($username)) {
            $error = 'Ce nom d\'utilisateur est déjà utilisé';
        } elseif ($userModel->getByEmail($email)) {
            $error = 'Cet email est déjà utilisé';
        } else {
            // Créer le compte
            $userId = $userModel->create($username, $email, $password);
            if ($userId) {
                // Connecter automatiquement l'utilisateur
                $user = $userModel->getById($userId);

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['avatar'] = $user['avatar'];
                $_SESSION['user_type'] = 'user';

                // Créer un token "Remember Me" pour garder l'utilisateur connecté
                createRememberToken($userId, 30); // 30 jours

                $success = 'Compte créé avec succès ! Redirection...';
                // Rediriger vers la page d'accueil après 1 seconde
                header("refresh:1;url=index.php");
            } else {
                $error = 'Erreur lors de la création du compte';
            }
        }
    }
}
