<?php
// SellerController - Handles seller verification actions
require_once __DIR__ . '/../models/SellerModel.php';

class SellerController {
    private $seller;

    public function __construct() {
        $this->seller = new SellerModel();
    }

    public function getPendingVerifications($limit = 50, $offset = 0) {
        return $this->seller->getPendingVerifications($limit, $offset);
    }

    public function approveVerification($verification_id) {
        return $this->seller->approveVerification($verification_id, $_SESSION['user_id']);
    }

    public function rejectVerification($verification_id) {
        return $this->seller->rejectVerification($verification_id, $_SESSION['user_id']);
    }

    public function revokeSellerVerification($user_id) {
        return $this->seller->revokeSellerVerification($user_id, $_SESSION['user_id']);
    }
}
?>
