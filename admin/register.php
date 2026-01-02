<?php
require_once __DIR__ . '/php/register_logic.php';
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription Admin - Système de Publications</title>

    <!-- Favicon Admin -->
    <link rel="apple-touch-icon" sizes="180x180" href="public/admin_favicon_io/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="public/admin_favicon_io/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="public/admin_favicon_io/favicon-16x16.png">
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#00D4FF">

    <link rel="stylesheet" href="../public/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .admin-register-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #000000 0%, #001a1a 100%);
            padding: 20px;
        }

        .admin-register-box {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            padding: 40px;
            width: 100%;
            max-width: 450px;
        }

        .admin-register-box h1 {
            color: #0f2027;
            font-size: 28px;
            margin-bottom: 10px;
            text-align: center;
            font-family: 'Playfair Display', serif;
        }

        .admin-register-box p {
            color: #666;
            text-align: center;
            margin-bottom: 30px;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            color: #333;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s;
            box-sizing: border-box;
        }

        .form-group input:focus {
            outline: none;
            border-color: #ff6b6b;
        }

        .btn-register {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(255, 107, 107, 0.4);
        }

        .alert {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-error {
            background-color: #ffebee;
            color: #c62828;
            border-left: 4px solid #c62828;
        }

        .alert-success {
            background-color: #e8f5e9;
            color: #2e7d32;
            border-left: 4px solid #2e7d32;
        }

        .alert-success a {
            color: #2e7d32;
            text-decoration: underline;
            font-weight: 600;
        }

        .login-link {
            text-align: center;
            margin-top: 20px;
            color: #666;
            font-size: 14px;
        }

        .login-link a {
            color: #ff6b6b;
            text-decoration: none;
            font-weight: 600;
        }

        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="admin-register-container">
        <div class="admin-register-box">
            <h1><i class="fas fa-shield-alt"></i> Inscription Admin</h1>
            <p>Créer un nouveau compte administrateur</p>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    <strong><i class="fas fa-exclamation-circle"></i></strong> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="username"><i class="fas fa-user"></i> Nom d'utilisateur</label>
                    <input type="text" id="username" name="username" required placeholder="Entrez votre nom d'utilisateur" value="<?php echo htmlspecialchars($username ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="email"><i class="fas fa-envelope"></i> Email</label>
                    <input type="email" id="email" name="email" required placeholder="Entrez votre email" value="<?php echo htmlspecialchars($email ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="password"><i class="fas fa-lock"></i> Mot de passe</label>
                    <input type="password" id="password" name="password" required placeholder="Entrez votre mot de passe" minlength="8">
                    <small style="color: #666; font-size: 12px; display: block; margin-top: 5px;">
                        <i class="fas fa-info-circle"></i> Au moins 8 caractères, 1 majuscule, 1 minuscule, 1 chiffre et 1 caractère spécial
                    </small>
                    <div id="passwordRequirements" style="display: none; margin-top: 10px; font-size: 12px;">
                        <div id="req-length" style="color: #c62828;"><i class="fas fa-times"></i> 8 caractères minimum</div>
                        <div id="req-uppercase" style="color: #c62828;"><i class="fas fa-times"></i> Une majuscule</div>
                        <div id="req-lowercase" style="color: #c62828;"><i class="fas fa-times"></i> Une minuscule</div>
                        <div id="req-number" style="color: #c62828;"><i class="fas fa-times"></i> Un chiffre</div>
                        <div id="req-special" style="color: #c62828;"><i class="fas fa-times"></i> Un caractère spécial</div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="confirm_password"><i class="fas fa-lock"></i> Confirmer le mot de passe</label>
                    <input type="password" id="confirm_password" name="confirm_password" required placeholder="Confirmez votre mot de passe">
                </div>

                <button type="submit" class="btn-register">
                    <i class="fas fa-user-plus"></i> S'inscrire
                </button>
            </form>

            <div class="login-link">
                Vous avez déjà un compte ? <a href="login.php">Se connecter</a>
            </div>
        </div>
    </div>

    <script src="public/js/admin-register.js"></script>
</body>

</html>
