<?php
require_once __DIR__ . '/php/statistics_logic.php';
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques Admin - Système de Publications</title>

    <!-- Favicon Admin -->
    <link rel="apple-touch-icon" sizes="180x180" href="public/favicon_io/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="public/n_io/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="public/favicon_io/favicon-16x16.png">
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#00D4FF">

    <link rel="stylesheet" href="public/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .modern-nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            background: rgba(0, 0, 0, 0.95);
            backdrop-filter: blur(10px);
            padding: 20px 0;
            border-bottom: 1px solid rgba(0, 212, 255, 0.2);
        }

        .nav-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo h1 {
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: 2px;
            color: white;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo h1 i {
            color: #00D4FF;
        }

        .nav-links {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            padding: 8px 20px;
            border: 1px solid rgba(0, 212, 255, 0.3);
            border-radius: 5px;
            transition: all 0.3s ease;
            font-size: 0.85rem;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .nav-links a:hover {
            background: #00D4FF;
            border-color: #00D4FF;
            color: #000;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 212, 255, 0.4);
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #000000 0%, #001a1a 100%);
            min-height: 100vh;
            color: white;
            margin: 0;
            padding: 0;
        }

        .admin-page {
            background: transparent;
            min-height: 100vh;
            padding: 100px 0 30px 0;
        }

        .admin-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: rgba(0, 212, 255, 0.05);
            border: 1px solid rgba(0, 212, 255, 0.2);
            padding: 25px;
            border-radius: 15px;
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 15px rgba(0, 212, 255, 0.1);
            text-align: center;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            border-color: rgba(0, 212, 255, 0.5);
            box-shadow: 0 10px 30px rgba(0, 212, 255, 0.3);
        }

        .stat-card i {
            font-size: 32px;
            color: #00D4FF;
            margin-bottom: 15px;
        }

        .stat-number {
            font-size: 36px;
            font-weight: bold;
            color: white;
            margin-bottom: 5px;
        }

        .stat-label {
            color: rgba(255, 255, 255, 0.6);
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .section {
            background: rgba(0, 212, 255, 0.05);
            border: 1px solid rgba(0, 212, 255, 0.2);
            padding: 30px;
            border-radius: 15px;
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 15px rgba(0, 212, 255, 0.1);
            margin-bottom: 30px;
            transition: all 0.3s ease;
        }

        .section:hover {
            border-color: rgba(0, 212, 255, 0.5);
            box-shadow: 0 10px 30px rgba(0, 212, 255, 0.2);
        }

        .section-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #00D4FF;
        }

        .section-header h2 {
            margin: 0;
            color: white;
            font-size: 20px;
        }

        .section-header i {
            color: #00D4FF;
            font-size: 24px;
        }

        .list-item {
            padding: 15px;
            border: 1px solid rgba(0, 212, 255, 0.1);
            background: rgba(0, 212, 255, 0.02);
            border-radius: 8px;
            margin-bottom: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            transition: all 0.3s ease;
        }

        .list-item:hover {
            background: rgba(0, 212, 255, 0.1);
            border-color: #00D4FF;
            transform: translateX(5px);
        }

        .item-info {
            flex: 1;
        }

        .item-info h4 {
            margin: 0 0 5px 0;
            color: white;
        }

        .item-info small {
            color: rgba(255, 255, 255, 0.6);
        }

        .item-stats {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
        }

        .badge {
            background: #f0f0f0;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            color: #666;
        }

        .badge.comments {
            background: #e3f2fd;
            color: #1976d2;
        }

        .badge.likes {
            background: #fce4ec;
            color: #d81b60;
        }

        .badge.activity {
            background: #f3e5f5;
            color: #7b1fa2;
        }

        .two-column {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .list-item {
                flex-direction: column;
                align-items: flex-start;
            }

            .item-stats {
                justify-content: flex-start;
                width: 100%;
            }

            .two-column {
                grid-template-columns: 1fr;
            }
        }

        .page-header {
            color: white;
            margin-bottom: 30px;
        }

        .page-header h1 {
            font-size: 32px;
            margin: 0 0 10px 0;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .page-header p {
            margin: 0;
            opacity: 0.9;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: white;
            text-decoration: none;
            margin-bottom: 20px;
            padding: 10px 20px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .back-link:hover {
            background: rgba(255, 255, 255, 0.3);
        }
    </style>
</head>

<body class="modern-layout admin-page">
    <!-- Navigation moderne -->
    <nav class="modern-nav">
        <div class="nav-container">
            <div class="logo">
                <h1><i class="fas fa-user-shield"></i> ADMIN PANEL</h1>
            </div>
            <div class="nav-links">
                <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
                <a href="manage-users.php"><i class="fas fa-users"></i> Utilisateurs</a>
                <a href="edit-profile.php"><i class="fas fa-user-cog"></i> Mon Profil</a>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
            </div>
        </div>
    </nav>

    <div class="admin-container">
        <a href="dashboard.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Retour au tableau de bord principal
        </a>

        <div class="page-header">
            <h1><i class="fas fa-chart-line"></i> Statistiques Globales</h1>
            <p>Vue d'ensemble complète de votre plateforme</p>
        </div>

        <!-- Statistiques principales -->
        <div class="stats-grid">
            <div class="stat-card">
                <i class="fas fa-users"></i>
                <div class="stat-number"><?php echo $global_stats['total_users']; ?></div>
                <div class="stat-label">Utilisateurs Total</div>
            </div>

            <div class="stat-card">
                <i class="fas fa-file-alt"></i>
                <div class="stat-number"><?php echo $global_stats['total_posts']; ?></div>
                <div class="stat-label">Publications</div>
            </div>

            <div class="stat-card">
                <i class="fas fa-comments"></i>
                <div class="stat-number"><?php echo $global_stats['total_comments']; ?></div>
                <div class="stat-label">Commentaires</div>
            </div>

            <div class="stat-card">
                <i class="fas fa-heart"></i>
                <div class="stat-number"><?php echo $global_stats['total_likes']; ?></div>
                <div class="stat-label">Likes</div>
            </div>

            <div class="stat-card">
                <i class="fas fa-calendar-check"></i>
                <div class="stat-number"><?php echo $global_stats['users_this_month']; ?></div>
                <div class="stat-label">Utilisateurs ce Mois</div>
            </div>

            <div class="stat-card">
                <i class="fas fa-clock"></i>
                <div class="stat-number"><?php echo $global_stats['posts_this_month']; ?></div>
                <div class="stat-label">Publications ce Mois</div>
            </div>
        </div>

        <div class="two-column">
            <!-- Utilisateurs les plus actifs -->
            <div class="section">
                <div class="section-header">
                    <i class="fas fa-fire"></i>
                    <h2>Utilisateurs les Plus Actifs</h2>
                </div>

                <?php if (!empty($global_stats['top_users'])): ?>
                    <?php foreach ($global_stats['top_users'] as $index => $user): ?>
                        <div class="list-item">
                            <div class="item-info">
                                <h4><?php echo htmlspecialchars($user['username']); ?></h4>
                                <small>Rang #<?php echo $index + 1; ?></small>
                            </div>
                            <div class="item-stats">
                                <span class="badge comments">
                                    <i class="fas fa-comments"></i> <?php echo $user['comment_count']; ?>
                                </span>
                                <span class="badge likes">
                                    <i class="fas fa-heart"></i> <?php echo $user['like_count']; ?>
                                </span>
                                <a href="user-details.php?id=<?php echo $user['id']; ?>" style="text-decoration: none; color: #00D4FF;">
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="text-align: center; color: #999; padding: 20px;">Aucune activité disponible</p>
                <?php endif; ?>
            </div>

            <!-- Publications les plus commentées -->
            <div class="section">
                <div class="section-header">
                    <i class="fas fa-comments"></i>
                    <h2>Publications les Plus Commentées</h2>
                </div>

                <?php if (!empty($global_stats['most_commented_posts'])): ?>
                    <?php foreach ($global_stats['most_commented_posts'] as $post): ?>
                        <div class="list-item">
                            <div class="item-info">
                                <h4><?php echo htmlspecialchars(substr($post['content'], 0, 50)); ?>...</h4>
                                <small>Par <?php echo htmlspecialchars($post['username']); ?></small>
                            </div>
                            <div class="item-stats">
                                <span class="badge activity">
                                    <i class="fas fa-comments"></i> <?php echo $post['comment_count']; ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="text-align: center; color: #999; padding: 20px;">Aucune publication</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="two-column">
            <!-- Publications les plus likées -->
            <div class="section">
                <div class="section-header">
                    <i class="fas fa-heart"></i>
                    <h2>Publications les Plus Likées</h2>
                </div>

                <?php if (!empty($global_stats['most_liked_posts'])): ?>
                    <?php foreach ($global_stats['most_liked_posts'] as $post): ?>
                        <div class="list-item">
                            <div class="item-info">
                                <h4><?php echo htmlspecialchars(substr($post['content'], 0, 50)); ?>...</h4>
                                <small>Par <?php echo htmlspecialchars($post['username']); ?></small>
                            </div>
                            <div class="item-stats">
                                <span class="badge likes">
                                    <i class="fas fa-heart"></i> <?php echo $post['like_count']; ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="text-align: center; color: #999; padding: 20px;">Aucune publication</p>
                <?php endif; ?>
            </div>

            <!-- Utilisateurs récemment inscrits -->
            <div class="section">
                <div class="section-header">
                    <i class="fas fa-user-plus"></i>
                    <h2>Utilisateurs Récents</h2>
                </div>

                <?php if (!empty($recent_users)): ?>
                    <?php foreach ($recent_users as $user): ?>
                        <div class="list-item">
                            <div class="item-info">
                                <h4><?php echo htmlspecialchars($user['username']); ?></h4>
                                <small><?php echo htmlspecialchars($user['email']); ?></small>
                            </div>
                            <div class="item-stats">
                                <small style="color: #999;">
                                    <i class="fas fa-clock"></i> <?php echo date('d/m/Y', strtotime($user['created_at'])); ?>
                                </small>
                                <a href="user-details.php?id=<?php echo $user['id']; ?>" style="text-decoration: none; color: #00D4FF;">
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="text-align: center; color: #999; padding: 20px;">Aucun utilisateur</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Publications récentes -->
        <div class="section">
            <div class="section-header">
                <i class="fas fa-newspaper"></i>
                <h2>Publications Récentes</h2>
            </div>

            <?php if (!empty($recent_posts)): ?>
                <?php foreach ($recent_posts as $post): ?>
                    <div class="list-item">
                        <div class="item-info">
                            <h4><?php echo htmlspecialchars(substr($post['content'], 0, 80)); ?>...</h4>
                            <small>Par <?php echo htmlspecialchars($post['username']); ?> · <?php echo date('d/m/Y H:i', strtotime($post['created_at'])); ?></small>
                        </div>
                        <div class="item-stats">
                            <span class="badge comments">
                                <i class="fas fa-comments"></i> <?php echo $post['comment_count']; ?>
                            </span>
                            <span class="badge likes">
                                <i class="fas fa-heart"></i> <?php echo $post['like_count']; ?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="text-align: center; color: #999; padding: 20px;">Aucune publication</p>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>
