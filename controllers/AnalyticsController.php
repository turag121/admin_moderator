<?php
// AnalyticsController - Handles analytics and dashboard actions
require_once __DIR__ . '/../models/AnalyticsModel.php';

class AnalyticsController {
    private $analytics;

    public function __construct() {
        $this->analytics = new AnalyticsModel();
    }

    public function getPlatformAnalytics() {
        return $this->analytics->getPlatformAnalytics();
    }

    public function getDashboardStats() {
        return $this->analytics->getDashboardStats();
    }
}
?>
