<?php
require_once __DIR__ . '/php/create_post_logic.php';
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un Post - <?php echo escape($_SESSION['username']); ?></title>

    <!-- Manifest et Icônes -->
    <link rel="manifest" href="manifest.json">
    <link rel="icon" type="image/x-icon" href="public/favicon_io/favicon.ico">
    <link rel="icon" type="image/png" sizes="32x32" href="public/favicon_io/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="public/favicon_io/favicon-16x16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="public/favicon_io/apple-touch-icon.png">
    <meta name="theme-color" content="#c87533">

    <link rel="stylesheet" href="public/css/create-post.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

</head>

<body class="modern-layout">
    <!-- Navigation transparente -->
    <?php include_once 'nav.php'; ?>

    <div class="container">
        <a href="index.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Retour à l'accueil
        </a>

        <div class="page-header">
            <h1><i class="fas fa-edit"></i> Créer un Post</h1>
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

        <div class="create-post-box">
            <div class="user-info">
                <img src="<?php
                            if (!empty($_SESSION['avatar'])) {
                                echo UPLOAD_URL . escape($_SESSION['avatar']);
                            } else {
                                echo 'public/images/default-avatar.png';
                            }
                            ?>" alt="Avatar" class="avatar-small">
                <div class="user-details">
                    <h3><?php echo escape($_SESSION['username']); ?></h3>
                    <p>Partage avec vos amis</p>
                </div>
            </div>

            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <textarea name="content"
                        id="content"
                        placeholder="Qu'est-ce que tu veux partager ?"
                        required></textarea>
                    <div class="char-count">
                        <span id="charCount">0</span> / 5000
                    </div>
                </div>

                <div class="form-group">
                    <label for="media" style="cursor: pointer;">
                        <div class="media-label">
                            <i class="fas fa-image"></i> Ajouter des images ou vidéos
                        </div>
                    </label>
                    <input type="file" name="media[]" id="media" accept="image/*,video/*" multiple>
                    <small style="color: rgba(255, 255, 255, 0.6); display: block; margin-top: 8px;">
                        <i class="fas fa-info-circle"></i> Vous pouvez sélectionner plusieurs fichiers
                    </small>
                </div>

                <div class="media-previews" id="mediaPreviews">
                    <!-- Les aperçus seront ajoutés ici par JavaScript -->
                </div>

                <div class="button-group">
                    <a href="index.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Annuler
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-share"></i> Publier
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="public/js/post-editor.js"></script>
    <?php include_once 'footer.php'; ?>
</body>

</html>
