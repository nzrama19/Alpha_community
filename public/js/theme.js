// ===== GESTION DU THÈME CLAIR/SOMBRE =====

(function() {
    'use strict';

    const THEME_KEY = 'alpha-community-theme';
    const THEME_LIGHT = 'light';
    const THEME_DARK = 'dark';

    class ThemeManager {
        constructor() {
            this.themeToggle = document.getElementById('themeToggle');
            this.themeIcon = document.getElementById('themeIcon');
            this.themeText = document.getElementById('themeText');
            this.body = document.body;
            
            this.init();
        }

        init() {
            // Charger le thème sauvegardé ou utiliser le thème par défaut (sombre)
            const savedTheme = this.getSavedTheme();
            this.applyTheme(savedTheme, false);

            // Ajouter l'écouteur d'événement sur le bouton toggle
            if (this.themeToggle) {
                this.themeToggle.addEventListener('click', () => this.toggleTheme());
            }
        }

        getSavedTheme() {
            return localStorage.getItem(THEME_KEY) || THEME_DARK;
        }

        saveTheme(theme) {
            localStorage.setItem(THEME_KEY, theme);
        }

        applyTheme(theme, animate = true) {
            // Ajouter classe de transition si animation activée
            if (animate) {
                this.body.classList.add('theme-transition');
                setTimeout(() => {
                    this.body.classList.remove('theme-transition');
                }, 300);
            }

            // Appliquer ou retirer la classe light-theme
            if (theme === THEME_LIGHT) {
                this.body.classList.add('light-theme');
                this.updateToggleUI(THEME_LIGHT);
            } else {
                this.body.classList.remove('light-theme');
                this.updateToggleUI(THEME_DARK);
            }

            // Sauvegarder la préférence
            this.saveTheme(theme);
        }

        updateToggleUI(theme) {
            if (!this.themeIcon) return;

            if (theme === THEME_LIGHT) {
                // Afficher l'icône soleil pour le thème clair
                this.themeIcon.classList.remove('fa-moon');
                this.themeIcon.classList.add('fa-sun');
                if (this.themeText) {
                    this.themeText.textContent = 'Clair';
                }
                this.themeToggle.setAttribute('aria-label', 'Passer au thème sombre');
            } else {
                // Afficher l'icône lune pour le thème sombre
                this.themeIcon.classList.remove('fa-sun');
                this.themeIcon.classList.add('fa-moon');
                if (this.themeText) {
                    this.themeText.textContent = 'Sombre';
                }
                this.themeToggle.setAttribute('aria-label', 'Passer au thème clair');
            }
        }

        toggleTheme() {
            const currentTheme = this.getSavedTheme();
            const newTheme = currentTheme === THEME_LIGHT ? THEME_DARK : THEME_LIGHT;
            this.applyTheme(newTheme, true);
        }

        getCurrentTheme() {
            return this.getSavedTheme();
        }
    }

    // Initialiser le gestionnaire de thème dès que le DOM est prêt
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            window.themeManager = new ThemeManager();
        });
    } else {
        window.themeManager = new ThemeManager();
    }
})();
