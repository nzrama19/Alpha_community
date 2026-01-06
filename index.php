<?php
require_once __DIR__ . '/php/index_logic.php';
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
    <title>ALPHA Community - Partagez vos Histoires</title>

    <!-- Manifest et Icônes -->
    <link rel="manifest" href="manifest.json">
    <link rel="icon" type="image/x-icon" href="public/favicon_io/favicon.ico">
    <link rel="icon" type="image/png" sizes="32x32" href="public/favicon_io/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="public/favicon_io/favicon-16x16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="public/favicon_io/apple-touch-icon.png">
    <meta name="theme-color" content="#c87533">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="ALPHA">
    <!-- Stylesheets -->
    <link rel="stylesheet" href="public/css/index.css">
    <link rel="stylesheet" href="public/css/page-loader.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body class="modern-layout">
    <!-- Page Loader - Style Prime Tech -->
    <div class="page-loader">
        <div class="loader-glow-left"></div>
        <div class="loader-content">
            <!-- Logo avec arcs de cercle tournants -->
            <div class="loader-logo-wrapper">
                <div class="loader-arc loader-arc-1"></div>
                <div class="loader-arc loader-arc-2"></div>
                <div class="loader-logo-circle">
                    <img src="public/favicon_io/android-chrome-192x192.png" alt="Alpha Logo" class="loader-logo-img">
                </div>
            </div>
            <div class="loader-logo-container">
                <div class="loader-logo">ALPHA</div>
            </div>
            <div class="loader-subtitle">COMMUNITY</div>
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

    <!-- Navigation transparente -->
    <?php include_once  'nav.php'; ?>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <h1 class="hero-title">Bienvenue sur<br> <strong class="title_2">ALPHA</strong> <br>Community</h1>
            <p class="hero-subtitle">Découvrez, Commentez et Partagez vos Opinions</p>
            <a href="categories.php" class="btn-hero">EXPLORER</a>
        </div>
        <div class="hero-scroll">
            <span>Scroll</span>
            <i class="fas fa-arrow-down"></i>
        </div>
    </section>

    <!-- Featured Section -->
    <section class="featured-section">
        <div class="container-wide">
            <div class="featured-content">
                <div class="featured-image">
                    <img src="public/images/2.jpg" alt="Camping">
                </div>
                <div class="featured-text">
                    <span class="section-tag">NOTRE PLATEFORME</span>
                    <h2>Un Espace d'Échange<br>et de Partage</h2>
                    <p>Notre blog vous permet de découvrir du contenu de qualité publié par d'autres utilisateurs. Interagissez en likant les publications qui vous plaisent et partagez votre point de vue dans les commentaires. Rejoignez une communauté active et engagée.</p>
                    <a href="about.php" class="btn-secondary">En savoir plus</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Publications Section -->
    <div class="container-wide publications-section">
        <div class="section-header-center">
            <span class="section-tag">NOS PUBLICATIONS</span>
            <h2>Explorez & Découvrez</h2>
        </div>

        <!-- Section Créer un Post -->
        <?php if (isUserLoggedIn()): ?>
            <div class="create-post-section">
                <div class="create-post-header">
                    <img src="<?php
                                if (!empty($_SESSION['avatar'])) {
                                    echo UPLOAD_URL . escape($_SESSION['avatar']);
                                } else {
                                    echo 'https://ui-avatars.com/api/?name=' . urlencode($_SESSION['username']) . '&background=d4a574&color=fff';
                                }
                                ?>" alt="Avatar" class="avatar-small">
                    <a href="create-post.php" class="create-post-input">
                        <i class="fas fa-edit"></i> Qu'est-ce que tu veux partager ?
                    </a>
                </div>
            </div>
        <?php endif; ?>
        <?php if (empty($posts)): ?>
            <!-- aucune publication -->
            <div class="empty-state">
                <i class="fas fa-compass"></i>
                <h3>Aucune publication pour le moment</h3>
                <p>Revenez plus tard pour découvrir du nouveau contenu !</p>
                <?php if (!isUserLoggedIn() && !isAdminLoggedIn()): ?>
                    <a href="register.php" class="btn-hero">
                        Rejoindre la communauté
                    </a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <!--publication active -->
            <div class="posts-feed">
                <?php foreach ($posts as $post): ?>
                    <?php
                    $comments = $commentModel->getByPostId($post['id']);
                    $hasLiked = isUserLoggedIn() ? $postModel->hasUserLiked($post['id'], $_SESSION['user_id']) : false;
                    ?>

                    <div class="post-card" data-post-id="<?php echo $post['id']; ?>">
                        <div class="post-header">
                            <img src="<?php
                                        if (!empty($post['avatar'])) {
                                            // Tous les avatars/photos sont dans UPLOAD_URL
                                            echo UPLOAD_URL . escape($post['avatar']);
                                        } else {
                                            echo BASE_URL . 'public/images/default-avatar.png';
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
                                <button class="btn-read-more" data-post-id="<?php echo $post['id']; ?>">
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
                                        <img src="<?php echo !empty($comment['avatar']) ? UPLOAD_URL . escape($comment['avatar']) : BASE_URL . 'public/images/default-avatar.png'; ?>" alt="Avatar" class="avatar-small">
                                        <div class="comment-content">
                                            <div class="comment-header">
                                                <strong><?php echo escape($comment['username']); ?></strong>
                                                <span class="comment-date"><?php echo date('d/m/Y H:i', strtotime($comment['created_at'])); ?></span>
                                            </div>
                                            <p style="color: rgb(0, 0, 0)"><?php echo nl2br(escape($comment['content'])); ?></p>
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
            </div>
        <?php endif; ?>
    </div>

    <!-- Stats Section -->
    <section class="stats-section-dark">
        <div class="container-wide">
            <div class="stats-header">
                <h2>NOTRE COMMUNAUTÉ</h2>
            </div>
            <div class="stats-grid-modern">
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3>10K+</h3>
                    <p>Membres Actifs</p>
                </div>
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <h3>247</h3>
                    <p>Articles Publiés</p>
                </div>
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="fas fa-comments"></i>
                    </div>
                    <h3>3460</h3>
                    <p>Commentaires Partagés</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section-modern">
        <div class="cta-overlay"></div>
        <div class="cta-content">
            <h2>Rejoignez Notre Communauté dès Maintenant !</h2>
            <p>Inscrivez-vous gratuitement pour commenter et interagir avec nos publications</p>
            <a href="register.php" class="btn-cta-large">INSCRIPTION GRATUITE</a>
        </div>
    </section>

    <!-- Modern Footer -->
    <?php include_once 'footer.php'; ?>

    <!-- Modal pour afficher les images en grand -->
    <div id="mediaModal" class="media-modal" onclick="closeMediaModal()">
        <span class="modal-close">&times;</span>
        <img id="modalImage" class="modal-content-media" style="display:none;">
        <video id="modalVideo" class="modal-content-media" controls style="display:none;">
            <source id="modalVideoSource" src="">
        </video>
    </div>

    <script src="public/js/main.js"></script>
    <script src="public/js/posts-media.js"></script>
    <script src="public/js/page-loader.js"></script>
    <?php if (isUserLoggedIn()): ?>
        <script>
            // Ajouter l'attribut data-username pour le toast de bienvenue
            document.addEventListener('DOMContentLoaded', function() {
                const body = document.body;
                const usernameDiv = document.createElement('div');
                usernameDiv.setAttribute('data-username', '<?php echo escape($_SESSION['username'] ?? ''); ?>');
                usernameDiv.style.display = 'none';
                body.appendChild(usernameDiv);
            });
        </script>
    <?php endif; ?>
</body>

</html>
