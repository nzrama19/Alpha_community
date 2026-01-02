<?php

/**
 * Logique métier pour admin/manage-users.php
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/User.php';

// Vérifier si l'admin est connecté
if (!isAdminLoggedIn()) {
    redirect('admin/login.php');
}

$user = new User();
$message = '';
$users = [];
$total_users = 0;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

// Traiter la suppression d'un utilisateur
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user'])) {
    $user_id = (int)$_POST['user_id'];

    // Ne pas permettre de supprimer soi-même
    if ($user_id != $_SESSION['admin_id']) {
        if ($user->delete($user_id)) {
            $message = '<div class="alert success"><i class="fas fa-check-circle"></i> Utilisateur supprimé avec succès !</div>';
        } else {
            $message = '<div class="alert error"><i class="fas fa-exclamation-circle"></i> Erreur lors de la suppression.</div>';
        }
    } else {
        $message = '<div class="alert error"><i class="fas fa-exclamation-circle"></i> Vous ne pouvez pas vous supprimer vous-même !</div>';
    }
}

// Récupérer les utilisateurs avec statistiques
$users = $user->getAllWithStats($per_page, $offset);
$total_users = $user->getTotalCount();
$total_pages = ceil($total_users / $per_page);
