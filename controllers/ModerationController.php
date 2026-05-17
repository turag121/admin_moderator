<?php
// ModerationController - Handles moderation and report actions
require_once __DIR__ . '/../models/ModerationModel.php';

class ModerationController {
    private $moderation;

    public function __construct() {
        $this->moderation = new ModerationModel();
    }

    public function getModerationSummary() {
        return $this->moderation->getModerationSummary();
    }

    public function getEscalatedUserReports($limit = 50, $offset = 0) {
        return $this->moderation->getEscalatedUserReports($limit, $offset);
    }

    public function deactivateUserFromReport($user_id, $report_id) {
        return $this->moderation->deactivateUserFromReport($user_id, $report_id);
    }

    public function clearUserReport($report_id) {
        return $this->moderation->clearUserReport($report_id);
    }
}
?>
