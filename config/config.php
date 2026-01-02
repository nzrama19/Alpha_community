<?php
// Démarrer la session
session_start();

// ===== CONFIGURATION DE LA BASE DE DONNÉES =====
define('DB_HOST', 'localhost');
define('DB_NAME', 'posts_system');
define('DB_USER', 'nzrama');
define('DB_PASS', 'Mondesir@19');

// URL de base
define('BASE_URL', 'http://localhost/PROJETS/Alpha_community/');

// Dossier pour les uploads
define('UPLOAD_DIR', __DIR__ . '/uploads/');
define('UPLOAD_URL', BASE_URL . 'config/uploads/');

// Créer le dossier uploads s'il n'existe pas
if (!file_exists(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0777, true);
}

// ===== CLASSE DE GESTION DE LA CONNEXION BD =====
class Database
{
    private $host = DB_HOST;
    private $db_name = DB_NAME;
    private $username = DB_USER;
    private $password = DB_PASS;
    public $conn;

    /**
     * Obtenir la connexion à la base de données
     * @return PDO|null La connexion PDO ou null en cas d'erreur
     */
    public function getConnection()
    {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Erreur de connexion à la base de données: " . $e->getMessage();
            exit();
        }

        return $this->conn;
    }
}

// Fonction pour vérifier si l'utilisateur normal est connecté
function isUserLoggedIn()
{
    return isset($_SESSION['user_id']) && isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'user';
}

// Fonction pour vérifier si l'admin est connecté
function isAdminLoggedIn()
{
    return isset($_SESSION['admin_id']) && isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin';
}

// Fonction pour vérifier si quelqu'un est connecté (admin ou user)
function isLoggedIn()
{
    return isUserLoggedIn() || isAdminLoggedIn();
}

// Fonction pour vérifier si l'utilisateur est admin
function isAdmin()
{
    return isAdminLoggedIn();
}

// Fonction pour rediriger
function redirect($page)
{
    header("Location: " . BASE_URL . $page);
    exit();
}

// Fonction pour échapper les données
function escape($data)
{
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

// ===== SYSTÈME DE "REMEMBER ME" =====

/**
 * Créer un token "Remember Me" pour un utilisateur
 * @param int $user_id ID de l'utilisateur
 * @param int $days Nombre de jours de validité (par défaut 30)
 * @return bool True si succès, False sinon
 */
function createRememberToken($user_id, $days = 30)
{
    $database = new Database();
    $conn = $database->getConnection();

    // Générer un token sécurisé
    $token = bin2hex(random_bytes(32));

    // Date d'expiration
    $expiry = date('Y-m-d H:i:s', time() + ($days * 24 * 60 * 60));

    try {
        $query = "INSERT INTO remember_tokens (user_id, token, expiry) VALUES (:user_id, :token, :expiry)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':expiry', $expiry);

        if ($stmt->execute()) {
            // Créer le cookie (valable 30 jours)
            setcookie('remember_token', $token, time() + ($days * 24 * 60 * 60), '/', '', false, true);
            return true;
        }
    } catch (PDOException $e) {
        error_log("Erreur création token: " . $e->getMessage());
    }

    return false;
}

/**
 * Vérifier et restaurer la session depuis un token "Remember Me"
 * @return bool True si la session a été restaurée, False sinon
 */
function checkRememberToken()
{
    // Si déjà connecté, ne rien faire
    if (isLoggedIn()) {
        return true;
    }

    // Vérifier si le cookie existe
    if (!isset($_COOKIE['remember_token'])) {
        return false;
    }

    $token = $_COOKIE['remember_token'];
    $database = new Database();
    $conn = $database->getConnection();

    try {
        // Récupérer le token et l'utilisateur associé
        $query = "SELECT rt.user_id, rt.expiry, u.username, u.avatar 
                  FROM remember_tokens rt 
                  INNER JOIN users u ON rt.user_id = u.id 
                  WHERE rt.token = :token AND rt.expiry > NOW()";

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':token', $token);
        $stmt->execute();

        $result = $stmt->fetch();

        if ($result) {
            // Restaurer la session
            $_SESSION['user_id'] = $result['user_id'];
            $_SESSION['username'] = $result['username'];
            $_SESSION['avatar'] = $result['avatar'];
            $_SESSION['user_type'] = 'user';

            // Renouveler le token (créer un nouveau)
            deleteRememberToken($token);
            createRememberToken($result['user_id']);

            return true;
        } else {
            // Token invalide ou expiré, le supprimer
            deleteRememberToken($token);
            setcookie('remember_token', '', time() - 3600, '/');
        }
    } catch (PDOException $e) {
        error_log("Erreur vérification token: " . $e->getMessage());
    }

    return false;
}

/**
 * Supprimer un token "Remember Me"
 * @param string $token Le token à supprimer
 * @return bool True si succès, False sinon
 */
function deleteRememberToken($token)
{
    $database = new Database();
    $conn = $database->getConnection();

    try {
        $query = "DELETE FROM remember_tokens WHERE token = :token";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':token', $token);
        return $stmt->execute();
    } catch (PDOException $e) {
        error_log("Erreur suppression token: " . $e->getMessage());
    }

    return false;
}

/**
 * Supprimer tous les tokens d'un utilisateur (lors de la déconnexion)
 * @param int $user_id ID de l'utilisateur
 * @return bool True si succès, False sinon
 */
function deleteAllUserTokens($user_id)
{
    $database = new Database();
    $conn = $database->getConnection();

    try {
        $query = "DELETE FROM remember_tokens WHERE user_id = :user_id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        return $stmt->execute();
    } catch (PDOException $e) {
        error_log("Erreur suppression tokens utilisateur: " . $e->getMessage());
    }

    return false;
}

/**
 * Nettoyer les tokens expirés (à exécuter régulièrement)
 */
function cleanExpiredTokens()
{
    $database = new Database();
    $conn = $database->getConnection();

    try {
        $query = "DELETE FROM remember_tokens WHERE expiry < NOW()";
        $stmt = $conn->prepare($query);
        $stmt->execute();
    } catch (PDOException $e) {
        error_log("Erreur nettoyage tokens expirés: " . $e->getMessage());
    }
}

// Vérifier automatiquement le token Remember Me à chaque chargement de page
checkRememberToken();

// Nettoyer les tokens expirés (1 chance sur 100)
if (rand(1, 100) === 1) {
    cleanExpiredTokens();
}
