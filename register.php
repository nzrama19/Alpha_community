<?php
require_once __DIR__ . '/php/register_logic.php';
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Inscrivez-vous sur ALPHA COMMUNITY et rejoignez notre communauté. Créez votre compte en quelques étapes simples.">
    <title>Inscription - ALPHA Community</title>

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

    <!-- Container d'inscription -->
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <div class="auth-icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <h1>Créer un compte</h1>
                <p>Rejoignez ALPHA Community et partagez vos idées</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <span><?php echo escape($error); ?></span>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <span><?php echo escape($success); ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" class="auth-form" id="registerForm">
                <div class="form-group">
                    <label for="username">Nom d'utilisateur</label>
                    <div class="input-wrapper">
                        <i class="fas fa-user"></i>
                        <input
                            type="text"
                            id="username"
                            name="username"
                            class="form-control"
                            placeholder="Choisissez votre nom d'utilisateur"
                            value="<?php echo isset($_POST['username']) ? escape($_POST['username']) : ''; ?>"
                            minlength="3"
                            required
                            autocomplete="username">
                    </div>
                    <small style="color: var(--text-muted); font-size: 12px; margin-top: 4px; display: block;">
                        Au moins 3 caractères
                    </small>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <div class="input-wrapper">
                        <i class="fas fa-envelope"></i>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-control"
                            placeholder="votre@email.com"
                            value="<?php echo isset($_POST['email']) ? escape($_POST['email']) : ''; ?>"
                            required
                            autocomplete="email">
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
                            placeholder="Créez un mot de passe sécurisé"
                            minlength="8"
                            required
                            autocomplete="new-password">
                    </div>
                    <small style="color: var(--text-muted); font-size: 12px; margin-top: 4px; display: block;">
                        Au moins 8 caractères, 1 majuscule, 1 minuscule, 1 chiffre et 1 caractère spécial
                    </small>
                    <div id="passwordStrength" class="password-strength" style="display: none;"></div>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirmer le mot de passe</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock"></i>
                        <input
                            type="password"
                            id="confirm_password"
                            name="confirm_password"
                            class="form-control"
                            placeholder="Confirmez votre mot de passe"
                            minlength="8"
                            required
                            autocomplete="new-password">
                    </div>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fas fa-user-plus"></i> Créer mon compte
                </button>
            </form>

            <div class="auth-links">
                <p>
                    Vous avez déjà un compte ?
                    <a href="login.php">Se connecter</a>
                </p>
                <p style="margin-top: 15px;">
                    <a href="index.php">
                        <i class="fas fa-arrow-left"></i> Retour à l'accueil
                    </a>
                </p>
            </div>
        </div>
    </div>

    <script src="public/js/auth-register.js"></script>
</body>

</html>
