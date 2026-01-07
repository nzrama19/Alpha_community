<?php

/**
 * Classe de validation et de sécurité des entrées utilisateur
 * 
 * Usage:
 * $validator = new InputValidator();
 * $email = $validator->email($_POST['email']);
 * $username = $validator->alphanumeric($_POST['username'], 3, 20);
 */
class InputValidator
{
    private $errors = [];

    /**
     * Valider et nettoyer une adresse email
     * @param string $email Email à valider
     * @return string|false Email valide ou false
     */
    public function email($email)
    {
        $email = trim($email);
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $email;
        }
        $this->addError("Email invalide: $email");
        return false;
    }

    /**
     * Valider une chaîne alphanumérique
     * @param string $value Valeur à valider
     * @param int $minLength Longueur minimale
     * @param int $maxLength Longueur maximale
     * @return string|false
     */
    public function alphanumeric($value, $minLength = 1, $maxLength = 255)
    {
        $value = trim($value);
        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $value)) {
            $this->addError("Caractères non autorisés");
            return false;
        }
        if (strlen($value) < $minLength || strlen($value) > $maxLength) {
            $this->addError("Longueur invalide (min: $minLength, max: $maxLength)");
            return false;
        }
        return $value;
    }

    /**
     * Valider une chaîne de texte
     * @param string $value Valeur à valider
     * @param int $minLength Longueur minimale
     * @param int $maxLength Longueur maximale
     * @return string|false
     */
    public function text($value, $minLength = 1, $maxLength = 500)
    {
        $value = trim($value);
        if (strlen($value) < $minLength || strlen($value) > $maxLength) {
            $this->addError("Longueur invalide (min: $minLength, max: $maxLength)");
            return false;
        }
        return $value;
    }

    /**
     * Valider un nombre entier
     * @param mixed $value Valeur à valider
     * @param int $min Valeur minimale
     * @param int $max Valeur maximale
     * @return int|false
     */
    public function integer($value, $min = PHP_INT_MIN, $max = PHP_INT_MAX)
    {
        $value = filter_var($value, FILTER_VALIDATE_INT, [
            'options' => [
                'min_range' => $min,
                'max_range' => $max
            ]
        ]);

        if ($value === false) {
            $this->addError("Nombre invalide");
            return false;
        }
        return $value;
    }

    /**
     * Valider une URL
     * @param string $url URL à valider
     * @return string|false
     */
    public function url($url)
    {
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            return $url;
        }
        $this->addError("URL invalide");
        return false;
    }

    /**
     * Valider une date
     * @param string $date Date au format YYYY-MM-DD
     * @return string|false
     */
    public function date($date, $format = 'Y-m-d')
    {
        $d = DateTime::createFromFormat($format, $date);
        if ($d && $d->format($format) === $date) {
            return $date;
        }
        $this->addError("Date invalide");
        return false;
    }

    /**
     * Valider un fichier uploadé
     * @param array $file Fichier $_FILES
     * @param array $allowedTypes Types MIME autorisés
     * @param int $maxSize Taille maximale en octets
     * @return bool
     */
    public function file($file, $allowedTypes = ['image/jpeg', 'image/png'], $maxSize = 5242880)
    {
        if (!isset($file['tmp_name'])) {
            $this->addError("Aucun fichier uploadé");
            return false;
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $allowedTypes)) {
            $this->addError("Type de fichier non autorisé: $mimeType");
            return false;
        }

        if ($file['size'] > $maxSize) {
            $this->addError("Fichier trop volumineux");
            return false;
        }

        return true;
    }

    /**
     * Ajouter une erreur de validation
     * @param string $error Message d'erreur
     */
    private function addError($error)
    {
        $this->errors[] = $error;
    }

    /**
     * Obtenir toutes les erreurs
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Vérifier s'il y a des erreurs
     * @return bool
     */
    public function hasErrors()
    {
        return count($this->errors) > 0;
    }

    /**
     * Nettoyer une chaîne pour l'affichage HTML
     * @param string $value Valeur à nettoyer
     * @return string
     */
    public static function escape($value)
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Nettoyer une chaîne pour usage SQL (préparation à l'avance)
     * @param string $value Valeur à nettoyer
     * @return string
     */
    public static function escapeSql($value)
    {
        return addslashes($value);
    }
}
