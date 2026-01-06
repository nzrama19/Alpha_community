<?php
require_once __DIR__ . '/php/manage_users_logic.php';
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Utilisateurs - Admin</title>

    <!-- Favicon Admin -->
    <link rel="apple-touch-icon" sizes="180x180" href="public/favicon_io/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="public/favicon_io/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="public/favicon_io/favicon-16x16.png">
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#00D4FF">

    <link rel="stylesheet" href="public/css/user.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

</head>

<body class="modern-layout admin-page">
    <?php include 'nav.php'; ?>

    <div class="admin-container">
        <a href="dashboard.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Retour au Dashboard
        </a>

        <div class="page-header">
            <h1><i class="fas fa-users"></i> Gestion des Utilisateurs</h1>
            <div class="stat-card" style="margin: 0;">
                <div class="stat-number"><?php echo $total_users; ?></div>
                <div class="stat-label">Utilisateurs total</div>
            </div>
        </div>

        <?php echo $message; ?>

        <div class="users-container">
            <?php if (empty($users)): ?>
                <div class="no-data">
                    <i class="fas fa-inbox"></i>
                    <p>Aucun utilisateur trouvé</p>
                </div>
            <?php else: ?>
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>Utilisateur</th>
                            <th>Email</th>
                            <th>Commentaires</th>
                            <th>Likes</th>
                            <th>Inscrit le</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $u): ?>
                            <tr>
                                <td>
                                    <div class="user-info">
                                        <?php if (!empty($u['avatar'])): ?>
                                            <img src="<?php echo UPLOAD_URL . htmlspecialchars($u['avatar']); ?>" alt="<?php echo htmlspecialchars($u['username']); ?>" class="user-avatar">
                                        <?php else: ?>
                                            <div class="user-avatar">
                                                <?php echo strtoupper(substr($u['username'], 0, 1)); ?>
                                            </div>
                                        <?php endif; ?>
                                        <div class="user-details">
                                            <h4><?php echo htmlspecialchars($u['username']); ?></h4>
                                            <small>ID: <?php echo $u['id']; ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($u['email']); ?></td>
                                <td>
                                    <span class="stat-badge comments">
                                        <i class="fas fa-comments"></i> <?php echo $u['comment_count']; ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="stat-badge likes">
                                        <i class="fas fa-heart"></i> <?php echo $u['like_count']; ?>
                                    </span>
                                </td>
                                <td><?php echo date('d/m/Y H:i', strtotime($u['created_at'])); ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="user-details.php?id=<?php echo $u['id']; ?>" class="btn-small btn-view">
                                            <i class="fas fa-eye"></i> Voir
                                        </a>
                                        <?php if ($u['id'] != $_SESSION['admin_id']): ?>
                                            <button class="btn-small btn-delete" onclick="showDeleteModal(<?php echo $u['id']; ?>, '<?php echo htmlspecialchars($u['username']); ?>')">
                                                <i class="fas fa-trash"></i> Supprimer
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <?php if ($total_pages > 1): ?>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="?page=1">Première</a>
                            <a href="?page=<?php echo $page - 1; ?>">Précédente</a>
                        <?php else: ?>
                            <span>Première</span>
                            <span>Précédente</span>
                        <?php endif; ?>

                        <?php
                        $start = max(1, $page - 2);
                        $end = min($total_pages, $page + 2);

                        if ($start > 1) echo '<span>...</span>';

                        for ($i = $start; $i <= $end; $i++):
                            if ($i == $page):
                                echo '<span class="active">' . $i . '</span>';
                            else:
                                echo '<a href="?page=' . $i . '">' . $i . '</a>';
                            endif;
                        endfor;

                        if ($end < $total_pages) echo '<span>...</span>';
                        ?>

                        <?php if ($page < $total_pages): ?>
                            <a href="?page=<?php echo $page + 1; ?>">Suivante</a>
                            <a href="?page=<?php echo $total_pages; ?>">Dernière</a>
                        <?php else: ?>
                            <span>Suivante</span>
                            <span>Dernière</span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal de suppression -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Supprimer l'utilisateur</h2>
                <button class="modal-close" onclick="closeDeleteModal()">×</button>
            </div>
            <div class="user-details-info">
                <p><strong>Êtes-vous sûr de vouloir supprimer cet utilisateur ?</strong></p>
                <p id="deleteUsername"></p>
                <p style="color: #dc3545; margin-top: 15px;">
                    <i class="fas fa-exclamation-triangle"></i>
                    Cette action supprimera également tous ses commentaires et likes.
                </p>
            </div>
            <form method="POST" class="button-group">
                <input type="hidden" name="user_id" id="deleteUserId">
                <button type="button" class="btn-small btn-cancel" onclick="closeDeleteModal()">Annuler</button>
                <button type="submit" name="delete_user" class="btn-small btn-confirm">Confirmer la suppression</button>
            </form>
        </div>
    </div>

    <script src="public/js/admin-manage-users.js"></script>
</body>

</html>
