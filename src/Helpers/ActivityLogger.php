<?php
namespace App\Helpers;

use PDO;

class ActivityLogger {
    public static function log($userId, $action, $description = null) {
        try {
            $pdo = getDbConnection();
            $stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, action, description) VALUES (:user_id, :action, :description)");
            $stmt->execute([
                ':user_id' => $userId,
                ':action' => $action,
                ':description' => $description
            ]);
        } catch (\Throwable $e) {
            // Silently fail if logging fails
        }
    }
}
