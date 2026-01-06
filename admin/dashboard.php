<?php
require_once __DIR__ . '/php/dashboard_logic.php';
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Bienvenue sur ALPHA COMMUNITY - Une plateforme de partage communautaire où vous pouvez découvrir, commenter et liker des publications de qualité.">
    <meta name="keywords" content="blog, communauté, publications, commentaires, likes">
    <meta property="og:title" content="ALPHA COMMUNITY - Partagez vos Histoires">
    <meta property="og:description" content="Plateforme de partage communautaire - Découvrez, commentez et interagissez.">
    <meta property="og:type" content="website">
    <title>Dashboard Admin - Gestion de Alpha Community</title>

    <!-- Manifest et Favicon Admin -->
    <link rel="manifest" href="manifest.json">
    <link rel="icon" type="image/x-icon" href="public/favicon_io/favicon.ico">
    <link rel="icon" type="image/png" sizes="32x32" href="public/favicon_io/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="public/favicon_io/favicon-16x16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="public/favicon_io/apple-touch-icon.png">
    <meta name="theme-color" content="#00D4FF">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="ALPHA">
    <!-- Stylesheets -->
    <link rel="stylesheet" href="public/css/style.css">
    <link rel="stylesheet" href="../public/css/page-loader.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body class="modern-layout admin-page">
    <!-- Admin Page Loader - Style Prime Tech -->
    <div class="page-loader admin-page-loader">
        <div class="loader-glow-left"></div>
        <div class="loader-content">
            <!-- Logo avec arcs de cercle tournants -->
            <div class="loader-logo-wrapper">
                <div class="loader-arc loader-arc-1"></div>
                <div class="loader-arc loader-arc-2"></div>
                <div class="loader-logo-circle">
                    <img src="public/favicon_io/android-chrome-192x192.png" alt="Admin Logo" class="loader-logo-img">
                </div>
            </div>
            <div class="loader-logo-container">
                <div class="loader-logo">ALPHA</div>
            </div>
            <div class="loader-subtitle">ADMIN PANEL</div>
            <div class="loader-counter">
                <div class="loader-percentage">0</div>
            </div>
            <div class="loader-progress-container">
                <div class="loader-progress-bar"></div>
            </div>
            <div class="loader-text">
                <span>C</span><span>H</span><span>A</span><span>R</span><span>G</span><span>E</span><span>M</span><span>E</span><span>N</span><span>T</span>
            </div>
        </div>
    </div>

    <?php include 'nav.php'; ?>

    <div class="container-wide publications-section" style="padding-top: 100px;">
        <div class="section-header-center">
            <span class="section-tag">TABLEAU DE BORD</span>
            <h2>Gestion des Publications</h2>
        </div>

        <?php echo $message; ?>

        <div class="create-post-section-modern">
            <h2><i class="fas fa-plus-circle"></i> Créer une nouvelle publication</h2>
            <form method="POST" enctype="multipart/form-data" class="create-post-form-modern">
                <textarea name="content" placeholder="Quoi de neuf ?" required></textarea>

                <div class="media-upload-modern">
                    <label for="media" class="media-label">
                        <i class="fas fa-image"></i> Ajouter une image ou vidéo
                    </label>
                    <input type="file" id="media" name="media" accept="image/*,video/*">
                    <span id="file-name" class="file-name"></span>
                </div>

                <button type="submit" name="create_post" class="btn-cta-large">
                    <i class="fas fa-paper-plane"></i> Publier
                </button>
            </form>
        </div>

        <div class="posts-feed">
            <h2 style="text-align: center; color: white; margin-bottom: 2rem; font-size: 1.8rem;">
                <i class="fas fa-list"></i> Mes Publications (<?php echo count($posts); ?>)
            </h2>

            <?php if (empty($posts)): ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <h3>Aucune publication pour le moment</h3>
                    <p>Créez votre première publication dès maintenant !</p>
                </div>
            <?php else: ?>
                <?php foreach ($posts as $p): ?>
                    <div class="post-card">
                        <div class="post-header">
                            <img src="<?php
                                        if (!empty($p['avatar'])) {
                                            // Les photos/avatars sont tous dans UPLOAD_URL maintenant
                                            echo UPLOAD_URL . escape($p['avatar']);
                                        } else {
                                            echo BASE_URL . 'public/images/default-avatar.png';
                                        }
                                        ?>" alt="Avatar" class="avatar">
                            <div class="post-info">
                                <strong><?php echo escape($p['username']); ?></strong>
                                <span class="admin-badge"><i class="fas fa-shield-alt"></i> Admin</span>
                                <span class="post-date"><i class="fas fa-calendar"></i> <?php echo date('d/m/Y à H:i', strtotime($p['created_at'])); ?></span>
                            </div>
                        </div>

                        <div class="post-content">
                            <p><?php echo nl2br(escape($p['content'])); ?></p>

                            <?php if ($p['media_type'] === 'image'): ?>
                                <div class="media-container" onclick="openMediaModal('<?php echo UPLOAD_URL . $p['media_url']; ?>', 'image')">
                                    <img src="<?php echo UPLOAD_URL . $p['media_url']; ?>" alt="Image" class="post-media">
                                    <div class="media-overlay">
                                        <i class="fas fa-search-plus"></i>
                                    </div>
                                </div>
                            <?php elseif ($p['media_type'] === 'video'): ?>
                                <div class="media-container video-container">
                                    <video controls class="post-media">
                                        <source src="<?php echo UPLOAD_URL . $p['media_url']; ?>">
                                        Votre navigateur ne supporte pas la lecture de vidéos.
                                    </video>
                                    <div class="video-indicator">
                                        <i class="fas fa-play-circle"></i>
                                    </div>
                                </div>
                            <?php elseif ($p['media_type'] === 'multiple'): ?>
                                <?php
                                $media_files = json_decode($p['media_url'], true);
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
                            <button class="btn-like" data-post-id="<?php echo $p['id']; ?>">
                                <i class="far fa-heart"></i>
                                <span class="likes-count"><?php echo $p['likes_count']; ?></span>
                            </button>
                            <button class="btn-comment" data-post-id="<?php echo $p['id']; ?>">
                                <i class="far fa-comment"></i>
                                <span class="comments-count"><?php echo $p['comments_count']; ?></span>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal pour afficher les images en grand -->
    <div id="mediaModal" class="media-modal" onclick="closeMediaModal()">
        <span class="modal-close">&times;</span>
        <img id="modalImage" class="modal-content-media" style="display:none;">
        <video id="modalVideo" class="modal-content-media" controls style="display:none;">
            <source id="modalVideoSource" src="">
        </video>
    </div>

    <script src="../public/js/main.js"></script>
    <script src="public/js/admin-dashboard.js"></script>
    <script src="../public/js/page-loader.js"></script>
    <script>
        // Ajouter l'attribut data-username pour le toast de bienvenue admin
        document.addEventListener('DOMContentLoaded', function() {
            const body = document.body;
            const usernameDiv = document.createElement('div');
            usernameDiv.setAttribute('data-username', '<?php echo escape($_SESSION['admin_username'] ?? ''); ?>');
            usernameDiv.style.display = 'none';
            body.appendChild(usernameDiv);
        });
    </script>
</body>

</html>
