<?php

/**
 * Logique métier pour edit-post.php
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/Post.php';
require_once __DIR__ . '/../includes/User.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    redirect('login.php');
}

$post_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$post_id) {
    redirect('my-posts.php');
}

$postModel = new Post();
$post = $postModel->getById($post_id);

// Vérifier que le post existe et appartient à l'utilisateur
if (!$post || $post['user_id'] !== $_SESSION['user_id']) {
    redirect('my-posts.php');
}

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_post'])) {
    $content = trim($_POST['content']);
    $media_type = $post['media_type'];
    $media_url = $post['media_url'];
    $keep_existing_media = isset($_POST['keep_existing_media']);

    if (empty($content)) {
        $error = 'Le contenu du post est obligatoire.';
    } else {
        // Gérer les nouveaux médias
        $uploaded_files = [];
        if (isset($_FILES['media']) && !empty($_FILES['media']['name'][0])) {
            $files = $_FILES['media'];
            $file_count = count($files['name']);

            for ($i = 0; $i < $file_count; $i++) {
                if ($files['error'][$i] === UPLOAD_ERR_OK) {
                    $file_tmp = $files['tmp_name'][$i];
                    $file_name = $files['name'][$i];
                    $file_type = mime_content_type($file_tmp);
                    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

                    $is_image = strpos($file_type, 'image') !== false && in_array($file_ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                    $is_video = strpos($file_type, 'video') !== false && in_array($file_ext, ['mp4', 'webm', 'ogg']);

                    if (!$is_image && !$is_video) {
                        $error = 'Type de fichier non autorisé. Utilisez : JPG, PNG, GIF, WEBP (images) ou MP4, WEBM, OGG (vidéos).';
                        break;
                    }

                    $new_filename = 'post_' . $_SESSION['user_id'] . '_' . time() . '_' . $i . '.' . $file_ext;
                    $upload_path = UPLOAD_DIR . $new_filename;

                    if (move_uploaded_file($file_tmp, $upload_path)) {
                        $uploaded_files[] = [
                            'filename' => $new_filename,
                            'type' => $is_image ? 'image' : 'video'
                        ];
                    } else {
                        $error = 'Erreur lors de l\'upload du fichier.';
                        break;
                    }
                }
            }

            // Si des nouveaux fichiers ont été uploadés
            if (!empty($uploaded_files) && empty($error)) {
                // Si on garde les médias existants et qu'ils existent
                if ($keep_existing_media && !empty($media_url) && $media_type === 'multiple') {
                    $existing_media = json_decode($media_url, true);
                    if (is_array($existing_media)) {
                        $uploaded_files = array_merge($existing_media, $uploaded_files);
                    }
                }

                $media_url = json_encode($uploaded_files);
                $media_type = 'multiple';
            }
        } elseif (!$keep_existing_media) {
            // Supprimer les médias si demandé
            $media_type = 'none';
            $media_url = null;
        }

        if (empty($error)) {
            if ($postModel->update($post_id, $content, $_SESSION['user_id'], $media_type, $media_url)) {
                $message = 'Post mis à jour avec succès !';
                $post = $postModel->getById($post_id);
            } else {
                $error = 'Erreur lors de la mise à jour du post.';
            }
        }
    }
}
