<?php

/**
 * Logique métier pour admin/register.php
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/Admin.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validation
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'Veuillez remplir tous les champs';
    } elseif (strlen($username) < 3) {
        $error = 'Le nom d\'utilisateur doit contenir au moins 3 caractères';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Veuillez entrer une adresse email valide';
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
        $adminModel = new Admin();

        // Vérifier si le username existe déjà
        if ($adminModel->getByUsername($username)) {
            $error = 'Ce nom d\'utilisateur est déjà utilisé';
        } else {
            // Créer le nouvel admin
            if ($adminModel->create($username, $email, $password)) {
                $success = 'Admin créé avec succès ! Vous pouvez maintenant <a href="login.php">vous connecter</a>';
            } else {
                $error = 'Erreur lors de la création de l\'admin. Veuillez réessayer.';
            }
        }
    }
}
