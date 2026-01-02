<?php

/**
 * Logique métier pour admin/moderation.php
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/Post.php';

// Vérifier si l'admin est connecté
if (!isAdminLoggedIn()) {
    redirect('admin/login.php');
}

$post = new Post();
$message = '';

// Traiter les actions de modération
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && isset($_POST['post_id'])) {
        $post_id = (int)$_POST['post_id'];
        $action = $_POST['action'];

        switch ($action) {
            case 'approve':
                if ($post->approve($post_id)) {
                    $message = '<div class="alert success"><i class="fas fa-check-circle"></i> Publication approuvée avec succès !</div>';
                } else {
                    $message = '<div class="alert error"><i class="fas fa-times-circle"></i> Erreur lors de l\'approbation.</div>';
                }
                break;

            case 'reject':
                if ($post->reject($post_id)) {
                    $message = '<div class="alert success"><i class="fas fa-check-circle"></i> Publication rejetée.</div>';
                } else {
                    $message = '<div class="alert error"><i class="fas fa-times-circle"></i> Erreur lors du rejet.</div>';
                }
                break;

            case 'delete':
                if ($post->delete($post_id)) {
                    $message = '<div class="alert success"><i class="fas fa-check-circle"></i> Publication supprimée.</div>';
                } else {
                    $message = '<div class="alert error"><i class="fas fa-times-circle"></i> Erreur lors de la suppression.</div>';
                }
                break;
        }
    }
}

// Filtrer par statut
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'pending';
$validFilters = ['pending', 'approved', 'rejected', 'all'];
if (!in_array($filter, $validFilters)) {
    $filter = 'pending';
}

// Récupérer les publications selon le filtre
if ($filter === 'all') {
    $posts = $post->getAllForAdmin();
} else {
    $posts = $post->getByStatus($filter);
}

// Compter les publications par statut
$pendingCount = $post->countByStatus('pending');
$approvedCount = $post->countByStatus('approved');
$rejectedCount = $post->countByStatus('rejected');
