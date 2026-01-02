<?php
require_once __DIR__ . '/../config/config.php';

class Post
{
    private $conn;
    private $table = 'posts';

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Créer une nouvelle publication (par un admin ou un user)
    public function create($content, $media_type = 'none', $media_url = null, $admin_id = null, $user_id = null)
    {
        try {
            // Valider qu'on a au moins un auteur
            if (empty($admin_id) && empty($user_id)) {
                throw new Exception('Un auteur (admin ou user) est obligatoire');
            }

            // Convertir les IDs vides en NULL explicite
            $admin_id = empty($admin_id) ? null : intval($admin_id);
            $user_id = empty($user_id) ? null : intval($user_id);

            // Les publications des admins sont automatiquement approuvées
            $status = ($admin_id !== null) ? 'approved' : 'pending';

            $query = "INSERT INTO " . $this->table . " (content, media_type, media_url, admin_id, user_id, status)
                      VALUES (:content, :media_type, :media_url, :admin_id, :user_id, :status)";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':content', $content);
            $stmt->bindParam(':media_type', $media_type);
            $stmt->bindParam(':media_url', $media_url);
            $stmt->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':status', $status);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erreur lors de la création du post: " . $e->getMessage());
            throw $e;
        }
    }

    // Obtenir toutes les publications approuvées avec les infos admin et user
    public function getAll($limit = 50, $offset = 0)
    {
        $query = "SELECT p.*, 
                  COALESCE(a.username, u.username) as username,
                  COALESCE(a.photo, u.avatar) as avatar,
                  CASE WHEN p.admin_id IS NOT NULL AND p.admin_id != 0 THEN 'admin' ELSE 'user' END as author_type,
                  (SELECT COUNT(*) FROM likes WHERE post_id = p.id) as likes_count,
                  (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comments_count
                  FROM " . $this->table . " p
                  LEFT JOIN admins a ON p.admin_id = a.id AND p.admin_id IS NOT NULL
                  LEFT JOIN users u ON (p.admin_id IS NULL OR p.admin_id = 0) AND p.user_id = u.id
                  WHERE p.status = 'approved'
                  ORDER BY p.created_at DESC
                  LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // Obtenir les publications par type de média
    public function getByMediaType($mediaType, $limit = 50, $offset = 0)
    {
        // Construire la condition selon le type
        if ($mediaType === 'text') {
            $whereClause = "p.media_type = 'none'";
        } elseif ($mediaType === 'image') {
            $whereClause = "(p.media_type = 'image' OR (p.media_type = 'multiple' AND p.media_url LIKE '%\"type\":\"image\"%'))";
        } elseif ($mediaType === 'video') {
            $whereClause = "(p.media_type = 'video' OR (p.media_type = 'multiple' AND p.media_url LIKE '%\"type\":\"video\"%'))";
        } else {
            $whereClause = "1=1";
        }

        $query = "SELECT p.*, 
                  COALESCE(a.username, u.username) as username,
                  COALESCE(a.photo, u.avatar) as avatar,
                  CASE WHEN p.admin_id IS NOT NULL AND p.admin_id != 0 THEN 'admin' ELSE 'user' END as author_type,
                  (SELECT COUNT(*) FROM likes WHERE post_id = p.id) as likes_count,
                  (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comments_count
                  FROM " . $this->table . " p
                  LEFT JOIN admins a ON p.admin_id = a.id AND p.admin_id IS NOT NULL
                  LEFT JOIN users u ON (p.admin_id IS NULL OR p.admin_id = 0) AND p.user_id = u.id
                  WHERE p.status = 'approved' AND " . $whereClause . "
                  ORDER BY p.created_at DESC
                  LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // Compter les publications par type de média
    public function countByMediaType($mediaType)
    {
        if ($mediaType === 'text') {
            $whereClause = "media_type = 'none'";
        } elseif ($mediaType === 'image') {
            $whereClause = "(media_type = 'image' OR (media_type = 'multiple' AND media_url LIKE '%\"type\":\"image\"%'))";
        } elseif ($mediaType === 'video') {
            $whereClause = "(media_type = 'video' OR (media_type = 'multiple' AND media_url LIKE '%\"type\":\"video\"%'))";
        } else {
            $whereClause = "1=1";
        }

        $query = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE status = 'approved' AND " . $whereClause;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'];
    }

    // Obtenir une publication par ID
    public function getById($id)
    {
        $query = "SELECT p.*, 
                  COALESCE(a.username, u.username) as username,
                  COALESCE(a.photo, u.avatar) as avatar,
                  CASE WHEN p.admin_id IS NOT NULL AND p.admin_id != 0 THEN 'admin' ELSE 'user' END as author_type,
                  (SELECT COUNT(*) FROM likes WHERE post_id = p.id) as likes_count,
                  (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comments_count
                  FROM " . $this->table . " p
                  LEFT JOIN admins a ON p.admin_id = a.id AND p.admin_id IS NOT NULL
                  LEFT JOIN users u ON (p.admin_id IS NULL OR p.admin_id = 0) AND p.user_id = u.id
                  WHERE p.id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch();
    }

    // Supprimer une publication
    public function delete($id, $user_id = null)
    {
        // Si user_id est fourni, vérifier que c'est le propriétaire
        if ($user_id !== null) {
            $query = "DELETE FROM " . $this->table . " WHERE id = :id AND user_id = :user_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':user_id', $user_id);
        } else {
            // Admin peut supprimer n'importe quel post
            $query = "DELETE FROM " . $this->table . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
        }

        return $stmt->execute();
    }

    // Mettre à jour le contenu d'une publication
    public function update($id, $content, $user_id = null, $media_type = null, $media_url = null)
    {
        // Si user_id est fourni, vérifier que c'est le propriétaire
        $query = "UPDATE " . $this->table . " SET content = :content";

        // Ajouter les médias si fournis
        if ($media_type !== null) {
            $query .= ", media_type = :media_type, media_url = :media_url";
        }

        $query .= " WHERE id = :id";

        if ($user_id !== null) {
            $query .= " AND user_id = :user_id";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':content', $content);

        if ($media_type !== null) {
            $stmt->bindParam(':media_type', $media_type);
            $stmt->bindParam(':media_url', $media_url);
        }

        if ($user_id !== null) {
            $stmt->bindParam(':user_id', $user_id);
        }

        return $stmt->execute();
    }

    // Obtenir les posts d'un utilisateur (tous les statuts pour voir ses propres posts)
    public function getUserPosts($user_id, $limit = 50, $offset = 0)
    {
        $query = "SELECT p.*,
                  u.username, u.avatar, 'user' as author_type,
                  (SELECT COUNT(*) FROM likes WHERE post_id = p.id) as likes_count,
                  (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comments_count
                  FROM " . $this->table . " p
                  INNER JOIN users u ON p.user_id = u.id
                  WHERE p.user_id = :user_id
                  ORDER BY p.created_at DESC
                  LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // Compter les posts d'un utilisateur
    public function getUserPostCount($user_id)
    {
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'];
    }

    // Obtenir les posts d'un admin
    public function getAdminPosts($admin_id, $limit = 50, $offset = 0)
    {
        $query = "SELECT p.*,
                  a.username, a.photo as avatar, 'admin' as author_type,
                  (SELECT COUNT(*) FROM likes WHERE post_id = p.id) as likes_count,
                  (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comments_count
                  FROM " . $this->table . " p
                  INNER JOIN admins a ON p.admin_id = a.id
                  WHERE p.admin_id = :admin_id
                  ORDER BY p.created_at DESC
                  LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // Compter les posts d'un admin
    public function getAdminPostCount($admin_id)
    {
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE admin_id = :admin_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':admin_id', $admin_id);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'];
    }

    // Vérifier si un utilisateur a liké une publication
    public function hasUserLiked($post_id, $user_id)
    {
        $query = "SELECT id FROM likes WHERE post_id = :post_id AND user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':post_id', $post_id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        return $stmt->fetch() !== false;
    }

    // ===== MÉTHODES DE MODÉRATION =====

    // Obtenir les publications en attente de modération
    public function getPendingPosts($limit = 50, $offset = 0)
    {
        $query = "SELECT p.*, 
                  COALESCE(a.username, u.username) as username,
                  COALESCE(a.photo, u.avatar) as avatar,
                  CASE WHEN p.admin_id IS NOT NULL AND p.admin_id != 0 THEN 'admin' ELSE 'user' END as author_type,
                  (SELECT COUNT(*) FROM likes WHERE post_id = p.id) as likes_count,
                  (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comments_count
                  FROM " . $this->table . " p
                  LEFT JOIN admins a ON p.admin_id = a.id AND p.admin_id IS NOT NULL
                  LEFT JOIN users u ON (p.admin_id IS NULL OR p.admin_id = 0) AND p.user_id = u.id
                  WHERE p.status = 'pending'
                  ORDER BY p.created_at ASC
                  LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // Compter les publications en attente
    public function getPendingCount()
    {
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE status = 'pending'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'];
    }

    // Obtenir les publications par statut
    public function getByStatus($status, $limit = 50, $offset = 0)
    {
        $query = "SELECT p.*, 
                  COALESCE(a.username, u.username) as username,
                  COALESCE(a.photo, u.avatar) as avatar,
                  CASE WHEN p.admin_id IS NOT NULL AND p.admin_id != 0 THEN 'admin' ELSE 'user' END as author_type,
                  (SELECT COUNT(*) FROM likes WHERE post_id = p.id) as likes_count,
                  (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comments_count
                  FROM " . $this->table . " p
                  LEFT JOIN admins a ON p.admin_id = a.id AND p.admin_id IS NOT NULL
                  LEFT JOIN users u ON (p.admin_id IS NULL OR p.admin_id = 0) AND p.user_id = u.id
                  WHERE p.status = :status
                  ORDER BY p.created_at DESC
                  LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // Approuver une publication
    public function approve($id)
    {
        $query = "UPDATE " . $this->table . " SET status = 'approved' WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Rejeter une publication
    public function reject($id)
    {
        $query = "UPDATE " . $this->table . " SET status = 'rejected' WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Changer le statut d'une publication
    public function updateStatus($id, $status)
    {
        $validStatuses = ['pending', 'approved', 'rejected'];
        if (!in_array($status, $validStatuses)) {
            return false;
        }

        $query = "UPDATE " . $this->table . " SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':status', $status);
        return $stmt->execute();
    }

    // Compter par statut
    public function countByStatus($status)
    {
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE status = :status";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'];
    }

    // Obtenir toutes les publications (pour l'admin, tous statuts)
    public function getAllForAdmin($limit = 50, $offset = 0)
    {
        $query = "SELECT p.*, 
                  COALESCE(a.username, u.username) as username,
                  COALESCE(a.photo, u.avatar) as avatar,
                  CASE WHEN p.admin_id IS NOT NULL AND p.admin_id != 0 THEN 'admin' ELSE 'user' END as author_type,
                  (SELECT COUNT(*) FROM likes WHERE post_id = p.id) as likes_count,
                  (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comments_count
                  FROM " . $this->table . " p
                  LEFT JOIN admins a ON p.admin_id = a.id AND p.admin_id IS NOT NULL
                  LEFT JOIN users u ON (p.admin_id IS NULL OR p.admin_id = 0) AND p.user_id = u.id
                  ORDER BY p.created_at DESC
                  LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
