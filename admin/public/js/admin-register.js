const passwordInput = document.getElementById('password');
const requirementsDiv = document.getElementById('passwordRequirements');

// Afficher/masquer les exigences
passwordInput.addEventListener('focus', function() {
    requirementsDiv.style.display = 'block';
});

// Vérification en temps réel des contraintes
passwordInput.addEventListener('input', function() {
    const password = this.value;

    // Longueur
    updateRequirement('req-length', password.length >= 8);
    // Majuscule
    updateRequirement('req-uppercase', /[A-Z]/.test(password));
    // Minuscule
    updateRequirement('req-lowercase', /[a-z]/.test(password));
    // Chiffre
    updateRequirement('req-number', /[0-9]/.test(password));
    // Caractère spécial
    updateRequirement('req-special', /[^A-Za-z0-9]/.test(password));
});

function updateRequirement(id, valid) {
    const element = document.getElementById(id);
    if (valid) {
        element.style.color = '#2e7d32';
        element.innerHTML = element.innerHTML.replace('fa-times', 'fa-check');
    } else {
        element.style.color = '#c62828';
        element.innerHTML = element.innerHTML.replace('fa-check', 'fa-times');
    }
}

// Validation avant soumission
document.querySelector('form').addEventListener('submit', function(e) {
    const password = passwordInput.value;
    const confirmPassword = document.getElementById('confirm_password').value;

    // Vérifier toutes les contraintes
    if (password.length < 8) {
        e.preventDefault();
        alert('Le mot de passe doit contenir au moins 8 caractères');
        passwordInput.focus();
        return false;
    }
    if (!/[A-Z]/.test(password)) {
        e.preventDefault();
        alert('Le mot de passe doit contenir au moins une majuscule');
        passwordInput.focus();
        return false;
    }
    if (!/[a-z]/.test(password)) {
        e.preventDefault();
        alert('Le mot de passe doit contenir au moins une minuscule');
        passwordInput.focus();
        return false;
    }
    if (!/[0-9]/.test(password)) {
        e.preventDefault();
        alert('Le mot de passe doit contenir au moins un chiffre');
        passwordInput.focus();
        return false;
    }
    if (!/[^A-Za-z0-9]/.test(password)) {
        e.preventDefault();
        alert('Le mot de passe doit contenir au moins un caractère spécial (!@#$%^&*...)');
        passwordInput.focus();
        return false;
    }
    if (password !== confirmPassword) {
        e.preventDefault();
        alert('Les mots de passe ne correspondent pas');
        document.getElementById('confirm_password').focus();
        return false;
    }
});
