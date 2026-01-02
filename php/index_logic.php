<?php

/**
 * Logique métier pour index.php
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/Post.php';
require_once __DIR__ . '/../includes/Comment.php';
require_once __DIR__ . '/../includes/Like.php';

$postModel = new Post();
$commentModel = new Comment();
$likeModel = new Like();

// Récupérer toutes les publications
$posts = $postModel->getAll();
