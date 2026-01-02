<?php

/**
 * Logique métier pour create-post.php
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/Post.php';

// Vérifier si l'utilisateur est connecté
if (!isUserLoggedIn()) {
    redirect('login.php');
}

$postModel = new Post();
$message = '';
$error = '';

// Traiter la création du post
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = trim($_POST['content']);
    $media_type = 'none';
    $media_url = null;

    if (empty($content)) {
        $error = 'Veuillez écrire un message pour votre post.';
    } else {
        // Traiter les médias si fournis (plusieurs fichiers)
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

                    // Vérifier le type de fichier
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

            // Si des fichiers ont été uploadés avec succès
            if (!empty($uploaded_files) && empty($error)) {
                // Stocker les informations des médias en JSON
                $media_url = json_encode($uploaded_files);
                $media_type = 'multiple';
            }
        }

        // Créer le post si pas d'erreur
        if (empty($error)) {
            try {
                if ($postModel->create($content, $media_type, $media_url, null, $_SESSION['user_id'])) {
                    $message = 'Votre post a été soumis avec succès ! Il sera visible après validation par un modérateur.';
                    $content = '';
                    // Rediriger vers mes posts après 3 secondes
                    header('Refresh: 3; url=my-posts.php');
                } else {
                    $error = 'Erreur lors de la création du post.';
                }
            } catch (Exception $e) {
                $error = 'Erreur lors de la création du post: ' . $e->getMessage();
            }
        }
    }
}
