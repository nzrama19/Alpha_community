<?php

/**
 * Classe centralisée de gestion des erreurs (Serveur + Client)
 * Enregistre toutes les erreurs dans un seul fichier: errors.log
 * 
 * Usage Serveur PHP:
 * $errorLog = new AppErrorLogger();
 * $errorLog->error('Message d\'erreur', $exception);
 * 
 * Usage Client JavaScript:
 * Voir: public/js/error-logger.js
 */
class AppErrorLogger
{
    const LOG_FILE = __DIR__ . '/../errors.log';
    const MAX_FILE_SIZE = 10485760; // 10MB

    // Types d'erreurs
    const ERROR_PHP = 'PHP_ERROR';
    const ERROR_EXCEPTION = 'EXCEPTION';
    const ERROR_DATABASE = 'DATABASE_ERROR';
    const ERROR_VALIDATION = 'VALIDATION_ERROR';
    const ERROR_JAVASCRIPT = 'JAVASCRIPT_ERROR';
    const ERROR_SECURITY = 'SECURITY_ERROR';
    const ERROR_API = 'API_ERROR';

    /**
     * Enregistrer une erreur
     */
    public static function log($type, $message, $context = [])
    {
        $logEntry = self::formatLogEntry($type, $message, $context);

        // Écrire dans le fichier
        file_put_contents(self::LOG_FILE, $logEntry . "\n", FILE_APPEND | LOCK_EX);

        // Vérifier la rotation
        self::rotateIfNeeded();
    }

    /**
     * Logger une erreur PHP
     */
    public static function phpError($message, $exception = null)
    {
        $context = [
            'ip' => self::getClientIp(),
            'uri' => $_SERVER['REQUEST_URI'] ?? '',
            'method' => $_SERVER['REQUEST_METHOD'] ?? 'N/A'
        ];

        if ($exception) {
            $context['exception'] = get_class($exception);
            $context['file'] = $exception->getFile();
            $context['line'] = $exception->getLine();
            $context['trace'] = self::sanitizeTrace($exception->getTraceAsString());
        }

        self::log(self::ERROR_PHP, $message, $context);
    }

    /**
     * Logger une erreur de base de données
     */
    public static function databaseError($message, $query = '', $error = '')
    {
        $context = [
            'query' => self::sanitizeQuery($query),
            'error' => $error,
            'ip' => self::getClientIp()
        ];

        self::log(self::ERROR_DATABASE, $message, $context);
    }

    /**
     * Logger une erreur de validation
     */
    public static function validationError($message, $field = '', $value = '')
    {
        $context = [
            'field' => $field,
            'value' => self::sanitizeValue($value),
            'ip' => self::getClientIp()
        ];

        self::log(self::ERROR_VALIDATION, $message, $context);
    }

    /**
     * Logger une erreur de sécurité
     */
    public static function securityError($message, $context = [])
    {
        $context['ip'] = self::getClientIp();
        $context['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $context['timestamp'] = date('Y-m-d H:i:s');

        self::log(self::ERROR_SECURITY, $message, $context);
    }

    /**
     * Logger une erreur d'API
     */
    public static function apiError($message, $endpoint = '', $statusCode = 0, $response = '')
    {
        $context = [
            'endpoint' => $endpoint,
            'status_code' => $statusCode,
            'response' => $response,
            'ip' => self::getClientIp()
        ];

        self::log(self::ERROR_API, $message, $context);
    }

    /**
     * Logger une erreur JavaScript (depuis le client)
     */
    public static function javascriptError($message, $filename = '', $lineno = 0, $colno = 0, $stack = '')
    {
        $context = [
            'filename' => $filename,
            'line' => $lineno,
            'column' => $colno,
            'stack' => $stack,
            'ip' => self::getClientIp(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? ''
        ];

        self::log(self::ERROR_JAVASCRIPT, $message, $context);
    }

    /**
     * Formater une entrée de log
     */
    private static function formatLogEntry($type, $message, $context = [])
    {
        $timestamp = date('Y-m-d H:i:s');
        $contextJson = !empty($context) ? ' | ' . json_encode($context, JSON_UNESCAPED_UNICODE) : '';

        return "[$timestamp] [$type] $message$contextJson";
    }

    /**
     * Obtenir l'adresse IP du client
     */
    private static function getClientIp()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
        }

        return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : 'Invalid IP';
    }

    /**
     * Nettoyer une trace d'exception
     */
    private static function sanitizeTrace($trace)
    {
        // Limiter la longueur et enlever les chemins complets
        $lines = explode("\n", $trace);
        $sanitized = [];

        foreach (array_slice($lines, 0, 5) as $line) {
            $line = str_replace($_SERVER['DOCUMENT_ROOT'] ?? '', '', $line);
            $sanitized[] = trim($line);
        }

        return implode(' | ', $sanitized);
    }

    /**
     * Nettoyer une requête SQL
     */
    private static function sanitizeQuery($query)
    {
        // Enlever les données sensibles
        $query = preg_replace('/password\s*=\s*[\'"].*?[\'"]/', 'password=***', $query);
        $query = preg_replace('/token\s*=\s*[\'"].*?[\'"]/', 'token=***', $query);

        return substr($query, 0, 500); // Limiter la longueur
    }

    /**
     * Nettoyer une valeur
     */
    private static function sanitizeValue($value)
    {
        if (is_array($value)) {
            return '[Array]';
        }

        if (is_object($value)) {
            return '[Object]';
        }

        return substr((string)$value, 0, 100);
    }

    /**
     * Vérifier et effectuer la rotation du fichier
     */
    private static function rotateIfNeeded()
    {
        if (!file_exists(self::LOG_FILE)) {
            return;
        }

        $fileSize = filesize(self::LOG_FILE);

        if ($fileSize > self::MAX_FILE_SIZE) {
            $timestamp = date('Y-m-d_H-i-s');
            $backupFile = self::LOG_FILE . '.' . $timestamp;

            rename(self::LOG_FILE, $backupFile);

            // Compresser si possible
            if (extension_loaded('zlib')) {
                $compressed = gzcompress(file_get_contents($backupFile), 9);
                file_put_contents($backupFile . '.gz', $compressed);
                unlink($backupFile);
            }
        }

        // Nettoyer les vieux logs
        self::cleanOldLogs();
    }

    /**
     * Nettoyer les logs archivés anciens
     */
    private static function cleanOldLogs()
    {
        $archiveDays = 30;
        $cutoffDate = time() - ($archiveDays * 86400);
        $logDir = dirname(self::LOG_FILE);

        foreach (glob($logDir . '/errors.log.*') as $logFile) {
            if (filemtime($logFile) < $cutoffDate) {
                @unlink($logFile);
            }
        }
    }

    /**
     * Obtenir les logs récents
     */
    public static function getRecentLogs($limit = 100)
    {
        if (!file_exists(self::LOG_FILE)) {
            return [];
        }

        $lines = file(self::LOG_FILE);
        return array_reverse(array_slice($lines, -$limit));
    }

    /**
     * Obtenir les stats du fichier de log
     */
    public static function getStats()
    {
        $stats = [
            'total_size' => 0,
            'total_files' => 0,
            'error_counts' => [
                self::ERROR_PHP => 0,
                self::ERROR_JAVASCRIPT => 0,
                self::ERROR_DATABASE => 0,
                self::ERROR_VALIDATION => 0,
                self::ERROR_SECURITY => 0,
                self::ERROR_API => 0
            ]
        ];

        // Taille du fichier principal
        if (file_exists(self::LOG_FILE)) {
            $stats['total_size'] += filesize(self::LOG_FILE);
            $stats['total_files']++;

            // Compter les erreurs par type
            $lines = file(self::LOG_FILE);
            foreach ($lines as $line) {
                foreach ($stats['error_counts'] as $type => $count) {
                    if (strpos($line, "[$type]") !== false) {
                        $stats['error_counts'][$type]++;
                    }
                }
            }
        }

        // Fichiers archivés
        foreach (glob(dirname(self::LOG_FILE) . '/errors.log.*') as $file) {
            $stats['total_size'] += filesize($file);
            $stats['total_files']++;
        }

        $stats['total_size_mb'] = round($stats['total_size'] / 1048576, 2);

        return $stats;
    }

    /**
     * Exporter les logs
     */
    public static function exportLogs($outputFile)
    {
        if (!file_exists(self::LOG_FILE)) {
            throw new Exception('Fichier de log non trouvé');
        }

        $content = file_get_contents(self::LOG_FILE);
        file_put_contents($outputFile, $content);

        return true;
    }

    /**
     * Nettoyer tous les logs
     */
    public static function clearAllLogs()
    {
        if (file_exists(self::LOG_FILE)) {
            unlink(self::LOG_FILE);
        }

        foreach (glob(dirname(self::LOG_FILE) . '/errors.log.*') as $file) {
            @unlink($file);
        }
    }

    /**
     * Obtenir le chemin du fichier de log
     */
    public static function getLogFile()
    {
        return self::LOG_FILE;
    }
}
