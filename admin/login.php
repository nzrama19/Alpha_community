<?php
require_once __DIR__ . '/php/login_logic.php';
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Admin - Système de Publications</title>

    <!-- Favicon Admin -->
    <link rel="apple-touch-icon" sizes="180x180" href="public/favicon_io/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="public/favicon_io/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="public/favicon_io/favicon-16x16.png">
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#00D4FF">

    <link rel="stylesheet" href="../public/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
        }

        .admin-login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #000000 0%, #001a1a 100%);
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        .admin-login-container::before {
            content: '';
            position: absolute;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(0, 212, 255, 0.1) 0%, transparent 70%);
            top: -200px;
            right: -200px;
            animation: pulse 4s ease-in-out infinite;
        }

        .admin-login-container::after {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(0, 212, 255, 0.08) 0%, transparent 70%);
            bottom: -150px;
            left: -150px;
            animation: pulse 5s ease-in-out infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
                opacity: 0.5;
            }

            50% {
                transform: scale(1.1);
                opacity: 0.8;
            }
        }

        .admin-login-box {
            background: rgba(0, 212, 255, 0.05);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(0, 212, 255, 0.3);
            border-radius: 20px;
            padding: 60px 50px;
            max-width: 480px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0, 212, 255, 0.2);
            position: relative;
            z-index: 10;
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .admin-login-box:hover {
            border-color: rgba(0, 212, 255, 0.5);
            box-shadow: 0 25px 70px rgba(0, 212, 255, 0.3);
        }

        .login-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .login-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #00D4FF 0%, #00C0E8 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 2.5rem;
            color: #000;
            box-shadow: 0 10px 30px rgba(0, 212, 255, 0.5);
            animation: iconBounce 2s ease-in-out infinite;
        }

        @keyframes iconBounce {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        .admin-login-box h1 {
            text-align: center;
            font-size: 2.5rem;
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            background: linear-gradient(135deg, #00D4FF 0%, #00C0E8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 10px;
            letter-spacing: 2px;
        }

        .admin-login-box .subtitle {
            text-align: center;
            color: rgba(255, 255, 255, 0.6);
            margin-bottom: 10px;
            font-size: 0.85rem;
            letter-spacing: 3px;
            text-transform: uppercase;
            font-weight: 300;
        }

        .divider {
            width: 60px;
            height: 3px;
            background: linear-gradient(90deg, transparent, #00D4FF, transparent);
            margin: 0 auto 30px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 10px;
            color: #00D4FF;
            font-weight: 600;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-group label i {
            font-size: 1rem;
        }

        .input-wrapper {
            position: relative;
        }

        .form-group input {
            width: 100%;
            padding: 15px 20px;
            background: rgba(0, 212, 255, 0.05);
            border: 2px solid rgba(0, 212, 255, 0.3);
            border-radius: 12px;
            color: white;
            font-size: 1rem;
            transition: all 0.3s ease;
            font-family: 'Poppins', sans-serif;
        }

        .form-group input::placeholder {
            color: rgba(255, 255, 255, 0.4);
        }

        .form-group input:focus {
            outline: none;
            border-color: #00D4FF;
            background: rgba(0, 212, 255, 0.1);
            box-shadow: 0 0 20px rgba(0, 212, 255, 0.3);
            transform: translateY(-2px);
        }

        .btn-login {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #00D4FF 0%, #00C0E8 100%);
            color: #000;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-top: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow: 0 10px 30px rgba(0, 212, 255, 0.4);
        }

        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(0, 212, 255, 0.6);
        }

        .btn-login:active {
            transform: translateY(-1px);
        }

        /* Loading state pour le bouton admin */
        .btn-login.loading {
            position: relative;
            color: transparent;
            pointer-events: none;
        }

        .btn-login.loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            top: 50%;
            left: 50%;
            margin-left: -10px;
            margin-top: -10px;
            border: 3px solid #000;
            border-radius: 50%;
            border-top-color: transparent;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .alert {
            margin-bottom: 25px;
            padding: 15px 20px;
            background: rgba(255, 107, 107, 0.1);
            border: 1px solid rgba(255, 107, 107, 0.5);
            border-radius: 12px;
            color: #FF6B6B;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: shake 0.5s ease;
        }

        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            25% {
                transform: translateX(-10px);
            }

            75% {
                transform: translateX(10px);
            }
        }

        .alert i {
            font-size: 1.2rem;
        }

        .footer-link {
            text-align: center;
            margin-top: 30px;
            padding-top: 25px;
            border-top: 1px solid rgba(0, 212, 255, 0.2);
        }

        .footer-link p {
            color: rgba(255, 255, 255, 0.5);
            font-size: 0.85rem;
            margin-bottom: 10px;
        }

        .footer-link a {
            color: #00D4FF;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .footer-link a:hover {
            color: white;
            transform: translateX(-5px);
        }

        @media (max-width: 576px) {
            .admin-login-box {
                padding: 40px 30px;
            }

            .admin-login-box h1 {
                font-size: 2rem;
            }

            .login-icon {
                width: 60px;
                height: 60px;
                font-size: 2rem;
            }
        }
    </style>
</head>

<body class="modern-layout">
    <div class="admin-login-container">
        <div class="admin-login-box">
            <div class="login-header">
                <div class="login-icon">
                    <i class="fas fa-shield-halved"></i>
                </div>
                <h1>ADMIN PANEL</h1>
                <p class="subtitle">Connexion Administrateur</p>
                <div class="divider"></div>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo escape($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label for="username">
                        <i class="fas fa-user"></i>
                        Nom d'utilisateur
                    </label>
                    <div class="input-wrapper">
                        <input
                            type="text"
                            id="username"
                            name="username"
                            placeholder="Entrez votre identifiant"
                            required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">
                        <i class="fas fa-lock"></i>
                        Mot de passe
                    </label>
                    <div class="input-wrapper">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Entrez votre mot de passe"
                            required>
                    </div>
                </div>

                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt"></i>
                    Se connecter
                </button>
            </form>

            <div class="footer-link">
                <p>Mot de passe oublié ?</p>
                <a href="../index.php">
                    <i class="fas fa-arrow-left"></i>
                    Retour au site
                </a>
            </div>
        </div>
    </div>

    <script>
        // Loading state pour le formulaire de connexion admin
        const adminLoginForm = document.querySelector('form');
        if (adminLoginForm) {
            adminLoginForm.addEventListener('submit', function(e) {
                const submitBtn = this.querySelector('.btn-login');
                if (submitBtn && !submitBtn.classList.contains('loading')) {
                    submitBtn.classList.add('loading');
                    submitBtn.disabled = true;
                }
            });
        }
    </script>
</body>

</html>
