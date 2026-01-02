// Admin Mobile Menu Toggle
const adminMenuToggle = document.getElementById('adminMenuToggle');
const adminNavLinks = document.getElementById('adminNavLinks');
const adminMenuOverlay = document.getElementById('adminMenuOverlay');

function closeAdminMenu() {
    adminMenuToggle.classList.remove('active');
    adminNavLinks.classList.remove('active');
    adminMenuOverlay.classList.remove('active');
    document.body.style.overflow = '';
}

function openAdminMenu() {
    adminMenuToggle.classList.add('active');
    adminNavLinks.classList.add('active');
    adminMenuOverlay.classList.add('active');
    document.body.style.overflow = 'hidden';
}

if (adminMenuToggle) {
    adminMenuToggle.addEventListener('click', function(e) {
        e.stopPropagation();
        if (this.classList.contains('active')) {
            closeAdminMenu();
        } else {
            openAdminMenu();
        }
    });

    // Fermer le menu lors du clic sur un lien
    const navLinkItems = adminNavLinks.querySelectorAll('a');
    navLinkItems.forEach(link => {
        link.addEventListener('click', closeAdminMenu);
    });

    // Fermer le menu lors du clic sur l'overlay
    adminMenuOverlay.addEventListener('click', closeAdminMenu);

    // Fermer le menu avec la touche Échap
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && adminMenuToggle.classList.contains('active')) {
            closeAdminMenu();
        }
    });
}

// Fonction de confirmation de déconnexion admin avec modal stylisé
function confirmAdminLogout(event) {
    event.preventDefault();
    document.getElementById('adminLogoutModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeAdminLogoutModal() {
    document.getElementById('adminLogoutModal').classList.remove('active');
    document.body.style.overflow = '';
}

function proceedAdminLogout() {
    window.location.href = 'logout.php';
}

// Fermer le modal en cliquant à l'extérieur
document.addEventListener('click', function(e) {
    const modal = document.getElementById('adminLogoutModal');
    if (e.target === modal) {
        closeAdminLogoutModal();
    }
});

// Fermer le modal avec Échap
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeAdminLogoutModal();
    }
});
