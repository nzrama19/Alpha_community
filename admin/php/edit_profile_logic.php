<?php

/**
 * Logique métier pour admin/edit-profile.php
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/Admin.php';

// Vérifier si l'admin est connecté
if (!isAdminLoggedIn()) {
    redirect('admin/login.php');
}

$admin = new Admin();
$current_admin = $admin->getById($_SESSION['admin_id']);
$message = '';
$error = '';

// Traiter la mise à jour du profil
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['update_profile'])) {
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);

        // Validation
        if (empty($username) || empty($email)) {
            $error = 'Le nom d\'utilisateur et l\'email sont obligatoires.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'L\'adresse email n\'est pas valide.';
        } elseif ($admin->usernameExists($username, $_SESSION['admin_id'])) {
            $error = 'Ce nom d\'utilisateur est déjà utilisé.';
        } elseif ($admin->emailExists($email, $_SESSION['admin_id'])) {
            $error = 'Cette adresse email est déjà utilisée.';
        } else {
            $data = [
                'username' => $username,
                'email' => $email
            ];

            // Gérer l'upload de photo
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['photo'];
                $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $allowed_exts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                $max_file_size = 5 * 1024 * 1024; // 5MB

                if (!in_array($file_ext, $allowed_exts)) {
                    $error = 'Format de fichier non autorisé. Extensions acceptées: jpg, jpeg, png, gif, webp';
                } elseif ($file['size'] > $max_file_size) {
                    $error = 'La taille du fichier ne doit pas dépasser 5MB.';
                } else {
                    $new_filename = 'photo_admin_' . $_SESSION['admin_id'] . '_' . time() . '.' . $file_ext;
                    $upload_path = UPLOAD_DIR . $new_filename;

                    if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                        // Supprimer l'ancienne photo si elle existe
                        if ($current_admin['photo'] !== 'default-photo.png') {
                            $old_photo_path = UPLOAD_DIR . $current_admin['photo'];
                            if (file_exists($old_photo_path)) {
                                unlink($old_photo_path);
                            }
                        }
                        $data['photo'] = $new_filename;
                    } else {
                        $error = 'Erreur lors du téléchargement de la photo.';
                    }
                }
            }

            if ($admin->updateProfile($_SESSION['admin_id'], $data)) {
                $message = 'Profil mis à jour avec succès !';
                $current_admin = $admin->getById($_SESSION['admin_id']);
            } else {
                $error = 'Erreur lors de la mise à jour du profil.';
            }
        }
    }

    // Traiter le changement de mot de passe
    if (isset($_POST['change_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        // Validation
        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            $error = 'Tous les champs de mot de passe sont obligatoires.';
        } elseif ($new_password !== $confirm_password) {
            $error = 'Les nouveaux mots de passe ne correspondent pas.';
        } elseif (strlen($new_password) < 6) {
            $error = 'Le nouveau mot de passe doit contenir au moins 6 caractères.';
        } else {
            // Vérifier le mot de passe actuel
            $admin_data = $admin->getByUsername($current_admin['username']);

            if (password_verify($current_password, $admin_data['password'])) {
                if ($admin->changePassword($_SESSION['admin_id'], $new_password)) {
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
