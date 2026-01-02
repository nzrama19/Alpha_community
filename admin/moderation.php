<?php
require_once __DIR__ . '/php/moderation_logic.php';
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modération des Publications - Admin</title>

    <!-- Favicon Admin -->
    <link rel="manifest" href="manifest.json">
    <link rel="icon" type="image/x-icon" href="public/admin_favicon_io/favicon.ico">
    <link rel="icon" type="image/png" sizes="32x32" href="public/admin_favicon_io/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="public/admin_favicon_io/favicon-16x16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="public/admin_favicon_io/apple-touch-icon.png">
    <meta name="theme-color" content="#00D4FF">

    <link rel="stylesheet" href="public/css/moderation.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">


</head>

<body class="modern-layout admin-page">

    <?php include 'nav.php'; ?>

    <div class="moderation-container">
        <div class="page-header">
            <h1><i class="fas fa-shield-alt"></i> Modération des Publications</h1>
            <p>Approuvez ou rejetez les publications des utilisateurs</p>
        </div>

        <?php echo $message; ?>

        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card pending">
                <div class="stat-number"><?php echo $pendingCount; ?></div>
                <div class="stat-label">En attente</div>
            </div>
            <div class="stat-card approved">
                <div class="stat-number"><?php echo $approvedCount; ?></div>
                <div class="stat-label">Approuvées</div>
            </div>
            <div class="stat-card rejected">
                <div class="stat-number"><?php echo $rejectedCount; ?></div>
                <div class="stat-label">Rejetées</div>
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="filter-tabs">
            <a href="?filter=pending" class="filter-tab <?php echo $filter === 'pending' ? 'active' : ''; ?>">
                <i class="fas fa-clock"></i> En attente <span class="badge"><?php echo $pendingCount; ?></span>
            </a>
            <a href="?filter=approved" class="filter-tab <?php echo $filter === 'approved' ? 'active' : ''; ?>">
                <i class="fas fa-check"></i> Approuvées <span class="badge"><?php echo $approvedCount; ?></span>
            </a>
            <a href="?filter=rejected" class="filter-tab <?php echo $filter === 'rejected' ? 'active' : ''; ?>">
                <i class="fas fa-times"></i> Rejetées <span class="badge"><?php echo $rejectedCount; ?></span>
            </a>
            <a href="?filter=all" class="filter-tab <?php echo $filter === 'all' ? 'active' : ''; ?>">
                <i class="fas fa-list"></i> Toutes
            </a>
        </div>

        <!-- Posts List -->
        <div class="posts-grid">
            <?php if (empty($posts)): ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <h3>Aucune publication</h3>
                    <p>
                        <?php
                        switch ($filter) {
                            case 'pending':
                                echo "Aucune publication en attente de modération.";
                                break;
                            case 'approved':
                                echo "Aucune publication approuvée.";
                                break;
                            case 'rejected':
                                echo "Aucune publication rejetée.";
                                break;
                            default:
                                echo "Aucune publication trouvée.";
                        }
                        ?>
                    </p>
                </div>
            <?php else: ?>
                <?php foreach ($posts as $p): ?>
                    <div class="moderation-card status-<?php echo $p['status'] ?? 'pending'; ?>">
                        <div class="card-header">
                            <div class="author-info">
                                <img src="<?php
                                            if (!empty($p['avatar'])) {
                                                echo UPLOAD_URL . escape($p['avatar']);
                                            } else {
                                                echo BASE_URL . 'public/images/default-avatar.png';
                                            }
                                            ?>" alt="Avatar" class="author-avatar">
                                <div class="author-details">
                                    <h4><?php echo escape($p['username']); ?></h4>
                                    <span>
                                        <?php echo $p['author_type'] === 'admin' ? '<i class="fas fa-shield-alt"></i> Admin' : '<i class="fas fa-user"></i> Utilisateur'; ?>
                                        • <?php echo date('d/m/Y à H:i', strtotime($p['created_at'])); ?>
                                    </span>
                                </div>
                            </div>
                            <span class="status-badge <?php echo $p['status'] ?? 'pending'; ?>">
                                <?php
                                switch ($p['status'] ?? 'pending') {
                                    case 'pending':
                                        echo '<i class="fas fa-clock"></i> En attente';
                                        break;
                                    case 'approved':
                                        echo '<i class="fas fa-check"></i> Approuvée';
                                        break;
                                    case 'rejected':
                                        echo '<i class="fas fa-times"></i> Rejetée';
                                        break;
                                }
                                ?>
                            </span>
                        </div>

                        <div class="card-content">
                            <?php
                            $content = $p['content'];
                            $lines = explode("\n", $content);
                            $firstLine = isset($lines[0]) ? trim($lines[0]) : '';
                            $hasMoreContent = strlen($content) > 200 || count($lines) > 1;
                            ?>
                            <p class="post-text <?php echo $hasMoreContent ? 'expandable' : ''; ?>" data-post-id="<?php echo $p['id']; ?>">
                                <span class="post-preview"><?php echo nl2br(escape($firstLine)); ?></span>
                                <?php if ($hasMoreContent): ?>
                                    <span class="post-full-content" style="display:none;"><?php echo nl2br(escape($content)); ?></span>
                                <?php endif; ?>
                            </p>
                            <?php if ($hasMoreContent): ?>
                                <button class="btn-expand-text" onclick="toggleContent(<?php echo $p['id']; ?>)">
                                    <i class="fas fa-chevron-down"></i> Lire plus
                                </button>
                            <?php endif; ?>
                        </div>

                        <?php if ($p['media_type'] !== 'none' && !empty($p['media_url'])): ?>
                            <div class="card-media">
                                <?php if ($p['media_type'] === 'image'): ?>
                                    <div class="media-item image-media">
                                        <div class="media-indicator">
                                            <i class="fas fa-image"></i> Image
                                        </div>
                                        <img src="<?php echo UPLOAD_URL . $p['media_url']; ?>" alt="Image" onclick="openMediaModal(this.src, 'image')">
                                    </div>
                                <?php elseif ($p['media_type'] === 'video'): ?>
                                    <div class="media-item video-media">
                                        <div class="media-indicator">
                                            <i class="fas fa-video"></i> Vidéo
                                        </div>
                                        <video controls preload="metadata">
                                            <source src="<?php echo UPLOAD_URL . $p['media_url']; ?>" type="video/mp4">
                                            Votre navigateur ne supporte pas la lecture de vidéos.
                                        </video>
                                    </div>
                                <?php elseif ($p['media_type'] === 'multiple'): ?>
                                    <?php $media_files = json_decode($p['media_url'], true); ?>
                                    <?php if (is_array($media_files) && !empty($media_files)): ?>
                                        <div class="media-indicator">
                                            <i class="fas fa-images"></i> <?php echo count($media_files); ?> médias
                                        </div>
                                        <div class="media-gallery">
                                            <?php foreach ($media_files as $index => $media): ?>
                                                <?php if ($media['type'] === 'image'): ?>
                                                    <div class="gallery-item image-item">
                                                        <img src="<?php echo UPLOAD_URL . $media['filename']; ?>" alt="Image <?php echo $index + 1; ?>" onclick="openMediaModal(this.src, 'image')">
                                                        <div class="gallery-overlay">
                                                            <i class="fas fa-search-plus"></i>
                                                        </div>
                                                    </div>
                                                <?php elseif ($media['type'] === 'video'): ?>
                                                    <div class="gallery-item video-item">
                                                        <video controls preload="metadata">
                                                            <source src="<?php echo UPLOAD_URL . $media['filename']; ?>" type="video/mp4">
                                                        </video>
                                                        <div class="video-overlay">
                                                            <i class="fas fa-play-circle"></i>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <div class="card-footer">
                            <div class="post-stats">
                                <span><i class="fas fa-heart"></i> <?php echo $p['likes_count']; ?> likes</span>
                                <span><i class="fas fa-comment"></i> <?php echo $p['comments_count']; ?> commentaires</span>
                            </div>

                            <div class="action-buttons">
                                <?php if (($p['status'] ?? 'pending') !== 'approved'): ?>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="post_id" value="<?php echo $p['id']; ?>">
                                        <input type="hidden" name="action" value="approve">
                                        <button type="submit" class="btn-action btn-approve">
                                            <i class="fas fa-check"></i> Approuver
                                        </button>
                                    </form>
                                <?php endif; ?>

                                <?php if (($p['status'] ?? 'pending') !== 'rejected'): ?>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="post_id" value="<?php echo $p['id']; ?>">
                                        <input type="hidden" name="action" value="reject">
                                        <button type="submit" class="btn-action btn-reject">
                                            <i class="fas fa-ban"></i> Rejeter
                                        </button>
                                    </form>
                                <?php endif; ?>

                                <form method="POST" style="display: inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette publication ?');">
                                    <input type="hidden" name="post_id" value="<?php echo $p['id']; ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <button type="submit" class="btn-action btn-delete">
                                        <i class="fas fa-trash"></i> Supprimer
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal pour afficher les images -->
    <div id="mediaModal" class="media-modal" onclick="closeMediaModal()">
        <span class="modal-close">&times;</span>
        <img id="modalImage" class="modal-content-media">
    </div>

    <script src="../public/js/main.js"></script>
    <script src="public/js/admin-moderation.js"></script>
</body>

</html>
