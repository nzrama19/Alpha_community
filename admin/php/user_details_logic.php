<?php

/**
 * Logique métier pour admin/user-details.php
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/User.php';
require_once __DIR__ . '/../../includes/Post.php';

// Vérifier si l'admin est connecté
if (!isAdminLoggedIn()) {
    redirect('admin/login.php');
}

$user_class = new User();
$post_class = new Post();
$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($user_id <= 0) {
    redirect('manage-users.php');
}

$user_data = $user_class->getById($user_id);
if (!$user_data) {
    redirect('manage-users.php');
}

$user_stats = $user_class->getUserStats($user_id);
$comments = $user_class->getUserComments($user_id);
$likes = $user_class->getUserLikes($user_id);
$user_posts = $post_class->getUserPosts($user_id);
