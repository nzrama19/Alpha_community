<?php
require_once __DIR__ . '/php/profile_logic.php';
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil - <?php echo htmlspecialchars($user_data['username']); ?></title>

    <!-- Manifest et Icônes -->
    <link rel="manifest" href="manifest.json">
    <link rel="icon" type="image/x-icon" href="public/favicon_io/favicon.ico">
    <link rel="icon" type="image/png" sizes="32x32" href="public/favicon_io/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="public/favicon_io/favicon-16x16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="public/favicon_io/apple-touch-icon.png">
    <meta name="theme-color" content="#c87533">

    <link rel="stylesheet" href="public/css/navbar.css">
    <link rel="stylesheet" href="public/css/profile.css">
    <link rel="stylesheet" href="public/css/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

</head>

<body>
    <?php include_once 'nav.php'; ?>

    <div class="container">
        <a href="index.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Retour à l'accueil
        </a>

        <div class="profile-header">
            <div class="avatar-container">
                <?php if (!empty($user_data['avatar'])): ?>
                    <img src="<?php echo UPLOAD_URL . htmlspecialchars($user_data['avatar']); ?>" alt="<?php echo htmlspecialchars($user_data['username']); ?>" class="avatar">
                <?php else: ?>
                    <div class="avatar">
                        <?php echo strtoupper(substr($user_data['username'], 0, 1)); ?>
                    </div>
                <?php endif; ?>
            </div>

            <h1><?php echo htmlspecialchars($user_data['username']); ?></h1>
            <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($user_data['email']); ?></p>
            <p><i class="fas fa-calendar"></i> Membre depuis <?php echo date('d/m/Y', strtotime($user_data['created_at'])); ?></p>
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

        <div class="tabs">
            <button class="tab-button active" onclick="switchTab(event, 'infos')">
                <i class="fas fa-user"></i> Informations
            </button>
            <button class="tab-button" onclick="switchTab(event, 'avatar')">
                <i class="fas fa-image"></i> Avatar
            </button>
            <button class="tab-button" onclick="switchTab(event, 'password')">
                <i class="fas fa-lock"></i> Mot de passe
            </button>
            <button class="tab-button" onclick="switchTab(event, 'stats')">
                <i class="fas fa-chart-bar"></i> Statistiques
            </button>
            <a href="my-activities.php" class="tab-button" style="text-decoration: none; text-align: center; display: inline-flex; align-items: center; justify-content: center;">
                <i class="fas fa-history"></i> Mes Activités
            </a>
        </div>

        <!-- Tab Informations -->
        <div id="infos" class="tab-content active">
            <h2><i class="fas fa-user"></i> Mes Informations</h2>
            <form method="POST" style="margin-top: 20px;">
                <div class="form-group">
                    <label for="username">Nom d'utilisateur:</label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user_data['username']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Adresse email:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" required>
                </div>

                <div class="form-buttons">
                    <button type="submit" name="update_profile" class="btn btn-primary">
                        <i class="fas fa-save"></i> Enregistrer
                    </button>
                    <a href="profile.php" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> Réinitialiser
                    </a>
                </div>
            </form>
        </div>

        <!-- Tab Avatar -->
        <div id="avatar" class="tab-content">
            <h2><i class="fas fa-image"></i> Changer d'Avatar</h2>
            <form method="POST" enctype="multipart/form-data" style="margin-top: 20px;">
                <div class="form-group">
                    <label for="avatar">Sélectionner une image:</label>
                    <input type="file" id="avatar" name="avatar" accept="image/*" required>
                    <small style="color: #999; margin-top: 8px; display: block;">Format acceptés: JPG, PNG, GIF, WEBP. Taille max: 5MB</small>
                </div>

                <div class="form-buttons">
                    <button type="submit" name="update_avatar" class="btn btn-primary">
                        <i class="fas fa-upload"></i> Télécharger
                    </button>
                </div>
            </form>
        </div>

        <!-- Tab Mot de passe -->
        <div id="password" class="tab-content">
            <h2><i class="fas fa-lock"></i> Changer le Mot de Passe</h2>
            <form method="POST" style="margin-top: 20px;">
                <div class="form-group">
                    <label for="current_password">Mot de passe actuel:</label>
                    <input type="password" id="current_password" name="current_password" required>
                </div>

                <div class="form-group">
                    <label for="new_password">Nouveau mot de passe:</label>
                    <input type="password" id="new_password" name="new_password" required>
                    <small style="color: #999; margin-top: 8px; display: block;">Minimum 6 caractères</small>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirmer le mot de passe:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>

                <div class="form-buttons">
                    <button type="submit" name="change_password" class="btn btn-primary">
                        <i class="fas fa-check"></i> Changer le mot de passe
                    </button>
                </div>
            </form>
        </div>

        <!-- Tab Statistiques -->
        <div id="stats" class="tab-content">
            <h2><i class="fas fa-chart-bar"></i> Mes Statistiques</h2>
            <div class="profile-stats">
                <div class="stat-card">
                    <div class="stat-number"><?php echo count($user->getUserComments($_SESSION['user_id'])); ?></div>
                    <div class="stat-label">Commentaires</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo count($user->getUserLikes($_SESSION['user_id'])); ?></div>
                    <div class="stat-label">Likes</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">
                        <?php
                        $comments = $user->getUserComments($_SESSION['user_id']);
                        $likes = $user->getUserLikes($_SESSION['user_id']);
                        echo count($comments) + count($likes);
                        ?>
                    </div>
                    <div class="stat-label">Total d'Actions</div>
                </div>
            </div>
        </div>
    </div>

    <script src="public/js/profile.js"></script>
    <?php include_once 'footer.php'; ?>
</body>

</html>
