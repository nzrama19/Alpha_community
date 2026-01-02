<?php
require_once __DIR__ . '/../config/config.php';

class Stats
{
    private $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Obtenir les statistiques globales du site
    public function getGlobalStats()
    {
        $stats = [];

        // Total des utilisateurs
        $query1 = "SELECT COUNT(*) as total FROM users";
        $stmt1 = $this->conn->prepare($query1);
        $stmt1->execute();
        $result1 = $stmt1->fetch();
        $stats['total_users'] = $result1['total'];

        // Total des publications
        $query2 = "SELECT COUNT(*) as total FROM posts";
        $stmt2 = $this->conn->prepare($query2);
        $stmt2->execute();
        $result2 = $stmt2->fetch();
        $stats['total_posts'] = $result2['total'];

        // Total des commentaires
        $query3 = "SELECT COUNT(*) as total FROM comments";
        $stmt3 = $this->conn->prepare($query3);
        $stmt3->execute();
        $result3 = $stmt3->fetch();
        $stats['total_comments'] = $result3['total'];

        // Total des likes
        $query4 = "SELECT COUNT(*) as total FROM likes";
        $stmt4 = $this->conn->prepare($query4);
        $stmt4->execute();
        $result4 = $stmt4->fetch();
        $stats['total_likes'] = $result4['total'];

        // Utilisateurs inscrits ce mois
        $query5 = "SELECT COUNT(*) as total FROM users WHERE MONTH(created_at) = MONTH(NOW()) AND YEAR(created_at) = YEAR(NOW())";
        $stmt5 = $this->conn->prepare($query5);
        $stmt5->execute();
        $result5 = $stmt5->fetch();
        $stats['users_this_month'] = $result5['total'];

        // Publications ce mois
        $query6 = "SELECT COUNT(*) as total FROM posts WHERE MONTH(created_at) = MONTH(NOW()) AND YEAR(created_at) = YEAR(NOW())";
        $stmt6 = $this->conn->prepare($query6);
        $stmt6->execute();
        $result6 = $stmt6->fetch();
        $stats['posts_this_month'] = $result6['total'];

        // Utilisateur le plus actif (commentaires + likes)
        $query7 = "SELECT u.id, u.username, u.avatar, 
                    COUNT(DISTINCT c.id) as comment_count,
                    COUNT(DISTINCT l.id) as like_count,
                    (COUNT(DISTINCT c.id) + COUNT(DISTINCT l.id)) as total_activity
                   FROM users u
                   LEFT JOIN comments c ON u.id = c.user_id
                   LEFT JOIN likes l ON u.id = l.user_id
                   GROUP BY u.id
                   ORDER BY total_activity DESC
                   LIMIT 5";
        $stmt7 = $this->conn->prepare($query7);
        $stmt7->execute();
        $stats['top_users'] = $stmt7->fetchAll();

        // Publication la plus commentée
        $query8 = "SELECT p.id, p.content, p.user_id, u.username,
                    COUNT(c.id) as comment_count
                   FROM posts p
                   LEFT JOIN comments c ON p.id = c.post_id
                   LEFT JOIN users u ON p.user_id = u.id
                   GROUP BY p.id
                   ORDER BY comment_count DESC
                   LIMIT 5";
        $stmt8 = $this->conn->prepare($query8);
        $stmt8->execute();
        $stats['most_commented_posts'] = $stmt8->fetchAll();

        // Publication la plus likée
        $query9 = "SELECT p.id, p.content, p.user_id, u.username,
                    COUNT(l.id) as like_count
                   FROM posts p
                   LEFT JOIN likes l ON p.id = l.post_id
                   LEFT JOIN users u ON p.user_id = u.id
                   GROUP BY p.id
                   ORDER BY like_count DESC
                   LIMIT 5";
        $stmt9 = $this->conn->prepare($query9);
        $stmt9->execute();
        $stats['most_liked_posts'] = $stmt9->fetchAll();

        return $stats;
    }

    // Obtenir les utilisateurs récemment inscrits
    public function getRecentUsers($limit = 5)
    {
        $query = "SELECT id, username, email, avatar, created_at FROM users ORDER BY created_at DESC LIMIT :limit";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Obtenir les publications récentes
    public function getRecentPosts($limit = 5)
    {
        $query = "SELECT p.id, p.user_id, p.content, p.created_at, u.username,
                    COUNT(DISTINCT c.id) as comment_count,
                    COUNT(DISTINCT l.id) as like_count
                  FROM posts p
                  LEFT JOIN comments c ON p.id = c.post_id
                  LEFT JOIN likes l ON p.id = l.post_id
                  LEFT JOIN users u ON p.user_id = u.id
                  GROUP BY p.id
                  ORDER BY p.created_at DESC
                  LIMIT :limit";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Obtenir l'activité par jour de la semaine
    public function getActivityByDayOfWeek()
    {
        $query = "SELECT 
                    DAYNAME(created_at) as day,
                    DATE_FORMAT(created_at, '%w') as day_num,
                    COUNT(*) as activity_count
                  FROM posts
                  WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                  GROUP BY DATE(created_at)
                  ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
