<?php

/**
 * Classe de logging sécurisé des événements
 * 
 * Usage:
 * $logger = new SecurityLogger();
 * $logger->info('Utilisateur connecté', ['user_id' => 1]);
 * $logger->warning('Tentative de login échouée', ['ip' => $_SERVER['REMOTE_ADDR']]);
 * $logger->error('Erreur critique', ['error' => $e->getMessage()]);
 */
class SecurityLogger
{
    const LOG_DIR = __DIR__ . '/../logs/';
    const INFO = 'INFO';
    const WARNING = 'WARNING';
    const ERROR = 'ERROR';
    const CRITICAL = 'CRITICAL';

    public function __construct()
    {
        if (!file_exists(self::LOG_DIR)) {
            mkdir(self::LOG_DIR, 0755, true);
        }
    }

    /**
     * Logger un message INFO
     * @param string $message Message à logger
     * @param array $context Contexte additionnel
     */
    public function info($message, $context = [])
    {
        $this->log(self::INFO, $message, $context);
    }

    /**
     * Logger un message WARNING
     * @param string $message Message à logger
     * @param array $context Contexte additionnel
     */
    public function warning($message, $context = [])
    {
        $this->log(self::WARNING, $message, $context);
    }

    /**
     * Logger un message ERROR
     * @param string $message Message à logger
     * @param array $context Contexte additionnel
     */
    public function error($message, $context = [])
    {
        $this->log(self::ERROR, $message, $context);
    }

    /**
     * Logger un message CRITICAL
     * @param string $message Message à logger
     * @param array $context Contexte additionnel
     */
    public function critical($message, $context = [])
    {
        $this->log(self::CRITICAL, $message, $context);
    }

    /**
     * Logger un événement de sécurité
     * @param string $event Nom de l'événement
     * @param array $data Données associées
     */
    public function securityEvent($event, $data = [])
    {
        $data['timestamp'] = date('Y-m-d H:i:s');
        $data['ip'] = $this->getClientIp();
        $data['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';

        $this->log(self::WARNING, "SECURITY_EVENT: $event", $data);
    }

    /**
     * Logger un événement de tentative de connexion
     * @param string $username Nom d'utilisateur
     * @param bool $success Succès ou non
     */
    public function loginAttempt($username, $success = false)
    {
        $event = $success ? 'LOGIN_SUCCESS' : 'LOGIN_FAILED';
        $level = $success ? self::INFO : self::WARNING;

        $this->log($level, $event, [
            'username' => $username,
            'ip' => $this->getClientIp(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
        ]);
    }

    /**
     * Logger une tentative d'accès non autorisé
     * @param string $page Page tentée
     * @param string $reason Raison du refus
     */
    public function unauthorizedAccess($page, $reason = '')
    {
        $this->securityEvent('UNAUTHORIZED_ACCESS', [
            'page' => $page,
            'reason' => $reason
        ]);
    }

    /**
     * Logger une manipulation de fichier
     * @param string $action Action effectuée
     * @param string $file Fichier concerné
     * @param string $user_id ID utilisateur
     */
    public function fileOperation($action, $file, $user_id = null)
    {
        $this->securityEvent('FILE_OPERATION', [
            'action' => $action,
            'file' => $file,
            'user_id' => $user_id
        ]);
    }

    /**
     * Logger une erreur de base de données
     * @param string $message Message d'erreur
     * @param string $query Requête SQL (sans données sensibles)
     */
    public function databaseError($message, $query = '')
    {
        $this->log(self::ERROR, 'DATABASE_ERROR', [
            'message' => $message,
            'query' => $query
        ]);
    }

    /**
     * Enregistrer dans le fichier de log
     * @param string $level Niveau de log
     * @param string $message Message
     * @param array $context Contexte additionnel
     */
    private function log($level, $message, $context = [])
    {
        $timestamp = date('Y-m-d H:i:s');
        $contextJson = !empty($context) ? ' | ' . json_encode($context, JSON_UNESCAPED_UNICODE) : '';
        $logEntry = "[$timestamp] [$level] $message$contextJson\n";

        $logFile = self::LOG_DIR . strtolower($level) . '_' . date('Y-m-d') . '.log';
        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);

        // Limiter la taille des logs
        $this->rotateLogs($logFile);
    }

    /**
     * Obtenir l'adresse IP du client
     * @return string
     */
    private function getClientIp()
    {
        $ip = '';

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
        }

        // Valider l'IP
        if (filter_var($ip, FILTER_VALIDATE_IP)) {
            return $ip;
        }

        return 'Invalid IP';
    }

    /**
     * Rotationner les logs si trop volumineux
     * @param string $logFile Chemin du fichier de log
     * @param int $maxSize Taille maximale en MB
     */
    private function rotateLogs($logFile, $maxSize = 10)
    {
        $maxBytes = $maxSize * 1024 * 1024;

        if (file_exists($logFile) && filesize($logFile) > $maxBytes) {
            $backupFile = $logFile . '.' . date('Y-m-d_H-i-s');
            rename($logFile, $backupFile);

            // Compresser l'ancien log
            if (function_exists('gzcompress')) {
                $compressed = gzcompress(file_get_contents($backupFile), 9);
                file_put_contents($backupFile . '.gz', $compressed);
                unlink($backupFile);
            }
        }
    }

    /**
     * Obtenir les logs récents
     * @param string $level Niveau de log à filtrer
     * @param int $days Nombre de jours à récupérer
     * @return array
     */
    public function getLogs($level = null, $days = 7)
    {
        $logs = [];

        for ($i = 0; $i < $days; $i++) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $pattern = $level ? self::LOG_DIR . strtolower($level) . "_$date.log" : self::LOG_DIR . "*_$date.log";

            foreach (glob($pattern) as $logFile) {
                $logs[] = file_get_contents($logFile);
            }
        }

        return $logs;
    }
}
