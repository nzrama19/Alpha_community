// ===== PAGE LOADER - STYLE PRIME TECH =====
(function() {
    const loader = document.querySelector('.page-loader');
    if (!loader) return;

    const percentage = loader.querySelector('.loader-percentage');
    const progressBar = loader.querySelector('.loader-progress-bar');
    
    let progress = 0;
    const duration = 2000; // Durée totale en ms
    const interval = 20; // Intervalle de mise à jour
    const steps = duration / interval;
    const increment = 100 / steps;

    // Animation du compteur
    const counter = setInterval(() => {
        progress += increment;
        
        if (progress >= 100) {
            progress = 100;
            clearInterval(counter);
            
            // Ajouter la classe loaded pour déclencher l'animation de sortie
            setTimeout(() => {
                loader.classList.add('loaded');
                
                // Supprimer le loader du DOM après l'animation
                setTimeout(() => {
                    loader.remove();
                }, 1100);
            }, 200);
        }
        
        if (percentage) {
            percentage.textContent = Math.floor(progress);
        }
        if (progressBar) {
            progressBar.style.width = progress + '%';
        }
    }, interval);

    // Assurer que le loader disparaît même si le compteur ne fonctionne pas
    window.addEventListener('load', function() {
        setTimeout(() => {
            if (loader && !loader.classList.contains('loaded')) {
                progress = 100;
                if (percentage) percentage.textContent = '100';
                if (progressBar) progressBar.style.width = '100%';
                
                setTimeout(() => {
                    loader.classList.add('loaded');
                    setTimeout(() => {
                        loader.remove();
                    }, 1100);
                }, 200);
            }
        }, 2500);
    });
})();

// ===== WELCOME TOAST =====
function showWelcomeToast(username, isAdmin = false) {
    // Vérifier si le toast a déjà été affiché dans cette session
    const toastShown = sessionStorage.getItem('welcomeToastShown');
    if (toastShown) {
        return; // Ne pas afficher à nouveau
    }

    const toast = document.createElement('div');
    toast.className = isAdmin ? 'welcome-toast admin-welcome-toast show' : 'welcome-toast show';
    
    const icon = isAdmin ? 'fa-shield-halved' : 'fa-hand-wave';
    const greeting = isAdmin ? 'Bienvenue Admin' : 'Bienvenue';
    const message = isAdmin ? 'Tableau de bord administrateur' : 'Heureux de vous revoir !';

    toast.innerHTML = `
        <div class="welcome-toast-icon">
            <i class="fas ${icon}"></i>
        </div>
        <div class="welcome-toast-content">
            <h4>${greeting}, ${username} !</h4>
            <p>${message}</p>
        </div>
        <button class="welcome-toast-close" onclick="closeWelcomeToast(this)">
            <i class="fas fa-times"></i>
        </button>
    `;

    document.body.appendChild(toast);

    // Marquer le toast comme affiché dans cette session
    sessionStorage.setItem('welcomeToastShown', 'true');

    // Auto-masquer après 5 secondes
    setTimeout(() => {
        closeWelcomeToast(toast.querySelector('.welcome-toast-close'));
    }, 5000);
}

function closeWelcomeToast(button) {
    const toast = button.closest('.welcome-toast');
    if (toast) {
        toast.classList.remove('show');
        toast.classList.add('hide');
        setTimeout(() => {
            toast.remove();
        }, 500);
    }
}

// Afficher le toast de bienvenue après le chargement du loader
window.addEventListener('load', function() {
    setTimeout(() => {
        // Vérifier si l'utilisateur est connecté et récupérer son nom
        const usernameElement = document.querySelector('[data-username]');
        const isAdminPage = document.body.classList.contains('admin-page');
        
        if (usernameElement) {
            const username = usernameElement.getAttribute('data-username');
            if (username) {
                showWelcomeToast(username, isAdminPage);
            }
        }
    }, 1300); // Afficher le toast 500ms après que le loader commence à disparaître
});
