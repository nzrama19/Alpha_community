<?php

/**
 * Logique métier pour profile.php
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/User.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    redirect('login.php');
}

$user = new User();
$user_data = $user->getById($_SESSION['user_id']);
$message = '';
$error = '';

if (!$user_data) {
    redirect('login.php');
}

// Traiter la mise à jour du profil
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['update_profile'])) {
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);

        if (empty($username) || empty($email)) {
            $error = 'Le nom d\'utilisateur et l\'email sont obligatoires.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'L\'adresse email n\'est pas valide.';
        } elseif ($user->usernameExists($username, $_SESSION['user_id'])) {
            $error = 'Ce nom d\'utilisateur est déjà utilisé.';
        } elseif ($user->emailExists($email, $_SESSION['user_id'])) {
            $error = 'Cette adresse email est déjà utilisée.';
        } else {
            if ($user->updateProfile($_SESSION['user_id'], $username, $email)) {
                $message = 'Profil mis à jour avec succès !';
                $user_data = $user->getById($_SESSION['user_id']);
                $_SESSION['username'] = $username;
            } else {
                $error = 'Erreur lors de la mise à jour du profil.';
            }
        }
    }

    // Traiter le changement d'avatar
    if (isset($_POST['update_avatar'])) {
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['avatar'];
            $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed_exts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

            if (in_array($file_ext, $allowed_exts)) {
                $new_filename = 'avatar_' . $_SESSION['user_id'] . '_' . time() . '.' . $file_ext;
                $upload_path = UPLOAD_DIR . $new_filename;

                if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                    if ($user->updateAvatar($_SESSION['user_id'], $new_filename)) {
                        $message = 'Avatar mis à jour avec succès !';
                        $user_data = $user->getById($_SESSION['user_id']);
                        $_SESSION['avatar'] = $new_filename;
                    } else {
                        $error = 'Erreur lors de la mise à jour de l\'avatar.';
                    }
                } else {
                    $error = 'Erreur lors de l\'upload du fichier.';
                }
            } else {
                $error = 'Format de fichier non autorisé. Utilisez : JPG, PNG, GIF, WEBP.';
            }
        } else {
            $error = 'Veuillez sélectionner un fichier.';
        }
    }

    // Traiter le changement de mot de passe
    if (isset($_POST['change_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            $error = 'Tous les champs de mot de passe sont obligatoires.';
        } elseif ($new_password !== $confirm_password) {
            $error = 'Les nouveaux mots de passe ne correspondent pas.';
        } elseif (strlen($new_password) < 6) {
            $error = 'Le nouveau mot de passe doit contenir au moins 6 caractères.';
        } else {
            $user_with_password = $user->getByUsername($user_data['username']);

            if ($user_with_password && password_verify($current_password, $user_with_password['password'])) {
                if ($user->changePassword($_SESSION['user_id'], $new_password)) {
                    $message = 'Mot de passe changé avec succès !';
                } else {
                    $error = 'Erreur lors du changement de mot de passe.';
                }
            } else {
                $error = 'Le mot de passe actuel est incorrect.';
            }
        }
    }
}
