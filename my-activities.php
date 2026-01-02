<?php
require_once __DIR__ . '/php/my_activities_logic.php';
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Activités - <?php echo htmlspecialchars($user_data['username']); ?></title>

    <!-- Manifest et Icônes -->
    <link rel="manifest" href="manifest.json">
    <link rel="icon" type="image/x-icon" href="public/favicon_io/favicon.ico">
    <link rel="icon" type="image/png" sizes="32x32" href="public/favicon_io/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="public/favicon_io/favicon-16x16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="public/favicon_io/apple-touch-icon.png">
    <meta name="theme-color" content="#c87533">

    <link rel="stylesheet" href="public/css/navbar.css">
    <link rel="stylesheet" href="public/css/activities.css">
    <link rel="stylesheet" href="public/css/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>

    <?php include_once 'nav.php'; ?>

    <body>
        <div class="container">
            <a href="profile.php" class="back-link">
                <i class="fas fa-arrow-left"></i> Retour au profil
            </a>

            <div class="page-header">
                <h1><i class="fas fa-history"></i> Mes Activités</h1>
            </div>

            <?php if ($message): ?>
                <div class="alert success">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <div class="stats-row">
                <div class="stat-card">
                    <div style="font-size: 2.5rem; margin-bottom: 15px; color: var(--primary-color);">
                        <i class="fas fa-comments"></i>
                    </div>
                    <div class="stat-number"><?php echo count($user_comments); ?></div>
                    <div class="stat-label">Commentaires</div>
                </div>
                <div class="stat-card">
                    <div style="font-size: 2.5rem; margin-bottom: 15px; color: #ff6b6b;">
                        <i class="fas fa-heart"></i>
                    </div>
                    <div class="stat-number"><?php echo count($user_likes); ?></div>
                    <div class="stat-label">Likes</div>
                </div>
                <div class="stat-card">
                    <div style="font-size: 2.5rem; margin-bottom: 15px; color: #4ecdc4;">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="stat-number"><?php echo count($user_comments) + count($user_likes); ?></div>
                    <div class="stat-label">Total d'Actions</div>
                </div>
            </div>

            <div class="tabs">
                <button class="tab-button active" onclick="switchTab(event, 'comments')">
                    <i class="fas fa-comments"></i> Commentaires (<?php echo count($user_comments); ?>)
                </button>
                <button class="tab-button" onclick="switchTab(event, 'likes')">
                    <i class="fas fa-heart"></i> Likes (<?php echo count($user_likes); ?>)
                </button>
            </div>

            <!-- Tab Commentaires -->
            <div id="comments" class="tab-content active">
                <h2><i class="fas fa-comments"></i> Mes Commentaires</h2>

                <?php if (empty($user_comments)): ?>
                    <div class="empty-state">
                        <i class="fas fa-comment-slash"></i>
                        <h3>Aucun commentaire</h3>
                        <p>Vous n'avez pas encore commenté de publication</p>
                        <a href="index.php">
                            <i class="fas fa-arrow-left"></i> Voir les publications
                        </a>
                    </div>
                <?php else: ?>
                    <?php foreach ($user_comments as $c): ?>
                        <div class="activity-item comment-item">
                            <div class="activity-header">
                                <div>
                                    <span class="activity-type">
                                        <i class="fas fa-comment"></i> Commentaire
                                    </span>
                                </div>
                                <span class="activity-date">
                                    <i class="fas fa-calendar-alt"></i>
                                    <?php echo date('d/m/Y \à H:i', strtotime($c['created_at'])); ?>
                                </span>
                            </div>

                            <div class="activity-content">
                                <?php echo htmlspecialchars($c['content']); ?>
                            </div>

                            <div class="post-ref">
                                <strong><i class="fas fa-document-alt"></i> Publication originale:</strong>
                                <p style="margin-top: 8px; color: var(--text-light);">
                                    <?php echo htmlspecialchars(substr($c['post_content'], 0, 100)) . (strlen($c['post_content']) > 100 ? '...' : ''); ?>
                                </p>
                            </div>

                            <div class="activity-actions">
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="comment_id" value="<?php echo $c['id']; ?>">
                                    <button type="submit" name="delete_comment" class="delete-btn" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce commentaire ?');">
                                        <i class="fas fa-trash-alt"></i> Supprimer
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Tab Likes -->
            <div id="likes" class="tab-content">
                <h2><i class="fas fa-heart"></i> Mes Likes</h2>

                <?php if (empty($user_likes)): ?>
                    <div class="empty-state">
                        <i class="fas fa-heart-broken"></i>
                        <h3>Aucun like</h3>
                        <p>Vous n'avez pas encore aimé de publication</p>
                        <a href="index.php">
                            <i class="fas fa-arrow-left"></i> Voir les publications
                        </a>
                    </div>
                <?php else: ?>
                    <?php foreach ($user_likes as $l): ?>
                        <div class="activity-item like-item">
                            <div class="activity-header">
                                <div>
                                    <span class="activity-type" style="background: rgba(255, 107, 107, 0.2); border-color: rgba(255, 107, 107, 0.4); color: #ff6b6b;">
                                        <i class="fas fa-heart"></i> Like
                                    </span>
                                </div>
                                <span class="activity-date">
                                    <i class="fas fa-calendar-alt"></i>
                                    <?php echo date('d/m/Y \à H:i', strtotime($l['created_at'])); ?>
                                </span>
                            </div>

                            <div class="post-ref">
                                <strong><i class="fas fa-user-circle"></i> Publication de <?php echo htmlspecialchars($l['author']); ?>:</strong>
                                <p style="margin-top: 8px; color: var(--text-light);">
                                    <?php echo htmlspecialchars(substr($l['post_content'], 0, 100)) . (strlen($l['post_content']) > 100 ? '...' : ''); ?>
                                </p>
                            </div>

                            <div class="activity-actions">
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="post_id" value="<?php echo $l['post_id']; ?>">
                                    <button type="submit" name="delete_like" class="delete-btn" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce like ?');">
                                        <i class="fas fa-trash-alt"></i> Supprimer
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <script src="public/js/my-activities-tabs.js"></script>
            <?php include_once 'footer.php'; ?>
    </body>

</html>
