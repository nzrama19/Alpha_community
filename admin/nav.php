<nav class="modern-nav">
    <div class="nav-container">
        <div class="logo">
            <h1><i class="fas fa-user-shield"></i> ADMIN PANEL</h1>
        </div>

        <!-- Toggle Thème Clair/Sombre -->
        <button class="theme-toggle" id="themeToggle" aria-label="Changer le thème">
            <i class="fas fa-moon theme-toggle-icon" id="themeIcon"></i>
        </button>

        <button class="menu-toggle" id="adminMenuToggle" aria-label="Toggle navigation">
            <span></span>
            <span></span>
            <span></span>
        </button>

        <div class="nav-links" id="adminNavLinks">

            <a href="dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : ''; ?>">
                <i class="fas fa-home"></i> Dashboard
            </a>
            <a href="moderation.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'moderation.php' ? 'active' : ''; ?>">
                <i class="fas fa-shield-alt"></i> Modération
                <?php
                // Afficher le badge si des publications sont en attente
                require_once __DIR__ . '/../includes/Post.php';
                $postNav = new Post();
                $pendingNav = $postNav->getPendingCount();
                if ($pendingNav > 0): ?>
                    <span class="nav-badge"><?php echo $pendingNav; ?></span>
                <?php endif; ?>
            </a>
            <a href="manage-users.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'manage-users.php' ? 'active' : ''; ?>">
                <i class="fas fa-users"></i> Utilisateurs
            </a>
            <a href="edit-profile.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'edit-profile.php' ? 'active' : ''; ?>">
                <i class="fas fa-user-cog"></i> Mon Profil
            </a>
            <a href="#" onclick="confirmAdminLogout(event)">
                <i class="fas fa-sign-out-alt"></i> Déconnexion
            </a>
        </div>
    </div>
</nav>

<div class="menu-overlay" id="adminMenuOverlay"></div>

<link rel="stylesheet" href="public/css/nav.css">
<link rel="stylesheet" href="public/css/theme.css">

<script src="public/js/admin-nav.js"></script>

<!-- Modal de confirmation de déconnexion admin -->
<div id="adminLogoutModal" class="logout-modal">
    <div class="logout-modal-content">
        <div class="logout-modal-icon admin-icon">
            <i class="fas fa-user-shield"></i>
        </div>
        <h3 class="logout-modal-title">Déconnexion Admin</h3>
        <p class="logout-modal-text">Êtes-vous sûr de vouloir vous déconnecter du panel administrateur ?</p>
        <div class="logout-modal-buttons">
            <button class="logout-btn logout-btn-cancel" onclick="closeAdminLogoutModal()">
                <i class="fas fa-times"></i> Annuler
            </button>
            <button class="logout-btn logout-btn-confirm" onclick="proceedAdminLogout()">
                <i class="fas fa-sign-out-alt"></i> Déconnexion
            </button>
        </div>
    </div>
</div>

<style>
    /* Modal de déconnexion admin */
    .logout-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.7);
        backdrop-filter: blur(5px);
        z-index: 10000;
        justify-content: center;
        align-items: center;
        animation: fadeIn 0.3s ease;
    }

    .logout-modal.active {
        display: flex;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: scale(0.8) translateY(-20px);
        }

        to {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }

    .logout-modal-content {
        background: linear-gradient(145deg, #1a1a2e, #16213e);
        border: 1px solid rgba(0, 212, 255, 0.3);
        border-radius: 20px;
        padding: 40px;
        max-width: 420px;
        width: 90%;
        text-align: center;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5),
            0 0 40px rgba(0, 212, 255, 0.1);
        animation: slideIn 0.3s ease;
    }

    .logout-modal-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #ff6b6b, #ee5a5a);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 25px;
        box-shadow: 0 10px 30px rgba(255, 107, 107, 0.3);
    }

    .logout-modal-icon.admin-icon {
        background: linear-gradient(135deg, #00d4ff, #0099cc);
        box-shadow: 0 10px 30px rgba(0, 212, 255, 0.3);
    }

    .logout-modal-icon i {
        font-size: 35px;
        color: white;
    }

    .logout-modal-title {
        color: #fff;
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 15px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .logout-modal-text {
        color: #a0a0a0;
        font-size: 16px;
        margin-bottom: 30px;
        line-height: 1.6;
    }

    .logout-modal-buttons {
        display: flex;
        gap: 15px;
        justify-content: center;
    }

    .logout-btn {
        padding: 14px 28px;
        border: none;
        border-radius: 12px;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .logout-btn-cancel {
        background: linear-gradient(135deg, #3a3a5c, #2d2d4a);
        color: #fff;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .logout-btn-cancel:hover {
        background: linear-gradient(135deg, #4a4a6c, #3d3d5a);
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
    }

    .logout-btn-confirm {
        background: linear-gradient(135deg, #ff6b6b, #ee5a5a);
        color: #fff;
        box-shadow: 0 5px 20px rgba(255, 107, 107, 0.3);
    }

    .logout-btn-confirm:hover {
        background: linear-gradient(135deg, #ff5252, #e04848);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(255, 107, 107, 0.4);
    }

    /* Responsive */
    @media (max-width: 480px) {
        .logout-modal-content {
            padding: 30px 20px;
        }

        .logout-modal-buttons {
            flex-direction: column;
        }

        .logout-btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<!-- Script pour la gestion du thème -->
<script src="public/js/theme.js"></script>
