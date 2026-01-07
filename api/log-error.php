<?php

/**
 * API pour enregistrer les erreurs JavaScript côté client
 * Endpoint: /api/log-error.php
 * 
 * Requête:
 * POST /api/log-error.php
 * Content-Type: application/json
 * 
 * {
 *   "type": "javascript",
 *   "message": "Error message",
 *   "filename": "file.js",
 *   "lineno": 42,
 *   "colno": 10,
 *   "stack": "Error stack trace"
 * }
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/AppErrorLogger.php';

// En-têtes de sécurité
header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');

// Vérifier la méthode
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Récupérer les données JSON
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON']);
    exit;
}

// Valider les champs requis
$required = ['type', 'message'];
foreach ($required as $field) {
    if (empty($input[$field])) {
        http_response_code(400);
        echo json_encode(['error' => "Missing field: $field"]);
        exit;
    }
}

try {
    // Enregistrer l'erreur
    if ($input['type'] === 'javascript') {
        AppErrorLogger::javascriptError(
            $input['message'],
            $input['filename'] ?? '',
            $input['lineno'] ?? 0,
            $input['colno'] ?? 0,
            $input['stack'] ?? ''
        );
    } else {
        // Autres types d'erreurs
        AppErrorLogger::log(
            strtoupper($input['type']),
            $input['message'],
            array_slice($input, 2) // Contexte supplémentaire
        );
    }

    http_response_code(200);
    echo json_encode(['success' => true, 'message' => 'Error logged successfully']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to log error']);
}
