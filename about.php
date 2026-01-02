<?php
require_once __DIR__ . '/php/about_logic.php';
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Découvrez l'histoire et la mission de ALPHA COMMUNITY - Une plateforme dédiée au partage d'idées et d'expériences.">
    <title>À propos - ALPHA COMMUNITY</title>

    <!-- Manifest et Icônes -->
    <link rel="manifest" href="manifest.json">
    <link rel="icon" type="image/x-icon" href="public/favicon_io/favicon.ico">
    <link rel="icon" type="image/png" sizes="32x32" href="public/favicon_io/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="public/favicon_io/favicon-16x16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="public/favicon_io/apple-touch-icon.png">
    <meta name="theme-color" content="#c87533">

    <link rel="stylesheet" href="public/css/navbar.css">
    <link rel="stylesheet" href="public/css/about.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>
    <?php include_once 'nav.php'; ?>

    <div class="container">
        <div class="page-header">
            <h1><i class="fas fa-info-circle"></i> À propos de nous</h1>
            <p>Découvrez notre mission et notre vision</p>
        </div>

        <div class="about-content">
            <div class="about-section">
                <div class="about-text">
                    <h2><i class="fas fa-bullseye"></i> Notre Mission</h2>
                    <p>ALPHA COMMUNITY est une plateforme innovante dédiée au partage d'idées, d'expériences et de connaissances. Notre mission est de créer un espace où les utilisateurs publient du contenu de qualité et où les visiteurs peuvent interagir librement.</p>
                    <p>Nous croyons en la puissance de l'interaction communautaire. C'est pourquoi nous avons créé un système permettant à nos visiteurs de poster liker les publications et de partager leurs opinions dans les commentaires.</p>
                </div>
                <div class="about-image">
                    <i class="fas fa-users-cog about-icon"></i>
                </div>
            </div>

            <div class="features-grid">
                <div class="feature-box">
                    <div class="feature-icon">
                        <i class="fas fa-heart"></i>
                    </div>
                    <h3>Publications</h3>
                    <p>Partager avec les autres les choses qui vous plaisent</p>
                </div>
                <div class="feature-box">
                    <div class="feature-icon">
                        <i class="fas fa-heart"></i>
                    </div>
                    <h3>Likes</h3>
                    <p>Aimez les publications qui vous plaisent</p>
                </div>

                <div class="feature-box">
                    <div class="feature-icon">
                        <i class="fas fa-comments"></i>
                    </div>
                    <h3>Commentaires</h3>
                    <p>Partagez votre avis et vos idées sur les publications des autres utilisateurs</p>
                </div>
            </div>

            <div class="stats-section">
                <h2><i class="fas fa-chart-line"></i> En quelques chiffres</h2>
                <div class="stats-grid">
                    <div class="stat-card">
                        <i class="fas fa-users"></i>
                        <h3>500+</h3>
                        <p>Visiteurs actifs</p>
                    </div>
                    <div class="stat-card">
                        <i class="fas fa-file-alt"></i>
                        <h3>1,000+</h3>
                        <p>Articles publiés</p>
                    </div>
                    <div class="stat-card">
                        <i class="fas fa-comments"></i>
                        <h3>5,000+</h3>
                        <p>Commentaires postés</p>
                    </div>
                    <div class="stat-card">
                        <i class="fas fa-heart"></i>
                        <h3>10,000+</h3>
                        <p>Likes donnés</p>
                    </div>
                </div>
            </div>

            <div class="team-section">
                <h2><i class="fas fa-users"></i> Notre Équipe</h2>
                <div class="team-grid">
                    <div class="team-member">
                        <div class="member-avatar">
                            <i class="fas fa-user-tie"></i>
                            <img src="public/images/my_Profile.jpg" alt="profile">
                        </div>
                        <h4>N'da Mondesir</h4>
                        <p>Fondateur & CEO</p>
                        <div class="member-social">
                            <a href="#"><i class="fab fa-linkedin"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="cta-section">
                <h2>Rejoignez notre communauté !</h2>
                <p>Créez votre compte gratuitement pour commenter et liker les publications.</p>
                <a href="register.php" class="btn-cta">
                    <i class="fas fa-user-plus"></i> S'inscrire maintenant
                </a>
            </div>
        </div>
    </div>

    <?php include_once 'footer.php'; ?>

    <script src="public/js/main.js"></script>
</body>

</html>
