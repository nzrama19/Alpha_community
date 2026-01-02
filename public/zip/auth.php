<?php
require_once 'config/config.php';
require_once 'includes/User.php';

// Initialisation des variables
$isRegister = isset($_GET['register']) || basename($_SERVER['PHP_SELF']) === 'register.php';
$error = '';
$success = '';
$formData = [];

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = new User();

    if ($isRegister) {
        // Traitement de l'inscription
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        $formData = ['username' => $username, 'email' => $email];

        if (empty($username) || empty($email) || empty($password)) {
            $error = "Tous les champs sont obligatoires.";
        } elseif ($password !== $confirm_password) {
            $error = "Les mots de passe ne correspondent pas.";
        } elseif (strlen($password) < 6) {
            $error = "Le mot de passe doit contenir au moins 6 caractères.";
        } elseif (strlen($username) < 3) {
            $error = "Le nom d'utilisateur doit contenir au moins 3 caractères.";
        } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            $error = "Le nom d'utilisateur ne peut contenir que des lettres, chiffres et underscores.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Adresse email invalide.";
        } elseif ($user->usernameExists($username)) {
            $error = "Ce nom d'utilisateur est déjà utilisé.";
        } elseif ($user->emailExists($email)) {
            $error = "Cette adresse email est déjà utilisée.";
        } else {
            if ($user->create($username, $email, $password)) {
                $success = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
                $formData = [];
            } else {
                $error = "Une erreur est survenue lors de l'inscription.";
            }
        }
    } else {
        // Traitement de la connexion
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            $error = "Tous les champs sont obligatoires.";
        } else {
            // Tentative de connexion avec username
            $userData = $user->authenticate($username, $password);

            // Si échec, tentative avec email
            if (!$userData && filter_var($username, FILTER_VALIDATE_EMAIL)) {
                $userByEmail = $user->getByEmail($username);
                if ($userByEmail) {
                    $userData = $user->authenticate($userByEmail['username'], $password);
                }
            }

            if ($userData) {
                $_SESSION['user_id'] = $userData['id'];
                $_SESSION['username'] = $userData['username'];
                $_SESSION['user_type'] = 'user';
                header('Location: index.php');
                exit();
            } else {
                $error = "Nom d'utilisateur/email ou mot de passe incorrect.";
            }
        }
    }
}


?>
<!DOCTYPE html>
<html lang="fr" class="<?php echo $isRegister ? 'register-mode' : 'login-mode'; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $isRegister ? 'Inscription' : 'Connexion'; ?> - ALPHA Community</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="public/css/auths.css">
</head>

<body>
    <div class="auth-wrapper" id="authWrapper">
        <!-- Section Welcome -->
        <div class="welcome-section">
            <div class="welcome-content">
                <h1 class="welcome-title">
                    <?php echo $isRegister ? 'WELCOME!' : 'WELCOME<br>BACK!'; ?>
                </h1>
                <p class="welcome-text">
                    <?php echo $isRegister
                        ? 'Rejoignez notre communauté et commencez à partager vos idées avec des milliers de membres.'
                        : 'Nous sommes ravis de vous revoir ! Connectez-vous pour continuer votre expérience.'; ?>
                </p>
                <?php if ($isRegister): ?>
                    <ul class="welcome-features">
                        <li><i class="fas fa-check-circle"></i> Accès à toutes les fonctionnalités</li>
                        <li><i class="fas fa-check-circle"></i> Personnalisation du profil</li>
                        <li><i class="fas fa-check-circle"></i> Support prioritaire</li>
                    </ul>
                <?php endif; ?>
            </div>
            <div class="welcome-logo">ALPHA</div>
        </div>

        <!-- Section Formulaire -->
        <div class="form-section">
            <div class="mode-switch">
                <button class="switch-btn <?php echo !$isRegister ? 'active' : ''; ?>" data-mode="login">
                    Login
                </button>
                <button class="switch-btn <?php echo $isRegister ? 'active' : ''; ?>" data-mode="register">
                    Sign Up
                </button>
            </div>

            <div class="form-header">
                <h2 class="form-title"><?php echo $isRegister ? 'Inscription' : 'Connexion'; ?></h2>
                <p class="form-subtitle">
                    <?php echo $isRegister ? 'Créez votre compte' : 'Accédez à votre compte'; ?>
                </p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error" role="alert">
                    <i class="fas fa-exclamation-circle"></i>
                    <span><?php echo escape($error); ?></span>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success" role="alert">
                    <i class="fas fa-check-circle"></i>
                    <span><?php echo escape($success); ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" id="authForm" novalidate>
                <?php if ($isRegister): ?>
                    <!-- Formulaire d'inscription -->
                    <div class="form-group">
                        <label for="username">Nom d'utilisateur *</label>
                        <div class="input-wrapper">
                            <input type="text" id="username" name="username" class="form-control"
                                placeholder="Votre nom d'utilisateur"
                                value="<?php echo isset($formData['username']) ? escape($formData['username']) : ''; ?>"
                                minlength="3" maxlength="50" required autocomplete="username" pattern="[a-zA-Z0-9_]+">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="validation-message" id="username-error"></div>
                    </div>

                    <div class="form-group">
                        <label for="email">Email *</label>
                        <div class="input-wrapper">
                            <input type="email" id="email" name="email" class="form-control" placeholder="votre@email.com"
                                value="<?php echo isset($formData['email']) ? escape($formData['email']) : ''; ?>"
                                maxlength="100" required autocomplete="email">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="validation-message" id="email-error"></div>
                    </div>

                    <div class="form-group">
                        <label for="password">Mot de passe *</label>
                        <div class="input-wrapper">
                            <input type="password" id="password" name="password" class="form-control"
                                placeholder="Minimum 6 caractères" minlength="6" required autocomplete="new-password">
                            <i class="fas fa-lock"></i>
                            <button type="button" class="password-toggle" data-target="password">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="validation-message" id="password-error"></div>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Confirmer le mot de passe *</label>
                        <div class="input-wrapper">
                            <input type="password" id="confirm_password" name="confirm_password" class="form-control"
                                placeholder="Retapez votre mot de passe" minlength="6" required autocomplete="new-password">
                            <i class="fas fa-lock"></i>
                            <button type="button" class="password-toggle" data-target="confirm_password">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="validation-message" id="confirm-password-error"></div>
                    </div>

                    <p class="required-info">
                        * Champs obligatoires
                    </p>
                <?php else: ?>
                    <!-- Formulaire de connexion -->
                    <div class="form-group">
                        <label for="login_username">Nom d'utilisateur ou Email</label>
                        <div class="input-wrapper">
                            <input type="text" id="login_username" name="username" class="form-control"
                                placeholder="Votre nom d'utilisateur ou email" required autocomplete="username">
                            <i class="fas fa-user"></i>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="login_password">Mot de passe</label>
                        <div class="input-wrapper">
                            <input type="password" id="login_password" name="password" class="form-control"
                                placeholder="Votre mot de passe" required autocomplete="current-password">
                            <i class="fas fa-lock"></i>
                            <button type="button" class="password-toggle" data-target="login_password">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-group remember-group">
                        <label class="remember-label">
                            <input type="checkbox" name="remember">
                            Se souvenir de moi
                        </label>
                    </div>

                    <div class="forgot-password">
                        <a href="forgot-password.php">
                            Mot de passe oublié ?
                        </a>
                    </div>
                <?php endif; ?>

                <button type="submit" class="btn-submit" id="submitBtn">
                    <span><?php echo $isRegister ? 'S\'inscrire' : 'Se connecter'; ?></span>
                </button>
            </form>

            <div class="auth-links">
                <?php if ($isRegister): ?>
                    <p>
                        Déjà un compte ?
                        <a href="#" data-mode="login">Se connecter</a>
                    </p>
                <?php else: ?>
                    <p>
                        Pas encore de compte ?
                        <a href="#" data-mode="register">S'inscrire</a>
                    </p>
                <?php endif; ?>
                <p>
                    <a href="index.php"><i class="fas fa-home"></i> Retour à l'accueil</a>
                </p>
            </div>

            <div class="form-footer">
                <p>
                    En vous connectant, vous acceptez nos
                    <a href="terms.php">Conditions d'utilisation</a>
                    et notre
                    <a href="privacy.php">Politique de confidentialité</a>
                </p>
            </div>
        </div>
    </div>

    <script src="public/js/auth.js"></script>
</body>

</html>
