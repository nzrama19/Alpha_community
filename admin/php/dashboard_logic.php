<?php

/**
 * Logique métier pour admin/dashboard.php
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/Post.php';

// Vérifier si l'admin est connecté
if (!isAdminLoggedIn()) {
    redirect('admin/login.php');
}

$post = new Post();
$message = '';

// Traiter la création d'une publication
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_post'])) {
    $content = trim($_POST['content']);
    $media_type = 'none';
    $media_url = null;

    if (!empty($content)) {
        // Gérer l'upload de média
        if (isset($_FILES['media']) && $_FILES['media']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['media'];
            $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

            // Vérifier le type de fichier
            $image_exts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $video_exts = ['mp4', 'webm', 'ogg', 'mov'];

            if (in_array($file_ext, $image_exts)) {
                $media_type = 'image';
            } elseif (in_array($file_ext, $video_exts)) {
                $media_type = 'video';
            }

            if ($media_type !== 'none') {
                $new_filename = uniqid() . '_' . time() . '.' . $file_ext;
                $upload_path = UPLOAD_DIR . $new_filename;

                if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                    $media_url = $new_filename;
                }
            }
        }

        try {
            if ($post->create($content, $media_type, $media_url, $_SESSION['admin_id'], null)) {
                $message = '<div class="alert success">Publication créée avec succès !</div>';
            } else {
                $message = '<div class="alert error">Erreur lors de la création de la publication.</div>';
            }
        } catch (Exception $e) {
            $message = '<div class="alert error">Erreur: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    } else {
        $message = '<div class="alert error">Le contenu ne peut pas être vide.</div>';
    }
}

// Récupérer uniquement les publications de l'admin connecté
$posts = $post->getAdminPosts($_SESSION['admin_id']);
