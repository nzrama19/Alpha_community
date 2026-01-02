<?php

/**
 * Logique métier pour contact.php
 */

require_once __DIR__ . '/../config/config.php';

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error_message = 'Tous les champs sont obligatoires.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Adresse email invalide.';
    } else {
        // Ici, vous pouvez envoyer l'email ou sauvegarder dans la base de données
        // Pour l'instant, on simule juste un succès
        $success_message = 'Votre message a été envoyé avec succès ! Nous vous répondrons dans les plus brefs délais.';

        // Réinitialiser les champs après succès
        $name = $email = $subject = $message = '';
    }
}
