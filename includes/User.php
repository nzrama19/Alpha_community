<?php
require_once __DIR__ . '/../config/config.php';

class User
{
    private $conn;
    private $table = 'users';

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Créer un nouvel utilisateur
    public function create($username, $email, $password)
    {
        $query = "INSERT INTO " . $this->table . " (username, email, password) 
                  VALUES (:username, :email, :password)";

        $stmt = $this->conn->prepare($query);

        $password_hash = password_hash($password, PASSWORD_BCRYPT);

        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password_hash);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }

        return false;
    }

    // Obtenir un utilisateur par username
    public function getByUsername($username)
    {
        $query = "SELECT id, username, email, password, avatar, created_at 
                  FROM " . $this->table . " 
                  WHERE username = :username";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        return $stmt->fetch();
    }

    // Obtenir un utilisateur par ID
    public function getById($id)
    {
        $query = "SELECT id, username, email, avatar, created_at 
                  FROM " . $this->table . " 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch();
    }

    // Obtenir un utilisateur par email
    public function getByEmail($email)
    {
        $query = "SELECT id, username, email, password, avatar, created_at 
                  FROM " . $this->table . " 
                  WHERE email = :email";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        return $stmt->fetch();
    }

    // Vérifier les identifiants
    public function authenticate($username, $password)
    {
        $user = $this->getByUsername($username);

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }

        return false;
    }

    // Obtenir tous les utilisateurs
    public function getAll($limit = 100, $offset = 0)
    {
        $query = "SELECT id, username, email, avatar, created_at 
                  FROM " . $this->table . " 
                  ORDER BY created_at DESC 
                  LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // Obtenir le nombre total d'utilisateurs
    public function getTotalCount()
    {
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'];
    }

    // Obtenir les détails d'un utilisateur avec ses statistiques
    public function getUserStats($user_id)
    {
        $query = "SELECT 
                    u.id, u.username, u.email, u.avatar, u.created_at,
                    COUNT(DISTINCT c.id) as comment_count,
                    COUNT(DISTINCT l.id) as like_count
                  FROM " . $this->table . " u
                  LEFT JOIN comments c ON u.id = c.user_id
                  LEFT JOIN likes l ON u.id = l.user_id
                  WHERE u.id = :user_id
                  GROUP BY u.id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        return $stmt->fetch();
    }

    // Obtenir tous les utilisateurs avec leurs statistiques
    public function getAllWithStats($limit = 100, $offset = 0)
    {
        $query = "SELECT 
                    u.id, u.username, u.email, u.avatar, u.created_at,
                    COUNT(DISTINCT c.id) as comment_count,
                    COUNT(DISTINCT l.id) as like_count
                  FROM " . $this->table . " u
                  LEFT JOIN comments c ON u.id = c.user_id
                  LEFT JOIN likes l ON u.id = l.user_id
                  GROUP BY u.id
                  ORDER BY u.created_at DESC 
                  LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // Supprimer un utilisateur (admin)
    public function delete($user_id)
    {
        try {
            // Commencer une transaction
            $this->conn->beginTransaction();

            // Supprimer les likes de l'utilisateur
            $query1 = "DELETE FROM likes WHERE user_id = :user_id";
            $stmt1 = $this->conn->prepare($query1);
            $stmt1->bindParam(':user_id', $user_id);
            $stmt1->execute();

            // Supprimer les commentaires de l'utilisateur
            $query2 = "DELETE FROM comments WHERE user_id = :user_id";
            $stmt2 = $this->conn->prepare($query2);
            $stmt2->bindParam(':user_id', $user_id);
            $stmt2->execute();

            // Supprimer l'utilisateur
            $query3 = "DELETE FROM " . $this->table . " WHERE id = :user_id";
            $stmt3 = $this->conn->prepare($query3);
            $stmt3->bindParam(':user_id', $user_id);
            $result = $stmt3->execute();

            // Valider la transaction
            $this->conn->commit();
            return $result;
        } catch (PDOException $e) {
            // Annuler la transaction en cas d'erreur
            $this->conn->rollBack();
            return false;
        }
    }

    // Obtenir les commentaires d'un utilisateur
    public function getUserComments($user_id)
    {
        $query = "SELECT c.id, c.post_id, c.content, c.created_at, p.content as post_content
                  FROM comments c
                  INNER JOIN posts p ON c.post_id = p.id
                  WHERE c.user_id = :user_id
                  ORDER BY c.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // Obtenir les likes d'un utilisateur
    public function getUserLikes($user_id)
    {
        $query = "SELECT l.id, l.post_id, l.created_at, p.content as post_content, a.username as author
                  FROM likes l
                  INNER JOIN posts p ON l.post_id = p.id
                  INNER JOIN admins a ON p.admin_id = a.id
                  WHERE l.user_id = :user_id
                  ORDER BY l.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // Mettre à jour le profil utilisateur
    public function updateProfile($user_id, $username, $email)
    {
        $query = "UPDATE " . $this->table . " SET username = :username, email = :email WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':id', $user_id);

        return $stmt->execute();
    }

    // Mettre à jour l'avatar
    public function updateAvatar($user_id, $avatar_path)
    {
        $query = "UPDATE " . $this->table . " SET avatar = :avatar WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':avatar', $avatar_path);
        $stmt->bindParam(':id', $user_id);

        return $stmt->execute();
    }

    // Changer le mot de passe
    public function changePassword($user_id, $new_password)
    {
        $query = "UPDATE " . $this->table . " SET password = :password WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $password_hash = password_hash($new_password, PASSWORD_BCRYPT);
        $stmt->bindParam(':password', $password_hash);
        $stmt->bindParam(':id', $user_id);

        return $stmt->execute();
    }

    // Vérifier si un email existe (sauf pour l'utilisateur courant)
    public function emailExists($email, $user_id = null)
    {
        $query = "SELECT id FROM " . $this->table . " WHERE email = :email";
        if ($user_id !== null) {
            $query .= " AND id != :user_id";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        if ($user_id !== null) {
            $stmt->bindParam(':user_id', $user_id);
        }
        $stmt->execute();

        return $stmt->fetch() !== false;
    }

    // Vérifier si un username existe (sauf pour l'utilisateur courant)
    public function usernameExists($username, $user_id = null)
    {
        $query = "SELECT id FROM " . $this->table . " WHERE username = :username";
        if ($user_id !== null) {
            $query .= " AND id != :user_id";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        if ($user_id !== null) {
            $stmt->bindParam(':user_id', $user_id);
        }
        $stmt->execute();

        return $stmt->fetch() !== false;
    }
}
