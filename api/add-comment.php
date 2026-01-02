<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/Comment.php';
require_once __DIR__ . '/../includes/Post.php';

header('Content-Type: application/json');

if (!isUserLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Non autorisé - Utilisateur uniquement']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

$post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
$content = isset($_POST['content']) ? trim($_POST['content']) : '';

if ($post_id <= 0 || empty($content)) {
    echo json_encode(['success' => false, 'message' => 'Données invalides']);
    exit;
}

// Vérifier que le post existe
$post = new Post();
$post_exists = $post->getById($post_id);

if (!$post_exists) {
    echo json_encode(['success' => false, 'message' => 'Le post n\'existe pas']);
    exit;
}

$comment = new Comment();

try {
    if ($comment->create($post_id, $_SESSION['user_id'], $content)) {
        // Récupérer le commentaire créé
        $comments = $comment->getByPostId($post_id);
        $lastComment = end($comments);

        echo json_encode([
            'success' => true,
            'message' => 'Commentaire ajouté',
            'comment' => $lastComment,
            'total' => count($comments)
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'ajout du commentaire']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'ajout du commentaire: ' . $e->getMessage()]);
}
