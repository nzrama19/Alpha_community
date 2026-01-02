// Génération de particules animées
function createParticles() {
    const particlesContainer = document.getElementById('particles');
    const particleCount = 20;

    for (let i = 0; i < particleCount; i++) {
        const particle = document.createElement('div');
        particle.classList.add('particle');

        const size = Math.random() * 100 + 50;
        particle.style.width = `${size}px`;
        particle.style.height = `${size}px`;
        particle.style.left = `${Math.random() * 100}%`;
        particle.style.top = `${Math.random() * 100}%`;
        particle.style.animationDelay = `${Math.random() * 5}s`;

        particlesContainer.appendChild(particle);
    }
}

// Vérification de la force du mot de passe
const passwordInput = document.getElementById('password');
const strengthDiv = document.getElementById('passwordStrength');

passwordInput.addEventListener('input', function() {
    const password = this.value;

    if (password.length === 0) {
        strengthDiv.style.display = 'none';
        return;
    }

    strengthDiv.style.display = 'block';

    let strength = 0;
    let tips = [];

    // Critères de validation (nouvelles contraintes)
    if (password.length >= 8) strength++;
    else tips.push('au moins 8 caractères');

    if (/[A-Z]/.test(password)) strength++;
    else tips.push('une majuscule');

    if (/[a-z]/.test(password)) strength++;
    else tips.push('une minuscule');

    if (/\d/.test(password)) strength++;
    else tips.push('un chiffre');

    if (/[^a-zA-Z0-9]/.test(password)) strength++;
    else tips.push('un caractère spécial');

    // Mise à jour de l'affichage
    if (strength < 3) {
        strengthDiv.textContent = '🔴 Faible - Manque: ' + tips.slice(0, 3).join(', ');
        strengthDiv.className = 'alert alert-error password-strength';
    } else if (strength < 5) {
        strengthDiv.textContent = '🟡 Moyen - Manque: ' + (tips.length > 0 ? tips.join(', ') : 'Presque bon !');
        strengthDiv.className = 'alert password-strength';
        strengthDiv.style.background = 'linear-gradient(135deg, rgba(255, 193, 7, 0.2) 0%, rgba(255, 193, 7, 0.1) 100%)';
        strengthDiv.style.border = '1px solid rgba(255, 193, 7, 0.3)';
        strengthDiv.style.color = '#ffc107';
    } else {
        strengthDiv.textContent = '🟢 Fort - Toutes les exigences sont respectées !';
        strengthDiv.className = 'alert alert-success password-strength';
    }
});

// Vérification de la correspondance des mots de passe
const confirmPasswordInput = document.getElementById('confirm_password');

confirmPasswordInput.addEventListener('input', function() {
    const password = passwordInput.value;
    const confirmPassword = this.value;

    if (confirmPassword.length > 0) {
        if (password === confirmPassword) {
            this.style.borderColor = '#4ecdc4';
        } else {
            this.style.borderColor = '#ff6b6b';
        }
    } else {
        this.style.borderColor = 'rgba(255, 255, 255, 0.1)';
    }
});

// Validation finale
document.getElementById('registerForm').addEventListener('submit', function(e) {
    const password = passwordInput.value;
    const confirmPassword = confirmPasswordInput.value;

    // Vérification des contraintes de mot de passe
    if (password.length < 8) {
        e.preventDefault();
        showError('Le mot de passe doit contenir au moins 8 caractères');
        return;
    }
    if (!/[A-Z]/.test(password)) {
        e.preventDefault();
        showError('Le mot de passe doit contenir au moins une majuscule');
        return;
    }
    if (!/[a-z]/.test(password)) {
        e.preventDefault();
        showError('Le mot de passe doit contenir au moins une minuscule');
        return;
    }
    if (!/[0-9]/.test(password)) {
        e.preventDefault();
        showError('Le mot de passe doit contenir au moins un chiffre');
        return;
    }
    if (!/[^A-Za-z0-9]/.test(password)) {
        e.preventDefault();
        showError('Le mot de passe doit contenir au moins un caractère spécial');
        return;
    }

    if (password !== confirmPassword) {
        e.preventDefault();
        showError('Les mots de passe ne correspondent pas !');
        return;
    }
});

function showError(message) {
    const errorDiv = document.createElement('div');
    errorDiv.className = 'alert alert-error';
    errorDiv.innerHTML = '<i class="fas fa-exclamation-circle"></i> <span>' + message + '</span>';

    const form = document.getElementById('registerForm');
    form.parentElement.insertBefore(errorDiv, form);

    passwordInput.focus();

    setTimeout(() => errorDiv.remove(), 5000);
}

// Animation au chargement
window.addEventListener('load', function() {
    createParticles();

    const inputs = document.querySelectorAll('.form-control');
    inputs.forEach((input, index) => {
        input.style.animationDelay = `${index * 0.1}s`;
        input.style.opacity = '0';
        input.style.animation = 'slideUp 0.5s ease-out forwards';
    });
});
