<?php
require_once __DIR__ . '/php/edit_post_logic.php';
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Éditer mon post - ALPHA Community</title>

    <!-- Manifest et Icônes -->
    <link rel="manifest" href="manifest.json">
    <link rel="icon" type="image/x-icon" href="public/favicon_io/favicon.ico">
    <link rel="icon" type="image/png" sizes="32x32" href="public/favicon_io/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="public/favicon_io/favicon-16x16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="public/favicon_io/apple-touch-icon.png">
    <meta name="theme-color" content="#c87533">

    <link rel="stylesheet" href="public/css/edit-post.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body class="modern-layout">
    <!-- Navigation -->
    <?php include_once 'nav.php'; ?>

    <!-- Edit Post Section -->
    <div class="edit-post-container">
        <div class="edit-post-header">
            <h1>Éditer mon post</h1>
            <p>Modifiez le contenu de votre publication</p>
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

        <div class="edit-post-form">
            <!-- Infos du post -->
            <div class="post-meta">
                <p><strong>Publié le :</strong> <?php echo date('d/m/Y à H:i', strtotime($post['created_at'])); ?></p>
                <p><strong>Likes :</strong> <i class="fas fa-heart"></i> <?php echo $post['likes_count']; ?></p>
                <p><strong>Commentaires :</strong> <i class="fas fa-comment"></i> <?php echo $post['comments_count']; ?></p>
            </div>

            <!-- Prévisualisation des médias existants -->
            <?php if ($post['media_type'] !== 'none' && !empty($post['media_url'])): ?>
                <div class="existing-media-section">
                    <h3><i class="fas fa-images"></i> Médias actuels</h3>
                    <div class="existing-media-grid">
                        <?php if ($post['media_type'] === 'image'): ?>
                            <div class="existing-media-item">
                                <img src="<?php echo UPLOAD_URL . $post['media_url']; ?>" alt="Image du post">
                            </div>
                        <?php elseif ($post['media_type'] === 'video'): ?>
                            <div class="existing-media-item">
                                <video controls>
                                    <source src="<?php echo UPLOAD_URL . $post['media_url']; ?>">
                                    Votre navigateur ne supporte pas la lecture de vidéos.
                                </video>
                            </div>
                        <?php elseif ($post['media_type'] === 'multiple'): ?>
                            <?php
                            $media_files = json_decode($post['media_url'], true);
                            if (is_array($media_files)):
                                foreach ($media_files as $media):
                            ?>
                                    <div class="existing-media-item">
                                        <?php if ($media['type'] === 'image'): ?>
                                            <img src="<?php echo UPLOAD_URL . $media['filename']; ?>" alt="Image">
                                        <?php elseif ($media['type'] === 'video'): ?>
                                            <video controls>
                                                <source src="<?php echo UPLOAD_URL . $media['filename']; ?>">
                                            </video>
                                        <?php endif; ?>
                                    </div>
                            <?php
                                endforeach;
                            endif;
                            ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Formulaire d'édition -->
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="content">Contenu du post</label>
                    <textarea id="content" name="content" required><?php echo escape($post['content']); ?></textarea>
                    <div class="character-count">
                        <span id="charCount">0</span> caractères
                    </div>
                </div>

                <!-- Gestion des médias -->
                <?php if ($post['media_type'] !== 'none' && !empty($post['media_url'])): ?>
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="keep_existing_media" id="keep_existing_media" checked>
                            <span>Conserver les médias existants</span>
                        </label>
                    </div>
                <?php endif; ?>

                <div class="form-group">
                    <label for="media">
                        <div class="media-label">
                            <i class="fas fa-image"></i> Ajouter de nouveaux médias
                        </div>
                    </label>
                    <input type="file" name="media[]" id="media" accept="image/*,video/*" multiple>
                    <small style="color: rgba(255, 255, 255, 0.6); display: block; margin-top: 8px;">
                        <i class="fas fa-info-circle"></i> Sélectionnez plusieurs fichiers pour les ajouter
                    </small>
                </div>

                <div class="media-previews" id="mediaPreviews">
                    <!-- Les aperçus seront ajoutés ici par JavaScript -->
                </div>

                <div class="form-actions">
                    <button type="submit" name="update_post" class="btn-submit">
                        <i class="fas fa-save"></i> Mettre à jour
                    </button>
                    <a href="my-posts.php" class="btn-cancel">
                        <i class="fas fa-times"></i> Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <?php include_once 'footer.php'; ?>

    <script src="public/js/post-editor.js"></script>
</body>

</html>
