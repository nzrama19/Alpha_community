<?php
require_once __DIR__ . '/php/my_posts_logic.php';
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Posts - ALPHA Community</title>

    <!-- Manifest et Icônes -->
    <link rel="manifest" href="manifest.json">
    <link rel="icon" type="image/x-icon" href="public/favicon_io/favicon.ico">
    <link rel="icon" type="image/png" sizes="32x32" href="public/favicon_io/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="public/favicon_io/favicon-16x16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="public/favicon_io/apple-touch-icon.png">
    <meta name="theme-color" content="#c87533">

    <link rel="stylesheet" href="public/css/my-posts.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

</head>

<body class="modern-layout">
    <!-- Navigation -->
    <?php include_once 'nav.php'; ?>

    <!-- Mes Posts Section -->
    <div class="my-posts-container">
        <div class="posts-header">
            <h1>Mes Posts</h1>
            <p>Gérez et consultez tous vos posts publiés</p>
            <span class="posts-count"><?php echo count($user_posts); ?> post<?php echo count($user_posts) > 1 ? 's' : ''; ?></span>
            <br>
            <a href="create-post.php" class="btn-create-post">
                <i class="fas fa-plus"></i> Créer un nouveau post
            </a>
        </div>

        <!-- Messages -->
        <?php if ($message): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <!-- Posts List -->
        <?php if (empty($user_posts)): ?>
            <div class="empty-posts">
                <i class="fas fa-file-alt"></i>
                <h3>Aucun post pour le moment</h3>
                <p>Vous n'avez pas encore créé de post. Commencez par en publier un !</p>
                <a href="create-post.php" class="btn-create-post">
                    <i class="fas fa-plus"></i> Créer mon premier post
                </a>
            </div>
        <?php else: ?>
            <div class="posts-list">
                <?php foreach ($user_posts as $post): ?>
                    <?php
                    $comments = $commentModel->getByPostId($post['id']);
                    $hasLiked = $postModel->hasUserLiked($post['id'], $_SESSION['user_id']);
                    ?>
                    <div class="post-item <?php echo 'status-' . ($post['status'] ?? 'pending'); ?>">
                        <div class="post-header-item">
                            <div class="post-meta">
                                <img src="<?php
                                            if (!empty($post['avatar'])) {
                                                echo UPLOAD_URL . escape($post['avatar']);
                                            } else {
                                                echo 'https://ui-avatars.com/api/?name=' . urlencode($post['username']) . '&background=d4a574&color=fff';
                                            }
                                            ?>" alt="Avatar">
                                <div class="post-user-info">
                                    <strong><?php echo escape($post['username']); ?></strong>
                                    <span class="post-date">
                                        <i class="far fa-clock"></i>
                                        <?php echo date('d/m/Y à H:i', strtotime($post['created_at'])); ?>
                                    </span>
                                    <?php
                                    $status = $post['status'] ?? 'pending';
                                    $statusLabels = [
                                        'pending' => ['label' => 'En attente de validation', 'icon' => 'fa-clock', 'class' => 'status-pending'],
                                        'approved' => ['label' => 'Publié', 'icon' => 'fa-check-circle', 'class' => 'status-approved'],
                                        'rejected' => ['label' => 'Refusé', 'icon' => 'fa-times-circle', 'class' => 'status-rejected']
                                    ];
                                    $statusInfo = $statusLabels[$status] ?? $statusLabels['pending'];
                                    ?>
                                    <span class="post-status-badge <?php echo $statusInfo['class']; ?>">
                                        <i class="fas <?php echo $statusInfo['icon']; ?>"></i>
                                        <?php echo $statusInfo['label']; ?>
                                    </span>
                                </div>
                            </div>
                            <div class="post-actions-item">
                                <a href="edit-post.php?id=<?php echo $post['id']; ?>" class="btn-edit" title="Éditer">
                                    <i class="fas fa-edit"></i> Éditer
                                </a>
                                <button class="btn-delete" onclick="openDeleteModal(<?php echo $post['id']; ?>)" title="Supprimer">
                                    <i class="fas fa-trash"></i> Supprimer
                                </button>
                            </div>
                        </div>

                        <?php
                        $content = escape($post['content']);
                        $content_length = mb_strlen($content);
                        $is_long = $content_length > 300;
                        ?>
                        <div class="post-content-item <?php echo $is_long ? 'truncated' : ''; ?>" data-full-text="<?php echo htmlspecialchars($content); ?>">
                            <div class="content-text">
                                <?php
                                if ($is_long) {
                                    echo nl2br(mb_substr($content, 0, 300)) . '...';
                                } else {
                                    echo nl2br($content);
                                }
                                ?>
                            </div>
                            <?php if ($is_long): ?>
                                <button class="btn-show-more" onclick="toggleContent(this)">
                                    <i class="fas fa-chevron-down"></i> Voir plus
                                </button>
                            <?php endif; ?>
                        </div>

                        <?php if ($post['media_type'] === 'image'): ?>
                            <img src="<?php echo UPLOAD_URL . $post['media_url']; ?>" alt="Image" class="post-media" onclick="openMediaModal('<?php echo UPLOAD_URL . $post['media_url']; ?>', 'image')">
                        <?php elseif ($post['media_type'] === 'video'): ?>
                            <video controls class="post-media">
                                <source src="<?php echo UPLOAD_URL . $post['media_url']; ?>">
                                Votre navigateur ne supporte pas la lecture de vidéos.
                            </video>
                        <?php elseif ($post['media_type'] === 'multiple'): ?>
                            <?php
                            $media_files = json_decode($post['media_url'], true);
                            if (is_array($media_files) && !empty($media_files)):
                            ?>
                                <div class="media-gallery">
                                    <?php foreach ($media_files as $media): ?>
                                        <?php if ($media['type'] === 'image'): ?>
                                            <div class="gallery-item" onclick="openMediaModal('<?php echo UPLOAD_URL . $media['filename']; ?>', 'image')">
                                                <img src="<?php echo UPLOAD_URL . $media['filename']; ?>" alt="Image">
                                                <div class="gallery-overlay">
                                                    <i class="fas fa-search-plus"></i>
                                                </div>
                                            </div>
                                        <?php elseif ($media['type'] === 'video'): ?>
                                            <div class="gallery-item video-item">
                                                <video controls>
                                                    <source src="<?php echo UPLOAD_URL . $media['filename']; ?>">
                                                </video>
                                                <div class="video-indicator">
                                                    <i class="fas fa-play-circle"></i>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>

                        <div class="post-stats">
                            <div class="stat-item">
                                <i class="fas fa-heart"></i>
                                <span><?php echo $post['likes_count']; ?> like<?php echo $post['likes_count'] > 1 ? 's' : ''; ?></span>
                            </div>
                            <div class="stat-item">
                                <i class="fas fa-comment"></i>
                                <span><?php echo $post['comments_count']; ?> commentaire<?php echo $post['comments_count'] > 1 ? 's' : ''; ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Modal de confirmation de suppression -->
    <div class="modal-delete" id="deleteModal">
        <div class="modal-content-delete">
            <h3>Supprimer ce post ?</h3>
            <p>Cette action est irréversible. Tous les commentaires et likes associés seront également supprimés.</p>
            <div class="modal-buttons">
                <button class="btn-modal btn-modal-cancel" onclick="closeDeleteModal()">Annuler</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    <input type="hidden" name="post_id" id="postIdToDelete" value="">
                    <input type="hidden" name="delete_post" value="1">
                    <button type="submit" class="btn-modal btn-modal-confirm">Supprimer</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal pour afficher les images en grand -->
    <div class="media-modal" id="mediaModal" onclick="closeMediaModal()">
        <span class="modal-close" onclick="closeMediaModal()">×</span>
        <div class="modal-media-content">
            <img id="modalMediaContent" src="" alt="Image en grand">
        </div>
    </div>

    <!-- Footer -->
    <?php include_once 'footer.php'; ?>

    <script src="public/js/my-posts.js"></script>
</body>

</html>
