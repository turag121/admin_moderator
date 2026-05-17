<?php
// AnnouncementController - Handles announcement actions
require_once __DIR__ . '/../models/AnnouncementModel.php';

class AnnouncementController {
    private $announcement;

    public function __construct() {
        $this->announcement = new AnnouncementModel();
    }

    public function createAnnouncement($title, $content) {
        return $this->announcement->createAnnouncement($_SESSION['user_id'], $title, $content);
    }

    public function getAnnouncements() {
        return $this->announcement->getAnnouncements();
    }

    public function deactivateAnnouncement($announcement_id) {
        return $this->announcement->deactivateAnnouncement($announcement_id);
    }
}
?>
