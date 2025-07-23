<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use PDO;

class SessionMonitorService
{
    private PDO $db;

    public function __construct(Database $database)
    {
        $this->db = $database->getConnection();
    }

    public function trackUserActivity(int $userId, string $userName): void
    {
        $sessionId = session_id();
        $now = date('Y-m-d H:i:s');
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        $stmt = $this->db->prepare("SELECT id FROM user_sessions WHERE session_id = ?");
        $stmt->execute([$sessionId]);
        $existing = $stmt->fetch();

        if ($existing) {
            $stmt = $this->db->prepare(
                "UPDATE user_sessions SET 
                 last_activity = ?, 
                 page_count = page_count + 1,
                 current_page = ?
                 WHERE session_id = ?"
            );
            $stmt->execute([$now, $_SERVER['REQUEST_URI'] ?? '/', $sessionId]);
        } else {
            $stmt = $this->db->prepare(
                "INSERT INTO user_sessions (
                    user_id, user_name, session_id, ip_address, user_agent,
                    login_time, last_activity, current_page, page_count
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)"
            );
            $stmt->execute([$userId, $userName, $sessionId, $ipAddress, $userAgent, $now, $now, $_SERVER['REQUEST_URI'] ?? '/']);
        }

        $this->cleanExpiredSessions();
    }

    public function getActiveSessions(): array
    {
        $stmt = $this->db->prepare(
            "SELECT 
                user_id,
                user_name,
                session_id,
                ip_address,
                login_time,
                last_activity,
                current_page,
                page_count,
                TIMESTAMPDIFF(MINUTE, last_activity, NOW()) as minutes_inactive,
                TIMESTAMPDIFF(MINUTE, login_time, NOW()) as session_duration
             FROM user_sessions 
             WHERE TIMESTAMPDIFF(MINUTE, last_activity, NOW()) <= 30
             ORDER BY last_activity DESC"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getUserSessionStats(int $userId): array
    {
        $stmt = $this->db->prepare(
            "SELECT 
                COUNT(*) as total_sessions,
                MAX(login_time) as last_login,
                AVG(TIMESTAMPDIFF(MINUTE, login_time, last_activity)) as avg_session_duration,
                SUM(page_count) as total_page_views
             FROM user_sessions 
             WHERE user_id = ? AND DATE(login_time) >= DATE_SUB(NOW(), INTERVAL 30 DAY)"
        );
        $stmt->execute([$userId]);
        $stats = $stmt->fetch();

        return $stats ?: [
            'total_sessions' => 0,
            'last_login' => null,
            'avg_session_duration' => 0,
            'total_page_views' => 0
        ];
    }

    public function getSessionReport(): array
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(DISTINCT user_id) as count 
             FROM user_sessions 
             WHERE TIMESTAMPDIFF(MINUTE, last_activity, NOW()) <= 5"
        );
        $stmt->execute();
        $onlineUsers = $stmt->fetch()['count'];

        $stmt = $this->db->prepare(
            "SELECT COUNT(DISTINCT user_id) as count 
             FROM user_sessions 
             WHERE TIMESTAMPDIFF(MINUTE, last_activity, NOW()) <= 30"
        );
        $stmt->execute();
        $activeUsers = $stmt->fetch()['count'];

        $stmt = $this->db->prepare(
            "SELECT COUNT(*) as count 
             FROM user_sessions 
             WHERE DATE(login_time) = CURDATE()"
        );
        $stmt->execute();
        $todaySessions = $stmt->fetch()['count'];

        $stmt = $this->db->prepare(
            "SELECT 
                current_page, 
                COUNT(*) as visits,
                COUNT(DISTINCT user_id) as unique_users
             FROM user_sessions 
             WHERE DATE(last_activity) = CURDATE()
             GROUP BY current_page 
             ORDER BY visits DESC 
             LIMIT 10"
        );
        $stmt->execute();
        $topPages = $stmt->fetchAll();

        return [
            'online_users' => $onlineUsers,
            'active_users' => $activeUsers,
            'today_sessions' => $todaySessions,
            'top_pages' => $topPages
        ];
    }

    public function endUserSession(string $sessionId): bool
    {
        $stmt = $this->db->prepare("DELETE FROM user_sessions WHERE session_id = ?");
        return $stmt->execute([$sessionId]);
    }

    public function cleanExpiredSessions(): void
    {
        $stmt = $this->db->prepare(
            "DELETE FROM user_sessions 
             WHERE TIMESTAMPDIFF(MINUTE, last_activity, NOW()) > 30"
        );
        $stmt->execute();
    }

    public function createSessionTable(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS user_sessions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            user_name VARCHAR(255) NOT NULL,
            session_id VARCHAR(255) NOT NULL UNIQUE,
            ip_address VARCHAR(45) NOT NULL,
            user_agent TEXT,
            login_time DATETIME NOT NULL,
            last_activity DATETIME NOT NULL,
            current_page VARCHAR(500),
            page_count INT DEFAULT 1,
            INDEX idx_user_id (user_id),
            INDEX idx_session_id (session_id),
            INDEX idx_last_activity (last_activity)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
    }
}