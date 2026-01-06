<?php
require_once __DIR__ . '/php/user_details_logic.php';
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de <?php echo htmlspecialchars($user_data['username']); ?> - Admin</title>

    <!-- Favicon Admin -->
    <link rel="apple-touch-icon" sizes="180x180" href="public/favicon_io/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="public/favicon_io/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="public/favicon_io/favicon-16x16.png">
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#00D4FF">

    <link rel="stylesheet" href="public/css/user-details.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

</head>

<body class="modern-layout admin-page">
    <!-- Navigation moderne -->
    <?php include 'nav.php'; ?>
    <div class="admin-container">
        <a href="manage-users.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Retour à la gestion des utilisateurs
        </a>

        <!-- En-tête utilisateur -->
        <div class="user-header">
            <?php if (!empty($user_data['avatar'])): ?>
                <img src="<?php echo UPLOAD_URL . htmlspecialchars($user_data['avatar']); ?>" alt="<?php echo htmlspecialchars($user_data['username']); ?>" class="user-avatar-large">
            <?php else: ?>
                <div class="user-avatar-large">
                    <?php echo strtoupper(substr($user_data['username'], 0, 1)); ?>
                </div>
            <?php endif; ?>

            <div class="user-info-main">
                <h1><?php echo htmlspecialchars($user_data['username']); ?></h1>
                <p><i class="fas fa-envelope"></i> <strong>Email:</strong> <?php echo htmlspecialchars($user_data['email']); ?></p>
                <p><i class="fas fa-calendar"></i> <strong>Inscrit le:</strong> <?php echo date('d/m/Y à H:i', strtotime($user_data['created_at'])); ?></p>
                <p><i class="fas fa-hashtag"></i> <strong>ID:</strong> <?php echo $user_data['id']; ?></p>

                <div class="stats-row">
                    <div class="stat-item">
                        <div class="stat-number"><?php echo count($user_posts); ?></div>
                        <div class="stat-label">Publications</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number"><?php echo $user_stats['comment_count']; ?></div>
                        <div class="stat-label">Commentaires</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number"><?php echo $user_stats['like_count']; ?></div>
                        <div class="stat-label">Likes</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section Publications -->
        <div class="section">
            <div class="section-header">
                <i class="fas fa-newspaper"></i>
                <h2>Publications (<?php echo count($user_posts); ?>)</h2>
            </div>

            <?php if (empty($user_posts)): ?>
                <div class="no-data">
                    <i class="fas fa-file-alt"></i>
                    <p>Cet utilisateur n'a pas encore publié</p>
                </div>
            <?php else: ?>
                <?php foreach ($user_posts as $user_post): ?>
                    <div class="post-item">
                        <div class="item-header">
                            <strong>Publication #<?php echo $user_post['id']; ?></strong>
                            <span class="item-date">
                                <i class="fas fa-clock"></i>
                                <?php echo date('d/m/Y H:i', strtotime($user_post['created_at'])); ?>
                            </span>
                        </div>
                        <?php
                        $post_content = htmlspecialchars($user_post['content']);
                        $is_long_post = mb_strlen($post_content) > 200;
                        ?>
                        <div class="item-content <?php echo $is_long_post ? 'truncated' : ''; ?>">
                            <div class="content-text">
                                <?php echo nl2br($is_long_post ? mb_substr($post_content, 0, 200) . '...' : $post_content); ?>
                            </div>
                            <?php if ($is_long_post): ?>
                                <div class="full-content" style="display: none;"><?php echo nl2br($post_content); ?></div>
                                <button class="btn-see-more" onclick="toggleContent(this)">
                                    <i class="fas fa-chevron-down"></i> Voir plus
                                </button>
                            <?php endif; ?>
                        </div>
                        <?php if ($user_post['media_type'] === 'image'): ?>
                            <div class="post-media-preview">
                                <img src="<?php echo UPLOAD_URL . htmlspecialchars($user_post['media_url']); ?>" alt="Image" class="preview-image">
                            </div>
                        <?php elseif ($user_post['media_type'] === 'video'): ?>
                            <div class="post-media-preview">
                                <video controls class="preview-video">
                                    <source src="<?php echo UPLOAD_URL . htmlspecialchars($user_post['media_url']); ?>">
                                </video>
                            </div>
                        <?php elseif ($user_post['media_type'] === 'multiple'): ?>
                            <?php $media_files = json_decode($user_post['media_url'], true); ?>
                            <?php if (is_array($media_files) && !empty($media_files)): ?>
                                <div class="post-media-gallery-preview">
                                    <?php foreach ($media_files as $media): ?>
                                        <?php if ($media['type'] === 'image'): ?>
                                            <img src="<?php echo UPLOAD_URL . htmlspecialchars($media['filename']); ?>" alt="Image" class="preview-gallery-item">
                                        <?php elseif ($media['type'] === 'video'): ?>
                                            <video controls class="preview-gallery-item">
                                                <source src="<?php echo UPLOAD_URL . htmlspecialchars($media['filename']); ?>">
                                            </video>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                        <div class="post-stats">
                            <span class="stat"><i class="fas fa-heart"></i> <?php echo $user_post['likes_count']; ?> likes</span>
                            <span class="stat"><i class="fas fa-comment"></i> <?php echo $user_post['comments_count']; ?> commentaires</span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Section Commentaires -->
        <div class="section">
            <div class="section-header">
                <i class="fas fa-comments"></i>
                <h2>Commentaires (<?php echo count($comments); ?>)</h2>
            </div>

            <?php if (empty($comments)): ?>
                <div class="no-data">
                    <i class="fas fa-comment-slash"></i>
                    <p>Cet utilisateur n'a pas encore commenté</p>
                </div>
            <?php else: ?>
                <?php foreach ($comments as $comment): ?>
                    <div class="comment-item">
                        <div class="item-header">
                            <strong>Commentaire #<?php echo $comment['id']; ?></strong>
                            <span class="item-date">
                                <i class="fas fa-clock"></i>
                                <?php echo date('d/m/Y H:i', strtotime($comment['created_at'])); ?>
                            </span>
                        </div>
                        <?php
                        $comment_content = htmlspecialchars($comment['content']);
                        $is_long_comment = mb_strlen($comment_content) > 150;
                        ?>
                        <div class="item-content <?php echo $is_long_comment ? 'truncated' : ''; ?>">
                            <div class="content-text">
                                <?php echo $is_long_comment ? mb_substr($comment_content, 0, 150) . '...' : $comment_content; ?>
                            </div>
                            <?php if ($is_long_comment): ?>
                                <div class="full-content" style="display: none;"><?php echo $comment_content; ?></div>
                                <button class="btn-see-more" onclick="toggleContent(this)">
                                    <i class="fas fa-chevron-down"></i> Voir plus
                                </button>
                            <?php endif; ?>
                        </div>
                        <?php
                        $post_ref = htmlspecialchars($comment['post_content']);
                        $is_long_ref = mb_strlen($post_ref) > 100;
                        ?>
                        <div class="item-post-ref <?php echo $is_long_ref ? 'truncated' : ''; ?>">
                            <strong>Publication:</strong>
                            <span class="content-text"><?php echo $is_long_ref ? mb_substr($post_ref, 0, 100) . '...' : $post_ref; ?></span>
                            <?php if ($is_long_ref): ?>
                                <span class="full-content" style="display: none;"><?php echo $post_ref; ?></span>
                                <button class="btn-see-more-small" onclick="toggleContent(this)">
                                    <i class="fas fa-chevron-down"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Section Likes -->
        <div class="section">
            <div class="section-header">
                <i class="fas fa-heart"></i>
                <h2>Likes (<?php echo count($likes); ?>)</h2>
            </div>

            <?php if (empty($likes)): ?>
                <div class="no-data">
                    <i class="fas fa-heart-broken"></i>
                    <p>Cet utilisateur n'a pas encore aimé de publication</p>
                </div>
            <?php else: ?>
                <?php foreach ($likes as $like): ?>
                    <div class="like-item">
                        <div class="item-header">
                            <strong>Like #<?php echo $like['id']; ?></strong>
                            <span class="item-date">
                                <i class="fas fa-clock"></i>
                                <?php echo date('d/m/Y H:i', strtotime($like['created_at'])); ?>
                            </span>
                        </div>
                        <?php
                        $like_ref = htmlspecialchars($like['post_content']);
                        $is_long_like = mb_strlen($like_ref) > 100;
                        ?>
                        <div class="item-post-ref <?php echo $is_long_like ? 'truncated' : ''; ?>">
                            <strong>Publication de <?php echo htmlspecialchars($like['author']); ?>:</strong>
                            <span class="content-text"><?php echo $is_long_like ? mb_substr($like_ref, 0, 100) . '...' : $like_ref; ?></span>
                            <?php if ($is_long_like): ?>
                                <span class="full-content" style="display: none;"><?php echo $like_ref; ?></span>
                                <button class="btn-see-more-small" onclick="toggleContent(this)">
                                    <i class="fas fa-chevron-down"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Boutons d'actions -->
        <div class="action-buttons">
            <a href="manage-users.php" class="btn-primary">
                <i class="fas fa-arrow-left"></i> Retour à la liste
            </a>
        </div>
    </div>

    <script src="public/js/admin-user-details.js"></script>
</body>

</html>
