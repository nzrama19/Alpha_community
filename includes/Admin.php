<?php
require_once __DIR__ . '/../config/config.php';

class Admin
{
    private $conn;
    private $table = 'admins';

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Créer un nouvel administrateur
    public function create($username, $email, $password)
    {
        $query = "INSERT INTO " . $this->table . " (username, email, password) 
                  VALUES (:username, :email, :password)";

        $stmt = $this->conn->prepare($query);

        $password_hash = password_hash($password, PASSWORD_BCRYPT);

        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password_hash);

        return $stmt->execute();
    }

    // Obtenir un admin par username
    public function getByUsername($username)
    {
        $query = "SELECT id, username, email, password, photo, created_at 
                  FROM " . $this->table . " 
                  WHERE username = :username";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        return $stmt->fetch();
    }

    // Obtenir un admin par ID
    public function getById($id)
    {
        $query = "SELECT id, username, email, photo, created_at 
                  FROM " . $this->table . " 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch();
    }

    // Vérifier les identifiants
    public function authenticate($username, $password)
    {
        $admin = $this->getByUsername($username);

        if ($admin && password_verify($password, $admin['password'])) {
            return $admin;
        }

        return false;
    }

    // Mettre à jour le profil
    public function updateProfile($id, $data)
    {
        $fields = [];
        $params = [':id' => $id];

        if (isset($data['username'])) {
            $fields[] = "username = :username";
            $params[':username'] = $data['username'];
        }

        if (isset($data['email'])) {
            $fields[] = "email = :email";
            $params[':email'] = $data['email'];
        }

        if (isset($data['photo'])) {
            $fields[] = "photo = :photo";
            $params[':photo'] = $data['photo'];
        }

        if (empty($fields)) {
            return false;
        }

        $query = "UPDATE " . $this->table . " SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        return $stmt->execute($params);
    }

    // Changer le mot de passe
    public function changePassword($id, $new_password)
    {
        $query = "UPDATE " . $this->table . " SET password = :password WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $password_hash = password_hash($new_password, PASSWORD_BCRYPT);
        $stmt->bindParam(':password', $password_hash);
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }

    // Vérifier si l'email existe déjà (pour un autre admin)
    public function emailExists($email, $exclude_id = null)
    {
        $query = "SELECT id FROM " . $this->table . " WHERE email = :email";

        if ($exclude_id) {
            $query .= " AND id != :exclude_id";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);

        if ($exclude_id) {
            $stmt->bindParam(':exclude_id', $exclude_id);
        }

        $stmt->execute();
        return $stmt->fetch() ? true : false;
    }

    // Vérifier si le username existe déjà (pour un autre admin)
    public function usernameExists($username, $exclude_id = null)
    {
        $query = "SELECT id FROM " . $this->table . " WHERE username = :username";

        if ($exclude_id) {
            $query .= " AND id != :exclude_id";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);

        if ($exclude_id) {
            $stmt->bindParam(':exclude_id', $exclude_id);
        }

        $stmt->execute();
        return $stmt->fetch() ? true : false;
    }
}
