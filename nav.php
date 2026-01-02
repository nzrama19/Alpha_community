<nav class="modern-nav">
    <div class="nav-container">
        <a href="index.php" class="logo">
            <img src="public/images/2.jpg" alt="Alpha Community Logo">
            <h1><strong>Alpha</strong> <br>Community</h1>
        </a>

        <button class="menu-toggle" id="menuToggle">
            <span></span>
            <span></span>
            <span></span>
        </button>

        <div class="nav-menu" id="navMenu">
            <a href="index.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : ''; ?>">
                <i class="fas fa-home"></i> Accueil
            </a>
            <a href="categories.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'categories.php' ? 'active' : ''; ?>">
                <i class="fas fa-th"></i> Catégories
            </a>
            <a href="about.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'about.php' ? 'active' : ''; ?>">
                <i class="fas fa-info-circle"></i> À propos
            </a>
            <a href="contact.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'contact.php' ? 'active' : ''; ?>">
                <i class="fas fa-envelope"></i> Contact
            </a>
        </div>

        <div class="nav-links" id="navLinks">
            <!-- Toggle Thème Clair/Sombre -->
            <button class="theme-toggle" id="themeToggle" aria-label="Changer le thème">
                <i class="fas fa-moon theme-toggle-icon" id="themeIcon"></i>
            </button>

            <?php if (isUserLoggedIn()): ?>
                <div class="user-dropdown">
                    <div class="user-dropdown-trigger" id="userDropdownTrigger">
                        <i class="fas fa-user-circle"></i>
                        <span><?php echo escape($_SESSION['username']); ?></span>
                        <i class="fas fa-chevron-down dropdown-arrow"></i>
                    </div>
                    <div class="user-dropdown-menu" id="userDropdownMenu">
                        <a href="my-posts.php" class="dropdown-menu-item">
                            <i class="fas fa-file-alt"></i>
                            <span>Mes Posts</span>
                        </a>
                        <a href="profile.php" class="dropdown-menu-item">
                            <i class="fas fa-user-circle"></i>
                            <span>Profil</span>
                        </a>
                        <a href="#" class="dropdown-menu-item" onclick="confirmLogout(event, 'logout.php')">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Déconnexion</span>
                        </a>
                    </div>
                </div>
            <?php elseif (isAdminLoggedIn()): ?>
                <div class="user-dropdown">
                    <div class="user-dropdown-trigger" id="userDropdownTrigger">
                        <i class="fas fa-crown"></i>
                        <span>Admin: <?php echo escape($_SESSION['username']); ?></span>
                        <i class="fas fa-chevron-down dropdown-arrow"></i>
                    </div>
                    <div class="user-dropdown-menu" id="userDropdownMenu">
                        <a href="admin/dashboard.php" class="dropdown-menu-item">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                        <a href="#" class="dropdown-menu-item" onclick="confirmLogout(event, 'admin/logout.php')">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Déconnexion</span>
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <a href="register.php" class="nav-link-register" aria-label="Inscription">
                    <i class="fas fa-user-plus"></i><span class="auth-text"> Inscription</span>
                </a>
                <a href="login.php" class="nav-link-login" aria-label="Connexion">
                    <i class="fas fa-sign-in-alt"></i><span class="auth-text"> Connexion</span>
                </a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<link rel="stylesheet" href="public/css/navbar_backup.css">
<link rel="stylesheet" href="public/css/theme.css">
<script src="public/js/nav.js"></script>

<!-- Modal de confirmation de déconnexion -->
<div id="logoutModal" class="logout-modal">
    <div class="logout-modal-content">
        <div class="logout-modal-icon">
            <i class="fas fa-sign-out-alt"></i>
        </div>
        <h3 class="logout-modal-title">Déconnexion</h3>
        <p class="logout-modal-text">Êtes-vous sûr de vouloir vous déconnecter ?</p>
        <div class="logout-modal-buttons">
            <button class="logout-btn logout-btn-cancel" onclick="closeLogoutModal()">
                <i class="fas fa-times"></i> Annuler
            </button>
            <button class="logout-btn logout-btn-confirm" onclick="proceedLogout()">
                <i class="fas fa-check"></i> Confirmer
            </button>
        </div>
    </div>
</div>

<style>
    /* Modal de déconnexion */
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
        max-width: 400px;
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
