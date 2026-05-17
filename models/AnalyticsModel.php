<?php
// AnalyticsModel - Handles platform analytics database operations
require_once __DIR__ . '/../config/Database.php';

class AnalyticsModel {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

    // Get platform analytics data
    public function getPlatformAnalytics() {
        $analytics = [];

        // Bidding activity for last 30 days
        $query = "SELECT DATE(created_at) as date, COUNT(*) as bid_count
                  FROM bids
                  WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                  GROUP BY DATE(created_at)
                  ORDER BY date DESC";
        $result = $this->conn->query($query);
        $analytics['bidding_activity'] = $result->fetch_all(MYSQLI_ASSOC);

        // Average auction duration
        $query = "SELECT AVG(TIMESTAMPDIFF(HOUR, created_at, end_datetime)) as avg_duration
                  FROM listings
                  WHERE status IN ('ended', 'cancelled')";
        $result = $this->conn->query($query);
        $row = $result->fetch_assoc();
        $analytics['avg_duration'] = $row['avg_duration'] ?? 0;

        // Sell-through rate
        $query = "SELECT
                    (SELECT COUNT(*) FROM listings WHERE status = 'ended') as completed_sales,
                    (SELECT COUNT(*) FROM listings) as total_listings";
        $result = $this->conn->query($query);
        $row = $result->fetch_assoc();
        $analytics['sell_through_rate'] = $row['total_listings'] > 0 ?
            ($row['completed_sales'] / $row['total_listings'] * 100) : 0;

        // Top buyers
        $query = "SELECT u.id, u.name, u.email, COUNT(b.id) as total_bids
                  FROM bids b
                  JOIN users u ON b.bidder_id = u.id
                  GROUP BY u.id
                  ORDER BY total_bids DESC
                  LIMIT 10";
        $result = $this->conn->query($query);
        $analytics['top_buyers'] = $result->fetch_all(MYSQLI_ASSOC);

        // Top sellers
        $query = "SELECT u.id, u.name, u.email, COUNT(l.id) as total_listings
                  FROM listings l
                  JOIN users u ON l.seller_id = u.id
                  WHERE l.status = 'ended'
                  GROUP BY u.id
                  ORDER BY total_listings DESC
                  LIMIT 10";
        $result = $this->conn->query($query);
        $analytics['top_sellers'] = $result->fetch_all(MYSQLI_ASSOC);

        return $analytics;
    }

    // Get dashboard stats
    public function getDashboardStats() {
        $stats = [];

        // Count users by role - single query instead of multiple
        $query = "SELECT role, COUNT(*) as count FROM users GROUP BY role";
        $result = $this->conn->query($query);
        while ($row = $result->fetch_assoc()) {
            $stats['users_' . $row['role']] = $row['count'];
        }

        // Total active listings
        $result = $this->conn->query("SELECT COUNT(*) as count FROM listings WHERE status = 'active'");
        $stats['active_listings'] = $result->fetch_assoc()['count'];

        // Total bids today
        $result = $this->conn->query("SELECT COUNT(*) as count FROM bids WHERE DATE(created_at) = CURDATE()");
        $stats['bids_today'] = $result->fetch_assoc()['count'];

        // Total platform commission this month
        $result = $this->conn->query("SELECT SUM(commission_amount) as total FROM platform_fees WHERE MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())");
        $row = $result->fetch_assoc();
        $stats['commission_this_month'] = $row['total'] ?? 0;

        // Pending seller verification requests
        $result = $this->conn->query("SELECT COUNT(*) as count FROM seller_verification_requests WHERE status = 'pending'");
        $stats['pending_verifications'] = $result->fetch_assoc()['count'];

        return $stats;
    }
}
?>
