<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/Like.php';
require_once __DIR__ . '/../includes/Post.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

$post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;

if ($post_id <= 0) {
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

$like = new Like();

try {
    // Seulement les utilisateurs connectés peuvent liker en BD
    if (isUserLoggedIn()) {
        $user_id = $_SESSION['user_id'];

        if ($like->toggle($post_id, $user_id)) {
            $liked = $like->exists($post_id, $user_id);
            $count = $like->countByPostId($post_id);

            echo json_encode([
                'success' => true,
                'liked' => $liked,
                'count' => $count,
                'message' => $liked ? 'Publication aimée' : 'Like retiré',
                'is_logged' => true
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'opération']);
        }
    } else {
        // Pour les anonymes: juste afficher le compteur actuel (gestion locale en JS)
        $count = $like->countByPostId($post_id);

        echo json_encode([
            'success' => true,
            'liked' => false,
            'count' => $count,
            'message' => 'Like enregistré localement',
            'is_logged' => false,
            'require_login' => false  // On accepte les likes anonymes
        ]);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
}
