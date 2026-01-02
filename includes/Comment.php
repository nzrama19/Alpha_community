<?php
require_once __DIR__ . '/../config/config.php';

class Comment
{
    private $conn;
    private $table = 'comments';

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Créer un nouveau commentaire
    public function create($post_id, $user_id, $content)
    {
        try {
            $query = "INSERT INTO " . $this->table . " (post_id, user_id, content) 
                      VALUES (:post_id, :user_id, :content)";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':content', $content);

            return $stmt->execute();
        } catch (PDOException $e) {
            // Log de l'erreur pour le débogage
            error_log("Erreur lors de la création du commentaire: " . $e->getMessage());
            throw $e;
        }
    }

    // Obtenir tous les commentaires d'une publication
    public function getByPostId($post_id)
    {
        $query = "SELECT c.*, u.username, u.avatar
                  FROM " . $this->table . " c
                  INNER JOIN users u ON c.user_id = u.id
                  WHERE c.post_id = :post_id
                  ORDER BY c.created_at ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':post_id', $post_id);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // Supprimer un commentaire
    public function delete($id, $user_id = null)
    {
        // Si user_id est fourni, vérifier que c'est le propriétaire
        if ($user_id !== null) {
            $query = "DELETE FROM " . $this->table . " WHERE id = :id AND user_id = :user_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':user_id', $user_id);
        } else {
            // Admin peut supprimer n'importe quel commentaire
            $query = "DELETE FROM " . $this->table . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
        }

        return $stmt->execute();
    }

    // Compter les commentaires d'une publication
    public function countByPostId($post_id)
    {
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE post_id = :post_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':post_id', $post_id);
        $stmt->execute();

        $result = $stmt->fetch();
        return $result['total'];
    }
}
