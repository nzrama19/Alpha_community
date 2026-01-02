<?php

/**
 * Logique métier pour categories.php
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/Post.php';
require_once __DIR__ . '/../includes/Comment.php';
require_once __DIR__ . '/../includes/Like.php';

$postModel = new Post();
$commentModel = new Comment();
$likeModel = new Like();

// Définir les catégories par type de média
$categories = [
    'video' => [
        'name' => 'Vidéos',
        'icon' => 'fa-video',
        'color' => '#ef4444',
        'description' => 'Publications contenant des vidéos'
    ],
    'image' => [
        'name' => 'Images',
        'icon' => 'fa-image',
        'color' => '#10b981',
        'description' => 'Publications contenant des images'
    ],
    'text' => [
        'name' => 'Texte',
        'icon' => 'fa-align-left',
        'color' => '#3b82f6',
        'description' => 'Publications texte uniquement'
    ]
];

$selectedCategory = isset($_GET['cat']) ? $_GET['cat'] : null;

// Récupérer les posts selon la catégorie sélectionnée
if ($selectedCategory && isset($categories[$selectedCategory])) {
    $posts = $postModel->getByMediaType($selectedCategory);
} else {
    $posts = [];
}

// Compter les posts par catégorie
$categoryCounts = [
    'video' => $postModel->countByMediaType('video'),
    'image' => $postModel->countByMediaType('image'),
    'text' => $postModel->countByMediaType('text')
];
