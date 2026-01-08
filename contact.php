<?php
require_once __DIR__ . '/php/contact_logic.php';
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - ALPHA COMMUNITY</title>

    <!-- Manifest et Icônes -->
    <link rel="manifest" href="manifest.json">
    <link rel="icon" type="image/x-icon" href="public/favicon_io/favicon.ico">
    <link rel="icon" type="image/png" sizes="32x32" href="public/favicon_io/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="public/favicon_io/favicon-16x16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="public/favicon_io/apple-touch-icon.png">
    <meta name="theme-color" content="#c87533">

    <link rel="stylesheet" href="public/css/navbar.css">
    <link rel="stylesheet" href="public/css/contact.css">
    <link rel="stylesheet" href="public/css/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>
    <?php include_once 'nav.php'; ?>

    <div class="container">
        <div class="page-header">
            <h1><i class="fas fa-envelope"></i> Contactez-nous</h1>
            <p>Nous sommes à votre écoute ! N'hésitez pas à nous faire part de vos questions ou suggestions.</p>
        </div>

        <div class="contact-wrapper">
            <div class="contact-info">
                <div class="info-card">
                    <div class="info-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <h3>Adresse</h3>
                    <p>123 Rue de la République<br>75001 Paris, France</p>
                </div>

                <div class="info-card">
                    <div class="info-icon">
                        <i class="fas fa-phone"></i>
                    </div>
                    <h3>Téléphone</h3>
                    <p>+33 1 23 45 67 89<br>Lun - Ven: 9h - 18h</p>
                </div>

                <div class="info-card">
                    <div class="info-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <h3>Email</h3>
                    <p>contact@monblog.fr<br>support@monblog.fr</p>
                </div>

                <div class="info-card">
                    <div class="info-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h3>Horaires</h3>
                    <p>Lundi - Vendredi: 9h - 18h<br>Samedi - Dimanche: Fermé</p>
                </div>
            </div>

            <div class="contact-form-wrapper">
                <?php if ($success_message): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
                    </div>
                <?php endif; ?>

                <?php if ($error_message): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>

                <form class="contact-form" method="POST" action="">
                    <div class="form-group">
                        <label for="name">
                            <i class="fas fa-user"></i> Nom complet *
                        </label>
                        <input type="text"
                            id="name"
                            name="name"
                            value="<?php echo escape($name ?? ''); ?>"
                            placeholder="Votre nom complet"
                            required>
                    </div>

                    <div class="form-group">
                        <label for="email">
                            <i class="fas fa-envelope"></i> Email *
                        </label>
                        <input type="email"
                            id="email"
                            name="email"
                            value="<?php echo escape($email ?? ''); ?>"
                            placeholder="votre@email.com"
                            required>
                    </div>

                    <div class="form-group">
                        <label for="subject">
                            <i class="fas fa-tag"></i> Sujet *
                        </label>
                        <input type="text"
                            id="subject"
                            name="subject"
                            value="<?php echo escape($subject ?? ''); ?>"
                            placeholder="Sujet de votre message"
                            required>
                    </div>

                    <div class="form-group">
                        <label for="message">
                            <i class="fas fa-comment-alt"></i> Message *
                        </label>
                        <textarea id="message"
                            name="message"
                            rows="6"
                            placeholder="Écrivez votre message ici..."
                            required><?php echo escape($message ?? ''); ?></textarea>
                    </div>

                    <button type="submit" class="btn-submit">
                        <i class="fas fa-paper-plane"></i> Envoyer le message
                    </button>
                </form>

                <div class="contact-faq">
                    <h3><i class="fas fa-question-circle"></i> Questions fréquentes</h3>
                    <div class="faq-item">
                        <h4>Combien de temps pour une réponse ?</h4>
                        <p>Nous répondons généralement sous 24 à 48 heures ouvrées.</p>
                    </div>
                    <div class="faq-item">
                        <h4>Comment créer un compte ?</h4>
                        <p>Cliquez sur "Inscription" dans le menu et remplissez le formulaire.</p>
                    </div>
                    <div class="faq-item">
                        <h4>Comment publier un article ?</h4>
                        <p>Seuls les administrateurs peuvent publier des articles. Contactez-nous pour plus d'infos.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <?php include_once 'footer.php'; ?>

    <script>
        window.BASE_URL = '<?php echo rtrim(BASE_URL, "/"); ?>';
    </script>
    <script src="public/js/main.js"></script>
</body>

</html>
