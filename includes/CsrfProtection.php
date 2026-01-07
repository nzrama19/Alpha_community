<?php

/**
 * Classe de gestion des tokens CSRF
 * Protection contre les attaques Cross-Site Request Forgery
 * 
 * Usage:
 * // Générer un token
 * $csrf = new CsrfProtection();
 * $token = $csrf->generateToken();
 * 
 * // Dans le formulaire HTML
 * <form method="POST">
 *     <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($token); ?>">
 * </form>
 * 
 * // Vérifier le token
 * if (!$csrf->verifyToken($_POST['csrf_token'] ?? '')) {
 *     die('Token CSRF invalide');
 * }
 */
class CsrfProtection
{
    const TOKEN_NAME = 'csrf_token';
    const TOKEN_LENGTH = 32;

    /**
     * Générer un nouveau token CSRF
     * @return string Le token généré
     */
    public function generateToken()
    {
        if (!isset($_SESSION[self::TOKEN_NAME])) {
            $_SESSION[self::TOKEN_NAME] = bin2hex(random_bytes(self::TOKEN_LENGTH));
        }
        return $_SESSION[self::TOKEN_NAME];
    }

    /**
     * Vérifier un token CSRF
     * @param string $token Token à vérifier
     * @return bool True si valide, False sinon
     */
    public function verifyToken($token)
    {
        if (empty($token) || empty($_SESSION[self::TOKEN_NAME])) {
            return false;
        }

        return hash_equals($_SESSION[self::TOKEN_NAME], $token);
    }

    /**
     * Régénérer un token (après vérification réussie)
     */
    public function regenerateToken()
    {
        $_SESSION[self::TOKEN_NAME] = bin2hex(random_bytes(self::TOKEN_LENGTH));
    }

    /**
     * Obtenir le token actuel ou en générer un nouveau
     * @return string
     */
    public function getToken()
    {
        return $this->generateToken();
    }

    /**
     * Afficher un champ input CSRF caché
     * @return string
     */
    public function getTokenField()
    {
        $token = $this->generateToken();
        return '<input type="hidden" name="' . self::TOKEN_NAME . '" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }
}
