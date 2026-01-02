<?php
require_once __DIR__ . '/php/login_logic.php';
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Connectez-vous à votre compte ALPHA COMMUNITY pour accéder à toutes les fonctionnalités de la plateforme.">
    <title>Connexion - ALPHA Community</title>

    <!-- Manifest et Icônes -->
    <link rel="manifest" href="manifest.json">
    <link rel="icon" type="image/x-icon" href="public/favicon_io/favicon.ico">
    <link rel="icon" type="image/png" sizes="32x32" href="public/favicon_io/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="public/favicon_io/favicon-16x16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="public/favicon_io/apple-touch-icon.png">
    <meta name="theme-color" content="#c87533">

    <link rel="stylesheet" href="public/css/auth.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
</head>

<body>
    <div class="particles" id="particles"></div>
    <!-- Logo -->
    <div class="logo-container">
        <div class="logo">ALPHA</div>
        <div class="logo-subtitle">COMMUNITY</div>
    </div>

    <!-- Container de connexion -->
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <div class="auth-icon">
                    <i class="fas fa-user-circle"></i>
                </div>
                <h1>Connexion</h1>
                <p>Accédez à votre compte ALPHA Community</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <span><?php echo escape($error); ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" class="auth-form">
                <div class="form-group">
                    <label for="username">Nom d'utilisateur</label>
                    <div class="input-wrapper">
                        <i class="fas fa-user"></i>
                        <input
                            type="text"
                            id="username"
                            name="username"
                            class="form-control"
                            placeholder="Entrez votre nom d'utilisateur"
                            required
                            autocomplete="username">
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock"></i>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control"
                            placeholder="Entrez votre mot de passe"
                            required
                            autocomplete="current-password">
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="display: flex; align-items: center; cursor: pointer; user-select: none;">
                        <input
                            type="checkbox"
                            name="remember"
                            id="remember"
                            style="margin-right: 8px; width: 18px; height: 18px; cursor: pointer;"
                            checked>
                        <span style="font-size: 14px; color: var(--text-primary);">
                            <i class="fas fa-bookmark" style="margin-right: 5px; color: var(--primary);"></i>
                            Se souvenir de moi (30 jours)
                        </span>
                    </label>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fas fa-sign-in-alt"></i> Se connecter
                </button>
            </form>

            <div class="auth-links">
                <p>
                    Pas encore de compte ?
                    <a href="register.php">Créer un compte gratuitement</a>
                </p>
                <p style="margin-top: 15px;">
                    <a href="index.php">
                        <i class="fas fa-arrow-left"></i> Retour à l'accueil
                    </a>
                </p>
            </div>
        </div>
    </div>

    <script src="public/js/auth-login.js"></script>
</body>

</html>
