<?php
require_once __DIR__ . '/../config/Session.php';

AdminSession::destroy();
header('Location: /auction-platform/?message=You have been logged out');
exit;
?>

