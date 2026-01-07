<?php

/**
 * Classe de gestion sécurisée des variables d'environnement
 * 
 * Usage:
 * $dbHost = EnvManager::get('DB_HOST', 'localhost');
 * $isProduction = EnvManager::getBoolean('APP_DEBUG', false);
 */
class EnvManager
{
    /**
     * Récupérer une variable d'environnement
     * @param string $key Clé de la variable
     * @param mixed $default Valeur par défaut si la clé n'existe pas
     * @return mixed La valeur ou la valeur par défaut
     */
    public static function get($key, $default = null)
    {
        $value = $_ENV[$key] ?? getenv($key) ?? $default;

        if ($value === null) {
            trigger_error("Variable d'environnement non définie: $key", E_USER_WARNING);
        }

        return $value;
    }

    /**
     * Récupérer une variable booléenne
     * @param string $key Clé de la variable
     * @param bool $default Valeur par défaut
     * @return bool La valeur booléenne
     */
    public static function getBoolean($key, $default = false)
    {
        $value = self::get($key, $default);
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Récupérer une variable entière
     * @param string $key Clé de la variable
     * @param int $default Valeur par défaut
     * @return int La valeur entière
     */
    public static function getInt($key, $default = 0)
    {
        $value = self::get($key, $default);
        return intval($value);
    }

    /**
     * Récupérer une variable avec une liste de valeurs autorisées
     * @param string $key Clé de la variable
     * @param array $allowed Valeurs autorisées
     * @param mixed $default Valeur par défaut
     * @return mixed La valeur si autorisée, sinon la valeur par défaut
     */
    public static function getEnum($key, $allowed = [], $default = null)
    {
        $value = self::get($key, $default);

        if (!in_array($value, $allowed, true)) {
            trigger_error("Valeur non autorisée pour $key: $value", E_USER_WARNING);
            return $default;
        }

        return $value;
    }

    /**
     * Vérifier si une variable d'environnement existe
     * @param string $key Clé de la variable
     * @return bool
     */
    public static function exists($key)
    {
        return isset($_ENV[$key]) || getenv($key) !== false;
    }

    /**
     * Obtenir toutes les variables d'environnement chargées
     * @return array
     */
    public static function all()
    {
        return $_ENV;
    }
}
