<?php

/**
 * Logique métier pour my-posts.php
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/User.php';
require_once __DIR__ . '/../includes/Post.php';
require_once __DIR__ . '/../includes/Comment.php';
require_once __DIR__ . '/../includes/Like.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    redirect('login.php');
}

$postModel = new Post();
$commentModel = new Comment();
$likeModel = new Like();
$user = new User();

$user_data = $user->getById($_SESSION['user_id']);
if (!$user_data) {
    redirect('login.php');
}

// Récupérer les posts de l'utilisateur
$user_posts = $postModel->getUserPosts($_SESSION['user_id']);
$message = '';
$error = '';

// Traiter la suppression de post
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_post'])) {
    $post_id = (int)$_POST['post_id'];
    if ($postModel->delete($post_id, $_SESSION['user_id'])) {
        $message = 'Post supprimé avec succès !';
        $user_posts = $postModel->getUserPosts($_SESSION['user_id']);
    } else {
        $error = 'Erreur lors de la suppression du post.';
    }
}
