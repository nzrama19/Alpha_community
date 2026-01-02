<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/Comment.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

$post_id = isset($_GET['post_id']) ? intval($_GET['post_id']) : 0;

if ($post_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Données invalides']);
    exit;
}

$comment = new Comment();
$comments = $comment->getByPostId($post_id);

echo json_encode([
    'success' => true,
    'comments' => $comments,
    'total' => count($comments)
]);
