// Mobile Menu Toggle
const menuToggle = document.getElementById('menuToggle');
const navMenu = document.getElementById('navMenu');
const navLinks = document.getElementById('navLinks');
const modernNav = document.querySelector('.modern-nav');

if (menuToggle) {
    menuToggle.addEventListener('click', function() {
        this.classList.toggle('active');
        navMenu.classList.toggle('active');
        navLinks.classList.toggle('active');
    });
}

// User Dropdown Menu (Desktop & Mobile)
const userDropdownTrigger = document.getElementById('userDropdownTrigger');
const userDropdownMenu = document.getElementById('userDropdownMenu');

if (userDropdownTrigger && userDropdownMenu) {
    // Toggle dropdown on trigger click
    userDropdownTrigger.addEventListener('click', function(e) {
        e.stopPropagation();
        userDropdownTrigger.classList.toggle('active');
        userDropdownMenu.classList.toggle('active');
    });

    // Close dropdown when clicking on a menu item
    document.querySelectorAll('.dropdown-menu-item').forEach(item => {
        item.addEventListener('click', function() {
            // Don't close on mobile if we're still in the menu
            const isMobile = window.innerWidth < 768;
            if (!isMobile) {
                userDropdownTrigger.classList.remove('active');
                userDropdownMenu.classList.remove('active');
            }
        });
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.user-dropdown')) {
            userDropdownTrigger.classList.remove('active');
            userDropdownMenu.classList.remove('active');
        }
    });

    // Close dropdown on window resize (when switching screens)
    window.addEventListener('resize', function() {
        userDropdownTrigger.classList.remove('active');
        userDropdownMenu.classList.remove('active');
    });
}

// Close menu when clicking on a link
document.querySelectorAll('.nav-link, .nav-links a').forEach(link => {
    link.addEventListener('click', function() {
        menuToggle?.classList.remove('active');
        navMenu?.classList.remove('active');
        navLinks?.classList.remove('active');
    });
});

// Navbar scroll effect
window.addEventListener('scroll', function() {
    if (window.scrollY > 50) {
        modernNav?.classList.add('scrolled');
    } else {
        modernNav?.classList.remove('scrolled');
    }
});

// Active link based on current page
document.querySelectorAll('.nav-link').forEach(link => {
    if (link.href === window.location.href) {
        link.classList.add('active');
    }
});

// Fonction de confirmation de déconnexion avec modal stylisé
let currentLogoutUrl = '';

function confirmLogout(event, logoutUrl) {
    event.preventDefault();
    currentLogoutUrl = logoutUrl;
    document.getElementById('logoutModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeLogoutModal() {
    document.getElementById('logoutModal').classList.remove('active');
    document.body.style.overflow = '';
}

function proceedLogout() {
    window.location.href = currentLogoutUrl;
}

// Fermer le modal en cliquant à l'extérieur
document.addEventListener('click', function(e) {
    const modal = document.getElementById('logoutModal');
    if (e.target === modal) {
        closeLogoutModal();
    }
});

// Fermer le modal avec Échap
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeLogoutModal();
    }
});
