<?php

// ===== INITIALISER LES LOGS IMMÉDIATEMENT =====
require_once __DIR__ . '/../includes/LogInitializer.php';

/**
 * Charger les variables d'environnement depuis .env
 */
function loadEnv($filePath)
{
    if (!file_exists($filePath)) {
        throw new Exception("Le fichier .env n'existe pas à: " . $filePath);
    }

    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        // Ignorer les commentaires
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        // Parser la ligne
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            // Retirer les guillemets si présents
            if ((strpos($value, '"') === 0 && strrpos($value, '"') === strlen($value) - 1) ||
                (strpos($value, "'") === 0 && strrpos($value, "'") === strlen($value) - 1)
            ) {
                $value = substr($value, 1, -1);
            }

            $_ENV[$key] = $value;
            putenv("$key=$value");
        }
    }
}

// Charger le fichier .env
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    loadEnv($envFile);
}

// Démarrer la session avec les options de sécurité
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.use_strict_mode', '1');
session_start();

// ===== CONFIGURATION DE LA BASE DE DONNÉES =====
// Les identifiants peuvent être dans le fichier .env ou utiliser les valeurs par défaut pour le développement
define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
define('DB_NAME', $_ENV['DB_NAME'] ?? 'alpha_community');
define('DB_USER', $_ENV['DB_USER'] ?? 'root');
define('DB_PASS', $_ENV['DB_PASS'] ?? '');

// ===== URL DE BASE =====
// Charger depuis .env, sinon utiliser l'URL courante
define('BASE_URL', $_ENV['BASE_URL'] ?? 'http://localhost/PROJETS/Alpha_community/');

// ===== ENVIRONNEMENT =====
define('APP_ENV', $_ENV['APP_ENV'] ?? 'production');
define('APP_DEBUG', ($_ENV['APP_DEBUG'] ?? 'false') === 'true');

// ===== CHEMINS SENSIBLES =====
define('UPLOAD_DIR', __DIR__ . '/uploads/');
define('UPLOAD_URL', BASE_URL . 'config/uploads/');
define('UPLOAD_MAX_SIZE', intval($_ENV['UPLOAD_MAX_SIZE'] ?? 5242880));

// ===== CONFIGURATION SESSION =====
define('SESSION_TIMEOUT', intval($_ENV['SESSION_TIMEOUT'] ?? 3600));
define('SESSION_SECURE', ($_ENV['SESSION_SECURE'] ?? 'false') === 'true');
define('SESSION_HTTPONLY', ($_ENV['SESSION_HTTPONLY'] ?? 'true') === 'true');

// ===== TOKENS =====
define('JWT_SECRET', $_ENV['JWT_SECRET'] ?? 'your_secret_key_here');
define('ENCRYPTION_KEY', $_ENV['ENCRYPTION_KEY'] ?? 'your_encryption_key_here');

// ===== CONFIGURATION DE SÉCURITÉ =====
// Désactiver l'affichage des erreurs en production
if (APP_ENV === 'production') {
    error_reporting(E_ALL);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/../php_errors.log');
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/../php_errors.log');
}

// Créer le dossier uploads s'il n'existe pas
if (!file_exists(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0755, true);
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
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (PDOException $e) {
            $error_msg = "Erreur PDO: " . $e->getMessage();
            error_log($error_msg);

            if (APP_DEBUG) {
                echo "<pre style='background: #f8d7da; padding: 20px; border: 1px solid #f5c6cb; border-radius: 4px;'>";
                echo "<h2 style='color: #721c24;'>Erreur de Connexion Base de Données</h2>";
                echo "<strong>Host:</strong> " . htmlspecialchars($this->host) . "<br>";
                echo "<strong>DB:</strong> " . htmlspecialchars($this->db_name) . "<br>";
                echo "<strong>User:</strong> " . htmlspecialchars($this->username) . "<br>";
                echo "<strong>Erreur:</strong> " . htmlspecialchars($e->getMessage()) . "<br>";
                echo "</pre>";
            } else {
                http_response_code(503);
                echo "Service indisponible - Erreur de base de données. Vérifiez les logs.";
            }
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
