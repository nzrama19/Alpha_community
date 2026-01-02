<?php
require_once __DIR__ . '/../config/config.php';

class Like
{
    private $conn;
    private $table = 'likes';

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Ajouter un like
    public function add($post_id, $user_id)
    {
        $query = "INSERT INTO " . $this->table . " (post_id, user_id) 
                  VALUES (:post_id, :user_id)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
        // user_id peut être numérique (utilisateur) ou string (anonyme hash)
        $stmt->bindParam(':user_id', $user_id);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            // Si le like existe déjà (erreur de clé unique), retourner false
            return false;
        }
    }

    // Retirer un like
    public function remove($post_id, $user_id)
    {
        $query = "DELETE FROM " . $this->table . " 
                  WHERE post_id = :post_id AND user_id = :user_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':post_id', $post_id);
        $stmt->bindParam(':user_id', $user_id);

        return $stmt->execute();
    }

    // Toggle like (ajouter ou retirer)
    public function toggle($post_id, $user_id)
    {
        // Vérifier si le like existe
        if ($this->exists($post_id, $user_id)) {
            return $this->remove($post_id, $user_id);
        } else {
            return $this->add($post_id, $user_id);
        }
    }

    // Vérifier si un like existe
    public function exists($post_id, $user_id)
    {
        $query = "SELECT id FROM " . $this->table . " 
                  WHERE post_id = :post_id AND user_id = :user_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':post_id', $post_id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        return $stmt->fetch() !== false;
    }

    // Compter les likes d'une publication
    public function countByPostId($post_id)
    {
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE post_id = :post_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':post_id', $post_id);
        $stmt->execute();

        $result = $stmt->fetch();
        return $result['total'];
    }

    // Obtenir tous les utilisateurs qui ont liké une publication
    public function getUsersByPostId($post_id)
    {
        $query = "SELECT u.id, u.username, u.avatar
                  FROM " . $this->table . " l
                  INNER JOIN users u ON l.user_id = u.id
                  WHERE l.post_id = :post_id
                  ORDER BY l.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':post_id', $post_id);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
