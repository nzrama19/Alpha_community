<?php
require_once __DIR__ . '/php/categories_logic.php';
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catégories - ALPHA COMMUNITY</title>

    <!-- Manifest et Icônes -->
    <link rel="manifest" href="manifest.json">
    <link rel="icon" type="image/x-icon" href="public/favicon_io/favicon.ico">
    <link rel="icon" type="image/png" sizes="32x32" href="public/favicon_io/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="public/favicon_io/favicon-16x16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="public/favicon_io/apple-touch-icon.png">
    <meta name="theme-color" content="#c87533">

    <link rel="stylesheet" href="public/css/navbar.css">
    <link rel="stylesheet" href="public/css/categories.css">
    <link rel="stylesheet" href="public/css/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>
    <?php include_once 'nav.php'; ?>

    <div class="container">
        <div class="page-header">
            <h1><i class="fas fa-th-large"></i> Catégories</h1>
            <p>Explorez nos différentes catégories de contenu</p>
        </div>

        <div class="categories-grid">
            <?php foreach ($categories as $key => $category): ?>
                <a href="?cat=<?php echo $key; ?>" class="category-card <?php echo $selectedCategory === $key ? 'active' : ''; ?>">
                    <div class="category-icon" style="background-color: <?php echo $category['color']; ?>">
                        <i class="fas <?php echo $category['icon']; ?>"></i>
                    </div>
                    <h3><?php echo $category['name']; ?></h3>
                    <p class="category-desc"><?php echo $category['description']; ?></p>
                    <span class="category-count"><?php echo $categoryCounts[$key]; ?> publications</span>
                </a>
            <?php endforeach; ?>
        </div>

        <?php if ($selectedCategory && isset($categories[$selectedCategory])): ?>
            <div class="category-posts">
                <h2>
                    <i class="fas <?php echo $categories[$selectedCategory]['icon']; ?>"></i>
                    <?php echo $categories[$selectedCategory]['name']; ?>
                    <span class="category-subtitle">(<?php echo count($posts); ?> publications)</span>
                </h2>
                <div class="posts-feed">
                    <?php if (empty($posts)): ?>
                        <div class="no-posts">
                            <i class="fas fa-inbox"></i>
                            <p>Aucun article dans cette catégorie pour le moment.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach (array_slice($posts, 0, 10) as $post): ?>
                            <?php
                            $comments = $commentModel->getByPostId($post['id']);
                            $hasLiked = isUserLoggedIn() ? $postModel->hasUserLiked($post['id'], $_SESSION['user_id']) : false;
                            ?>
                            <div class="post-card" data-post-id="<?php echo $post['id']; ?>">
                                <div class="post-header">
                                    <img src="<?php
                                                if (!empty($post['avatar'])) {
                                                    echo UPLOAD_URL . escape($post['avatar']);
                                                } else {
                                                    echo 'https://ui-avatars.com/api/?name=' . urlencode($post['username']) . '&background=d4a574&color=fff';
                                                }
                                                ?>" alt="Avatar" class="avatar">
                                    <div class="post-info">
                                        <strong><?php echo escape($post['username']); ?></strong>
                                        <span class="admin-badge">
                                            <i class="fas <?php echo ($post['author_type'] === 'admin') ? 'fa-crown' : 'fa-user'; ?>"></i>
                                            <?php echo ($post['author_type'] === 'admin') ? 'Admin' : 'Utilisateur'; ?>
                                        </span>
                                        <span class="post-date">
                                            <i class="far fa-clock"></i>
                                            <?php echo date('d/m/Y à H:i', strtotime($post['created_at'])); ?>
                                        </span>
                                    </div>
                                </div>

                                <div class="post-content">
                                    <?php
                                    $content = $post['content'];
                                    $lines = explode("\n", $content);
                                    $firstLine = isset($lines[0]) ? trim($lines[0]) : '';
                                    $hasMoreContent = strlen($content) > 200 || count($lines) > 1;
                                    ?>
                                    <p class="post-text <?php echo $hasMoreContent ? 'expandable' : ''; ?>" data-post-id="<?php echo $post['id']; ?>">
                                        <span class="post-preview"><?php echo nl2br(escape($firstLine)); ?></span>
                                        <?php if ($hasMoreContent): ?>
                                            <span class="post-full-content" style="display:none;"><?php echo nl2br(escape($content)); ?></span>
                                        <?php endif; ?>
                                    </p>
                                    <?php if ($hasMoreContent): ?>
                                        <button class="btn-expand-text" data-post-id="<?php echo $post['id']; ?>">
                                            <i class="fas fa-chevron-down"></i> Voir plus
                                        </button>
                                    <?php endif; ?>

                                    <?php if ($post['media_type'] === 'image'): ?>
                                        <div class="media-container" onclick="openMediaModal('<?php echo UPLOAD_URL . $post['media_url']; ?>', 'image')">
                                            <img src="<?php echo UPLOAD_URL . $post['media_url']; ?>" alt="Image" class="post-media">
                                            <div class="media-overlay">
                                                <i class="fas fa-search-plus"></i>
                                            </div>
                                        </div>
                                    <?php elseif ($post['media_type'] === 'video'): ?>
                                        <div class="media-container video-container">
                                            <video controls class="post-media">
                                                <source src="<?php echo UPLOAD_URL . $post['media_url']; ?>">
                                                Votre navigateur ne supporte pas la lecture de vidéos.
                                            </video>
                                            <div class="video-indicator">
                                                <i class="fas fa-play-circle"></i>
                                            </div>
                                        </div>
                                    <?php elseif ($post['media_type'] === 'multiple'): ?>
                                        <?php
                                        $media_files = json_decode($post['media_url'], true);
                                        if (is_array($media_files) && !empty($media_files)):
                                        ?>
                                            <div class="post-media-gallery">
                                                <?php foreach ($media_files as $media): ?>
                                                    <?php if ($media['type'] === 'image'): ?>
                                                        <div class="gallery-item" onclick="openMediaModal('<?php echo UPLOAD_URL . $media['filename']; ?>', 'image')">
                                                            <img src="<?php echo UPLOAD_URL . $media['filename']; ?>" alt="Image" class="post-media-item">
                                                            <div class="gallery-overlay">
                                                                <i class="fas fa-search-plus"></i>
                                                            </div>
                                                        </div>
                                                    <?php elseif ($media['type'] === 'video'): ?>
                                                        <div class="gallery-item video-item">
                                                            <video controls class="post-media-item">
                                                                <source src="<?php echo UPLOAD_URL . $media['filename']; ?>">
                                                                Votre navigateur ne supporte pas la lecture de vidéos.
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
                                </div>

                                <div class="post-actions">
                                    <button class="btn-like <?php echo $hasLiked ? 'liked' : ''; ?>"
                                        data-post-id="<?php echo $post['id']; ?>"
                                        <?php echo !isUserLoggedIn() ? 'disabled title="Connectez-vous pour aimer"' : ''; ?>>
                                        <i class="<?php echo $hasLiked ? 'fas' : 'far'; ?> fa-heart"></i>
                                        <span class="likes-count"><?php echo $post['likes_count']; ?></span>
                                    </button>

                                    <button class="btn-comment btn-toggle-comments" data-post-id="<?php echo $post['id']; ?>">
                                        <i class="far fa-comment"></i>
                                        <span class="comments-count"><?php echo $post['comments_count']; ?></span>
                                    </button>
                                </div>

                                <div class="comments-section comments-hidden" id="comments-<?php echo $post['id']; ?>"
                                    data-post-id="<?php echo $post['id']; ?>">
                                    <div class="comments-list">
                                        <?php foreach ($comments as $comment): ?>
                                            <div class="comment" data-comment-id="<?php echo $comment['id']; ?>">
                                                <img src="<?php echo !empty($comment['avatar']) ? UPLOAD_URL . escape($comment['avatar']) : 'https://ui-avatars.com/api/?name=' . urlencode($comment['username']) . '&background=d4a574&color=fff'; ?>" alt="Avatar" class="avatar-small">
                                                <div class="comment-content">
                                                    <div class="comment-header">
                                                        <strong><?php echo escape($comment['username']); ?></strong>
                                                        <span class="comment-date"><?php echo date('d/m/Y H:i', strtotime($comment['created_at'])); ?></span>
                                                    </div>
                                                    <p><?php echo nl2br(escape($comment['content'])); ?></p>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>

                                    <?php if (isUserLoggedIn()): ?>
                                        <form class="comment-form" data-post-id="<?php echo $post['id']; ?>">
                                            <img src="<?php
                                                        if (!empty($_SESSION['avatar'])) {
                                                            echo UPLOAD_URL . escape($_SESSION['avatar']);
                                                        } else {
                                                            echo 'https://ui-avatars.com/api/?name=' . urlencode($_SESSION['username']) . '&background=d4a574&color=fff';
                                                        }
                                                        ?>" alt="Avatar" class="avatar-small">
                                            <input type="text"
                                                name="comment"
                                                placeholder="Écrivez un commentaire..."
                                                required>
                                            <button type="submit">
                                                <i class="fas fa-paper-plane"></i>
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <div class="login-prompt">
                                            <i class="fas fa-lock"></i>
                                            <a href="register.php">Créez un compte</a> ou
                                            <a href="login.php">connectez-vous</a> pour commenter
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php include_once 'footer.php'; ?>

    <!-- Modal pour afficher les images en grand -->
    <div id="mediaModal" class="media-modal" onclick="closeMediaModal()">
        <span class="modal-close">&times;</span>
        <img id="modalImage" class="modal-content-media" style="display:none;">
        <video id="modalVideo" class="modal-content-media" controls style="display:none;">
            <source id="modalVideoSource" src="">
        </video>
    </div>

    <script>
        window.BASE_URL = '<?php echo rtrim(BASE_URL, "/"); ?>';
    </script>
    <script src="public/js/main.js"></script>
    <script src="public/js/posts-media.js"></script>
    <?php include_once 'footer.php'; ?>
</body>

</html>
