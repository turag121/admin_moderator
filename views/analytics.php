<?php
require_once __DIR__ . '/../config/Session.php';
require_once __DIR__ . '/../controllers/AnalyticsController.php';

AdminSession::requireAdmin();

$analyticsController = new AnalyticsController();
$analytics = $analyticsController->getPlatformAnalytics();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Platform Analytics - Admin</title>
    <link rel="stylesheet" href="/auction-platform/public/css/style.css">
</head>
<body class="admin-page">
    <nav class="navbar">
        <div class="container">
            <div class="nav-brand">Auction Platform Admin</div>
            <div class="nav-menu">
                <a href="/auction-platform/views/dashboard.php" class="nav-link">Dashboard</a>
                <a href="/auction-platform/views/verifications.php" class="nav-link">Seller Verifications</a>
                <a href="/auction-platform/views/users.php" class="nav-link">Manage Users</a>
                <a href="/auction-platform/views/listings.php" class="nav-link">Manage Listings</a>
                <a href="/auction-platform/views/commission.php" class="nav-link">Commission Rates</a>
                <a href="/auction-platform/views/reports.php" class="nav-link">Financial Reports</a>
                <a href="/auction-platform/views/analytics.php" class="nav-link active">Analytics</a>
                <a href="/auction-platform/public/logout.php" class="nav-link logout">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container admin-container">
        <div class="page-header">
            <h1>Platform Analytics</h1>
            <p>Activity trends and user engagement metrics</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <!-- <div class="stat-icon">📊</div> -->
                <div class="stat-content">
                    <div class="stat-value"><?php echo number_format($analytics['avg_auction_duration'], 1); ?></div>
                    <div class="stat-label">Avg Auction Duration (days)</div>
                </div>
            </div>

            <div class="stat-card">
                <!-- <div class="stat-icon">📈</div> -->
                <div class="stat-content">
                    <div class="stat-value"><?php echo number_format($analytics['sell_through_rate'], 1); ?>%</div>
                    <div class="stat-label">Sell-Through Rate</div>
                </div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
            <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <h2 style="color: #2c3e50; margin-bottom: 15px;">Most Active Buyers</h2>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Buyer</th>
                                <th>Bids Placed</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($analytics['top_buyers'])): ?>
                                <?php foreach ($analytics['top_buyers'] as $buyer): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($buyer['name']); ?></td>
                                        <td><?php echo $buyer['bid_count']; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="2" style="text-align: center;">No buyer data available</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <h2 style="color: #2c3e50; margin-bottom: 15px;">Most Active Sellers</h2>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Seller</th>
                                <th>Listings</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($analytics['top_sellers'])): ?>
                                <?php foreach ($analytics['top_sellers'] as $seller): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($seller['name']); ?></td>
                                        <td><?php echo $seller['listing_count']; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="2" style="text-align: center;">No seller data available</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h2 style="color: #2c3e50; margin-bottom: 15px;">Bidding Activity (Last 30 Days)</h2>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Number of Bids</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($analytics['bids_per_day'])): ?>
                            <?php foreach ($analytics['bids_per_day'] as $day): ?>
                                <tr>
                                    <td><?php echo date('M d, Y', strtotime($day['date'])); ?></td>
                                    <td><?php echo $day['bid_count']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="2" style="text-align: center;">No bidding data available</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="/public/js/main.js"></script>
</body>
</html>
