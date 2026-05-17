<?php
// SellerModel - Handles seller verification database operations
require_once __DIR__ . '/../config/Database.php';

class SellerModel {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

    // Get pending seller verifications
    public function getPendingVerifications($limit = 50, $offset = 0) {
        $stmt = $this->conn->prepare("
            SELECT svr.id, svr.user_id, svr.motivation, svr.id_document_path, svr.submitted_at,
                   u.name, u.email, u.phone
            FROM seller_verification_requests svr
            JOIN users u ON svr.user_id = u.id
            WHERE svr.status = 'pending'
            ORDER BY svr.submitted_at DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Get count of pending verifications
    public function getPendingVerificationsCount() {
        $result = $this->conn->query("SELECT COUNT(*) as count FROM seller_verification_requests WHERE status = 'pending'");
        return $result->fetch_assoc()['count'];
    }

    // Get verification details
    public function getVerificationDetails($verification_id) {
        $stmt = $this->conn->prepare("
            SELECT svr.*, u.name, u.email, u.phone
            FROM seller_verification_requests svr
            JOIN users u ON svr.user_id = u.id
            WHERE svr.id = ?
        ");
        $stmt->bind_param("i", $verification_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Approve seller verification
    public function approveVerification($verification_id, $admin_id) {
        $verification = $this->getVerificationDetails($verification_id);
        $user_id = $verification['user_id'];

        // Update verification request status
        $stmt = $this->conn->prepare("UPDATE seller_verification_requests SET status = 'approved', reviewed_by = ? WHERE id = ?");
        $stmt->bind_param("ii", $admin_id, $verification_id);
        $stmt->execute();

        // Update user role to seller
        $role = 'seller';
        $stmt = $this->conn->prepare("UPDATE users SET role = ?, seller_verified = TRUE WHERE id = ?");
        $stmt->bind_param("si", $role, $user_id);

        return $stmt->execute();
    }

    // Reject seller verification
    public function rejectVerification($verification_id, $admin_id) {
        $stmt = $this->conn->prepare("UPDATE seller_verification_requests SET status = 'rejected', reviewed_by = ? WHERE id = ?");
        $stmt->bind_param("ii", $admin_id, $verification_id);
        return $stmt->execute();
    }

    // Revoke seller verification
    public function revokeSellerVerification($user_id, $admin_id) {
        $stmt = $this->conn->prepare("UPDATE users SET seller_verified = FALSE, role = 'buyer' WHERE id = ? AND role = 'seller'");
        $stmt->bind_param("i", $user_id);
        return $stmt->execute();
    }
}
?>
