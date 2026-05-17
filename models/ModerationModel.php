<?php
// ModerationModel - Handles moderation and report database operations
require_once __DIR__ . '/../config/Database.php';

class ModerationModel {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

    // Get moderation summary for all moderators
    public function getModerationSummary() {
        $result = $this->conn->query("
            SELECT u.id, u.name,
                   (SELECT COUNT(*) FROM listing_reports WHERE status = 'resolved') as listings_reviewed,
                   (SELECT COUNT(*) FROM user_reports WHERE status = 'resolved') as reports_resolved,
                   (SELECT COUNT(*) FROM warnings WHERE issued_by = u.id) as warnings_issued
            FROM users u
            WHERE u.role = 'moderator'
        ");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Get escalated user reports
    public function getEscalatedUserReports($limit = 50, $offset = 0) {
        $stmt = $this->conn->prepare("
            SELECT ur.*, ru.name as reporter_name, ru.email as reporter_email,
                   uu.name as reported_user_name, uu.email as reported_user_email
            FROM user_reports ur
            JOIN users ru ON ur.reporter_id = ru.id
            JOIN users uu ON ur.reported_user_id = uu.id
            WHERE ur.status = 'escalated'
            ORDER BY ur.created_at DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Deactivate user from report
    public function deactivateUserFromReport($user_id, $report_id) {
        $this->conn->begin_transaction();

        try {
            $stmt = $this->conn->prepare("UPDATE users SET is_active = FALSE WHERE id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();

            $status = 'resolved';
            $stmt = $this->conn->prepare("UPDATE user_reports SET status = ? WHERE id = ?");
            $stmt->bind_param("si", $status, $report_id);
            $stmt->execute();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }

    // Clear/resolve user report
    public function clearUserReport($report_id) {
        $status = 'resolved';
        $stmt = $this->conn->prepare("UPDATE user_reports SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $report_id);
        return $stmt->execute();
    }

    // Get report by ID
    public function getReportById($report_id) {
        $stmt = $this->conn->prepare("
            SELECT ur.*, ru.name as reporter_name, ru.email as reporter_email,
                   uu.name as reported_user_name, uu.email as reported_user_email
            FROM user_reports ur
            JOIN users ru ON ur.reporter_id = ru.id
            JOIN users uu ON ur.reported_user_id = uu.id
            WHERE ur.id = ?
        ");
        $stmt->bind_param("i", $report_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}
?>
