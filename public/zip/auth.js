class AuthManager {
    constructor() {
        this.isAnimating = false;
        this.init();
    }

    init() {
        this.bindEvents();
        this.focusFirstInput();
    }

    bindEvents() {
        // Mode switch buttons
        document.querySelectorAll('[data-mode]').forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                const mode = button.getAttribute('data-mode');
                this.switchMode(mode);
            });
        });

        // Password toggle
        document.querySelectorAll('.password-toggle').forEach(button => {
            button.addEventListener('click', () => {
                const targetId = button.getAttribute('data-target');
                this.togglePassword(targetId);
            });
        });

        // Form validation for registration
        const authForm = document.getElementById('authForm');
        if (authForm && document.body.classList.contains('register-mode')) {
            authForm.addEventListener('submit', (e) => this.validateRegistrationForm(e));
        }
    }

    switchMode(mode) {
        if (this.isAnimating) return;

        const wrapper = document.getElementById('authWrapper');
        const isCurrentlyRegister = document.body.classList.contains('register-mode');
        const targetIsRegister = mode === 'register';

        if ((targetIsRegister && isCurrentlyRegister) || (!targetIsRegister && !isCurrentlyRegister)) {
            return;
        }

        this.isAnimating = true;
        wrapper.classList.add('animating');

        setTimeout(() => {
            const url = targetIsRegister ? 'auth.php?register=true' : 'auth.php';
            window.location.href = url;
        }, 800);
    }

    togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        if (!field) return;
        
        const toggleBtn = field.parentNode.querySelector('.password-toggle i');
        if (field.type === 'password') {
            field.type = 'text';
            toggleBtn.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            field.type = 'password';
            toggleBtn.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }

    validateRegistrationForm(e) {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm_password').value;
        const username = document.getElementById('username').value;
        const email = document.getElementById('email').value;

        let hasError = false;

        // Reset errors
        document.querySelectorAll('.validation-message').forEach(el => el.textContent = '');
        document.querySelectorAll('.form-control').forEach(el => el.style.borderColor = '');

        // Username validation
        if (username.length < 3) {
            this.showError('username', 'Le nom d\'utilisateur doit contenir au moins 3 caractères');
            hasError = true;
        } else if (!username.match(/^[a-zA-Z0-9_]+$/)) {
            this.showError('username', 'Le nom d\'utilisateur ne peut contenir que des lettres, chiffres et underscores');
            hasError = true;
        }

        // Email validation
        if (!email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
            this.showError('email', 'Adresse email invalide');
            hasError = true;
        }

        // Password validation
        if (password.length < 6) {
            this.showError('password', 'Le mot de passe doit contenir au moins 6 caractères');
            hasError = true;
        }

        // Password confirmation
        if (password !== confirmPassword) {
            this.showError('confirm_password', 'Les mots de passe ne correspondent pas');
            hasError = true;
        }

        if (hasError) {
            e.preventDefault();
        }
    }

    showError(fieldId, message) {
        const errorElement = document.getElementById(`${fieldId}-error`);
        const inputElement = document.getElementById(fieldId);
        
        if (errorElement) {
            errorElement.textContent = message;
        }
        if (inputElement) {
            inputElement.style.borderColor = 'var(--error-color)';
        }
    }

    focusFirstInput() {
        const firstInput = document.querySelector('input[required]');
        if (firstInput) firstInput.focus();
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new AuthManager();
});
