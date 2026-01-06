<?php
require_once __DIR__ . '/php/edit_profile_logic.php';
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier le profil - Admin</title>

    <!-- Favicon Admin -->
    <link rel="apple-touch-icon" sizes="180x180" href="public/favicon_io/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="public/favicon_io/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="public/favicon_io/favicon-16x16.png">
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#00D4FF">
    <link rel="stylesheet" href="public/css/profile.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body class="modern-layout admin-page">
    <!-- Navigation moderne -->
    <?php include 'nav.php'; ?>

    <div class="edit-profile-container">
        <!-- En-tête de la page -->
        <div class="page-header">
            <h1>Mon Profil Administrateur</h1>
            <p>Gérez vos informations personnelles et votre sécurité</p>
        </div>

        <!-- Messages de notification -->
        <?php if ($message): ?>
            <div class="alert success">
                <i class="fas fa-check-circle"></i>
                <span><?php echo escape($message); ?></span>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert error">
                <i class="fas fa-exclamation-circle"></i>
                <span><?php echo escape($error); ?></span>
            </div>
        <?php endif; ?>

        <!-- Grille principale -->
        <div class="profile-grid">
            <!-- Carte profil avec avatar -->
            <div class="profile-card">
                <div class="avatar-section">
                    <div class="avatar-preview">
                        <img src="<?php echo UPLOAD_URL . escape($current_admin['photo']); ?>" alt="Avatar Admin">
                    </div>
                    <div class="admin-info">
                        <h3><?php echo escape($current_admin['username']); ?></h3>
                        <div class="admin-badge">
                            <i class="fas fa-shield-alt"></i>
                            <span>Administrateur</span>
                        </div>
                        <div class="admin-stats">
                            <div class="stat-item">
                                <div class="value"><i class="fas fa-calendar"></i></div>
                                <div class="label">Membre depuis</div>
                                <div class="date-value"><?php echo date('d/m/Y', strtotime($current_admin['created_at'])); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Formulaire de modification du profil -->
            <div class="profile-section">
                <h2><i class="fas fa-user-edit"></i> Informations du profil</h2>
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="username">
                            <i class="fas fa-user"></i>
                            Nom d'utilisateur
                        </label>
                        <input type="text"
                            id="username"
                            name="username"
                            value="<?php echo escape($current_admin['username']); ?>"
                            placeholder="Votre nom d'utilisateur"
                            required>
                    </div>

                    <div class="form-group">
                        <label for="email">
                            <i class="fas fa-envelope"></i>
                            Adresse email
                        </label>
                        <input type="email"
                            id="email"
                            name="email"
                            value="<?php echo escape($current_admin['email']); ?>"
                            placeholder="votre@email.com"
                            required>
                    </div>

                    <div class="form-group">
                        <label for="photo">
                            <i class="fas fa-image"></i>
                            Changer la photo de profil
                        </label>
                        <input type="file"
                            id="photo"
                            name="photo"
                            accept="image/*">
                        <small>Formats acceptés: JPG, JPEG, PNG, GIF, WEBP (Max: 5MB)</small>
                    </div>

                    <button type="submit" name="update_profile" class="btn-submit">
                        <i class="fas fa-save"></i>
                        Enregistrer les modifications
                    </button>
                </form>
            </div>
        </div>

        <!-- Section de changement de mot de passe -->
        <div class="profile-section">
            <h2><i class="fas fa-lock"></i> Sécurité & Mot de passe</h2>
            <form method="POST">
                <div class="form-group">
                    <label for="current_password">
                        <i class="fas fa-key"></i>
                        Mot de passe actuel
                    </label>
                    <input type="password"
                        id="current_password"
                        name="current_password"
                        placeholder="••••••••"
                        required>
                </div>

                <div class="form-group">
                    <label for="new_password">
                        <i class="fas fa-lock"></i>
                        Nouveau mot de passe
                    </label>
                    <input type="password"
                        id="new_password"
                        name="new_password"
                        placeholder="••••••••"
                        required
                        minlength="6">
                    <small>Minimum 6 caractères - Utilisez des lettres, chiffres et symboles</small>
                </div>

                <div class="form-group">
                    <label for="confirm_password">
                        <i class="fas fa-lock"></i>
                        Confirmer le nouveau mot de passe
                    </label>
                    <input type="password"
                        id="confirm_password"
                        name="confirm_password"
                        placeholder="••••••••"
                        required
                        minlength="6">
                </div>

                <button type="submit" name="change_password" class="btn-submit">
                    <i class="fas fa-shield-alt"></i>
                    Mettre à jour le mot de passe
                </button>
            </form>
        </div>
    </div>
</body>

</html>
