<?php

/**
 * Classe de gestion des logs d'erreur
 * Enregistre les erreurs dans des fichiers avec rotation automatique
 * 
 * Usage:
 * $errorLogger = new ErrorLogger();
 * $errorLogger->error('Message d\'erreur', $exception);
 * $errorLogger->warning('Attention');
 * $errorLogger->info('Information');
 */
class ErrorLogger
{
    const LOG_DIR = __DIR__ . '/../logs/';
    const FATAL = 'FATAL';
    const ERROR = 'ERROR';
    const WARNING = 'WARNING';
    const INFO = 'INFO';
    const DEBUG = 'DEBUG';

    const MAX_LOG_SIZE = 10485760; // 10MB
    const ARCHIVE_DAYS = 30;

    private $logLevel;

    public function __construct($logLevel = self::DEBUG)
    {
        $this->logLevel = $logLevel;
        $this->createLogDirectory();
        $this->registerErrorHandlers();
    }

    /**
     * Créer le répertoire des logs s'il n'existe pas
     */
    private function createLogDirectory()
    {
        if (!file_exists(self::LOG_DIR)) {
            mkdir(self::LOG_DIR, 0755, true);
        }
    }

    /**
     * Enregistrer les gestionnaires d'erreur PHP natifs
     */
    private function registerErrorHandlers()
    {
        // Gérer les erreurs PHP
        set_error_handler([$this, 'handlePhpError']);

        // Gérer les exceptions non capturées
        set_exception_handler([$this, 'handleException']);

        // Gérer les erreurs de shutdown
        register_shutdown_function([$this, 'handleShutdownError']);
    }

    /**
     * Gestionnaire d'erreur PHP
     */
    public function handlePhpError($errno, $errstr, $errfile, $errline)
    {
        $errorType = $this->getErrorType($errno);

        $context = [
            'type' => $errorType,
            'file' => $errfile,
            'line' => $errline,
            'errno' => $errno
        ];

        $this->log(self::ERROR, $errstr, $context);

        // Retourner true pour éviter le gestionnaire d'erreur par défaut
        return true;
    }

    /**
     * Gestionnaire d'exception
     */
    public function handleException($exception)
    {
        $this->logException($exception);
    }

    /**
     * Gestionnaire de shutdown
     */
    public function handleShutdownError()
    {
        $error = error_get_last();

        if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            $this->log(
                self::FATAL,
                $error['message'],
                [
                    'file' => $error['file'],
                    'line' => $error['line'],
                    'type' => $this->getErrorType($error['type'])
                ]
            );
        }
    }

    /**
     * Logger une exception
     */
    public function logException($exception)
    {
        $context = [
            'exception' => get_class($exception),
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString()
        ];

        $this->log(self::ERROR, 'Exception: ' . get_class($exception), $context);
    }

    /**
     * Logger une erreur
     */
    public function error($message, $context = [])
    {
        if (is_object($context) && method_exists($context, 'getMessage')) {
            // C'est une exception
            $context = [
                'exception' => get_class($context),
                'message' => $context->getMessage(),
                'file' => $context->getFile(),
                'line' => $context->getLine(),
                'trace' => $context->getTraceAsString()
            ];
        }

        $this->log(self::ERROR, $message, $context);
    }

    /**
     * Logger un avertissement
     */
    public function warning($message, $context = [])
    {
        $this->log(self::WARNING, $message, $context);
    }

    /**
     * Logger une information
     */
    public function info($message, $context = [])
    {
        $this->log(self::INFO, $message, $context);
    }

    /**
     * Logger un debug
     */
    public function debug($message, $context = [])
    {
        $this->log(self::DEBUG, $message, $context);
    }

    /**
     * Logger une erreur fatale
     */
    public function fatal($message, $context = [])
    {
        $this->log(self::FATAL, $message, $context);
        exit(1);
    }

    /**
     * Logger un message
     */
    private function log($level, $message, $context = [])
    {
        // Vérifier le niveau de log
        if (!$this->shouldLog($level)) {
            return;
        }

        $timestamp = date('Y-m-d H:i:s');
        $contextJson = !empty($context) ? ' | ' . json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : '';

        // Format: [YYYY-MM-DD HH:MM:SS] [LEVEL] Message | Context
        $logEntry = "[$timestamp] [$level] $message$contextJson\n";

        $logFile = self::LOG_DIR . strtolower($level) . '_' . date('Y-m-d') . '.log';

        // Écrire dans le fichier avec verrou
        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);

        // Mettre à jour les permissions
        if (file_exists($logFile)) {
            chmod($logFile, 0644);
        }

        // Vérifier la rotation des logs
        $this->rotateLogs($logFile);
    }

    /**
     * Vérifier si le message doit être loggé
     */
    private function shouldLog($level)
    {
        $levels = [self::FATAL, self::ERROR, self::WARNING, self::INFO, self::DEBUG];
        $currentLevelIndex = array_search($this->logLevel, $levels);
        $messageLevelIndex = array_search($level, $levels);

        return $messageLevelIndex <= $currentLevelIndex;
    }

    /**
     * Obtenir le type d'erreur PHP
     */
    private function getErrorType($errno)
    {
        $errors = [
            E_ERROR => 'E_ERROR',
            E_WARNING => 'E_WARNING',
            E_PARSE => 'E_PARSE',
            E_NOTICE => 'E_NOTICE',
            E_CORE_ERROR => 'E_CORE_ERROR',
            E_CORE_WARNING => 'E_CORE_WARNING',
            E_COMPILE_ERROR => 'E_COMPILE_ERROR',
            E_COMPILE_WARNING => 'E_COMPILE_WARNING',
            E_USER_ERROR => 'E_USER_ERROR',
            E_USER_WARNING => 'E_USER_WARNING',
            E_USER_NOTICE => 'E_USER_NOTICE',
            E_STRICT => 'E_STRICT',
            E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
            E_DEPRECATED => 'E_DEPRECATED',
            E_USER_DEPRECATED => 'E_USER_DEPRECATED',
        ];

        return $errors[$errno] ?? 'UNKNOWN_ERROR';
    }

    /**
     * Rotationner les logs si trop volumineux
     */
    private function rotateLogs($logFile)
    {
        if (!file_exists($logFile)) {
            return;
        }

        $fileSize = filesize($logFile);

        if ($fileSize > self::MAX_LOG_SIZE) {
            $timestamp = date('Y-m-d_H-i-s');
            $backupFile = $logFile . '.' . $timestamp;

            // Renommer le fichier
            rename($logFile, $backupFile);

            // Compresser l'ancien log
            if (extension_loaded('zlib')) {
                $compressed = gzcompress(file_get_contents($backupFile), 9);
                file_put_contents($backupFile . '.gz', $compressed);
                unlink($backupFile);
            }
        }

        // Nettoyer les vieux logs
        $this->cleanOldLogs();
    }

    /**
     * Nettoyer les logs archivés anciens
     */
    private function cleanOldLogs()
    {
        $cutoffDate = time() - (self::ARCHIVE_DAYS * 86400);

        foreach (glob(self::LOG_DIR . '*.log.*') as $logFile) {
            if (filemtime($logFile) < $cutoffDate) {
                unlink($logFile);
            }
        }
    }

    /**
     * Obtenir les logs récents
     */
    public function getRecentLogs($level = null, $days = 7, $limit = 100)
    {
        $logs = [];
        $count = 0;

        for ($i = 0; $i < $days; $i++) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $pattern = $level ? self::LOG_DIR . strtolower($level) . "_$date.log" : self::LOG_DIR . "*_$date.log";

            foreach (glob($pattern) as $logFile) {
                $lines = file($logFile);

                // Prendre les dernières lignes
                $lines = array_reverse($lines);

                foreach ($lines as $line) {
                    if ($count >= $limit) {
                        break 3;
                    }

                    $logs[] = trim($line);
                    $count++;
                }
            }
        }

        return array_reverse($logs);
    }

    /**
     * Obtenir les statistiques des logs
     */
    public function getLogStats()
    {
        $stats = [
            'total_size' => 0,
            'total_files' => 0,
            'by_level' => []
        ];

        foreach (glob(self::LOG_DIR . '*.log*') as $logFile) {
            $stats['total_size'] += filesize($logFile);
            $stats['total_files']++;

            // Extraire le niveau du nom du fichier
            if (preg_match('/^(fatal|error|warning|info|debug)_/', basename($logFile), $matches)) {
                $level = $matches[1];
                if (!isset($stats['by_level'][$level])) {
                    $stats['by_level'][$level] = 0;
                }
                $stats['by_level'][$level]++;
            }
        }

        $stats['total_size_mb'] = round($stats['total_size'] / 1048576, 2);

        return $stats;
    }

    /**
     * Exporter les logs
     */
    public function exportLogs($outputFile, $days = 7)
    {
        $handle = fopen($outputFile, 'w');

        if (!$handle) {
            throw new Exception("Impossible de créer le fichier d'export: $outputFile");
        }

        fwrite($handle, "═════════════════════════════════════════════════════════════\n");
        fwrite($handle, "EXPORT DES LOGS\n");
        fwrite($handle, "Date: " . date('Y-m-d H:i:s') . "\n");
        fwrite($handle, "Période: " . $days . " jours\n");
        fwrite($handle, "═════════════════════════════════════════════════════════════\n\n");

        for ($i = 0; $i < $days; $i++) {
            $date = date('Y-m-d', strtotime("-$i days"));

            foreach (glob(self::LOG_DIR . "*_$date.log") as $logFile) {
                $level = strtoupper(basename($logFile, '_' . $date . '.log'));

                fwrite($handle, "\n\n");
                fwrite($handle, "───────────────────────────────────────────────────────────\n");
                fwrite($handle, "NIVEAU: " . $level . " | DATE: $date\n");
                fwrite($handle, "───────────────────────────────────────────────────────────\n");
                fwrite($handle, file_get_contents($logFile));
            }
        }

        fclose($handle);
        return true;
    }

    /**
     * Nettoyer tous les logs
     */
    public function clearAllLogs()
    {
        foreach (glob(self::LOG_DIR . '*.log*') as $logFile) {
            unlink($logFile);
        }
    }

    /**
     * Obtenir le chemin du répertoire des logs
     */
    public static function getLogDir()
    {
        return self::LOG_DIR;
    }
}
