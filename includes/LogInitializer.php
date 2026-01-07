<?php

/**
 * Initialisation automatique des logs et services
 * À inclure au début de config.php
 */

// S'assurer que les répertoires existent
$required_dirs = [
    __DIR__ . '/../logs',
    __DIR__ . '/../config/uploads',
];

foreach ($required_dirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }

    // S'assurer que le répertoire est accessible en écriture
    if (!is_writable($dir)) {
        @chmod($dir, 0755);
    }
}

// Initialiser les fichiers de logs s'ils n'existent pas
$log_files = [
    __DIR__ . '/../errors.log',
    __DIR__ . '/../php_errors.log',
];

foreach ($log_files as $logfile) {
    if (!file_exists($logfile)) {
        @touch($logfile);
        @chmod($logfile, 0644);
    }

    // S'assurer que le fichier est accessible en écriture
    if (!is_writable($logfile)) {
        @chmod($logfile, 0644);
    }
}

// Fonction helper pour logger les erreurs critiques
function logCriticalError($message, $context = [])
{
    $timestamp = date('Y-m-d H:i:s');
    $log_entry = "[$timestamp] [CRITICAL] $message";

    if (!empty($context)) {
        $log_entry .= " | " . json_encode($context, JSON_UNESCAPED_UNICODE);
    }

    $log_file = __DIR__ . '/../errors.log';
    @file_put_contents($log_file, $log_entry . "\n", FILE_APPEND | LOCK_EX);

    error_log($message);
}

// Enregistrer un message de démarrage
logCriticalError('Initialisation des services', [
    'php_version' => PHP_VERSION,
    'timestamp' => date('Y-m-d H:i:s'),
    'environment' => defined('APP_ENV') ? APP_ENV : 'unknown'
]);
