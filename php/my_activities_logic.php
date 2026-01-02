<?php

/**
 * Logique métier pour my-activities.php
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/User.php';
require_once __DIR__ . '/../includes/Comment.php';
require_once __DIR__ . '/../includes/Like.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    redirect('login.php');
}

$user = new User();
$comment = new Comment();
$like = new Like();

$user_data = $user->getById($_SESSION['user_id']);
if (!$user_data) {
    redirect('login.php');
}

$user_comments = $user->getUserComments($_SESSION['user_id']);
$user_likes = $user->getUserLikes($_SESSION['user_id']);
$message = '';
$error = '';

// Traiter la suppression de commentaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_comment'])) {
    $comment_id = (int)$_POST['comment_id'];
    if ($comment->delete($comment_id, $_SESSION['user_id'])) {
        $message = 'Commentaire supprimé avec succès !';
        $user_comments = $user->getUserComments($_SESSION['user_id']);
    } else {
        $error = 'Erreur lors de la suppression du commentaire.';
    }
}

// Traiter la suppression de like
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_like'])) {
    $post_id = (int)$_POST['post_id'];
    if ($like->remove($post_id, $_SESSION['user_id'])) {
        $message = 'Like supprimé avec succès !';
        $user_likes = $user->getUserLikes($_SESSION['user_id']);
    } else {
        $error = 'Erreur lors de la suppression du like.';
    }
}
