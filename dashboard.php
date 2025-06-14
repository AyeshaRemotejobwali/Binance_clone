<?php
require 'db.php';

// Enable error reporting for debugging (disable in production)
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    error_log('Session user_id not set, redirecting to login.php');
    echo "<script>window.location.href = 'login.php';</script>";
    exit;
}
$userId = $_SESSION['user_id'];

$wallets = [];
$transactions = [];

try {
    // Fetch wallet balances
    $stmt = $pdo->prepare("SELECT currency, balance, address FROM wallets WHERE user_id = ?");
    $stmt->execute([$userId]);
    $wallets = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // If no wallets exist, create a default BTC wallet
    if (empty($wallets)) {
        $defaultCurrency = 'BTC';
        $defaultAddress = '1A1zP' . bin2hex(random_bytes(16)); // Mock wallet address
        $defaultBalance = 0.0;

        $stmt = $pdo->prepare("INSERT INTO wallets (user_id, currency, address, balance) VALUES (?, ?, ?, ?)");
        $stmt->execute([$userId, $defaultCurrency, $defaultAddress, $defaultBalance]);

        // Refresh wallet data
        $stmt = $pdo->prepare("SELECT currency, balance, address FROM wallets WHERE user_id = ?");
        $stmt->execute([$userId]);
        $wallets = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Fetch recent transactions
    $stmt = $pdo->prepare("SELECT created_at, type, currency, amount, status FROM transactions WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
    $stmt->execute([$userId]);
    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // If no transactions exist, add a sample deposit transaction
    if (empty($transactions)) {
        $sampleCurrency = 'BTC';
        $sampleAmount = 0.1;
        $sampleType = 'deposit';
        $sampleStatus = 'completed';

        $stmt = $pdo->prepare("INSERT INTO transactions (user_id, type, currency, amount, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$userId, $sampleType, $sampleCurrency, $sampleAmount, $sampleStatus]);

        // Update wallet balance to reflect the deposit
        $stmt = $pdo->prepare("UPDATE wallets SET balance = balance + ? WHERE user_id = ? AND currency = ?");
        $stmt->execute([$sampleAmount, $userId, $sampleCurrency]);

        // Refresh transaction data
        $stmt = $pdo->prepare("SELECT created_at, type, currency, amount, status FROM transactions WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
        $stmt->execute([$userId]);
        $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Refresh wallet data to show updated balance
        $stmt = $pdo->prepare("SELECT currency, balance, address FROM wallets WHERE user_id = ?");
        $stmt->execute([$userId]);
        $wallets = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    error_log('Database error in dashboard.php: ' . $e->getMessage());
    echo "<script>alert('Database error. Please try again later.');</script>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Binance Clone</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: #0e1726;
            color: #ffffff;
            margin: 0;
            padding: 0;
        }
        .navbar {
            background: #1a2332;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar a {
            color: #f0b90b;
            text-decoration: none;
            margin: 0 15px;
            font-weight: bold;
        }
        .navbar a:hover {
            color: #ffffff;
        }
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
        }
        .wallet, .transactions {
            background: #1a2332;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .wallet h2, .transactions h2 {
            color: #f0b90b;
            margin-bottom: 15px;
        }
        .wallet-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
        .wallet-card {
            background: #2a3444;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
        }
        .wallet-card h3 {
            margin: 0 0 10px;
        }
        .btn {
            background: #f0b90b;
            color: #0e1726;
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn:hover {
            background: #ffffff;
        }
        .transaction-table {
            width: 100%;
            border-collapse: collapse;
        }
        .transaction-table th, .transaction-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #2a3444;
        }
        .transaction-table th {
            background: #f0b90b;
            color: #0e1726;
        }
        .error {
            color: #ff0000;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div>
            <a href="index.php">Binance Clone</a>
        </div>
        <div>
            <a href="#" onclick="redirectTo('dashboard.php')">Dashboard</a>
            <a href="#" onclick="redirectTo('trade.php')">Trade</a>
            <a href="#" onclick="logout()">Logout</a>
        </div>
    </div>
    <div class="container">
        <div class="wallet">
            <h2>Your Wallets</h2>
            <div class="wallet-grid">
                <?php foreach ($wallets as $wallet): ?>
                    <div class="wallet-card">
                        <h3><?php echo htmlspecialchars($wallet['currency']); ?></h3>
                        <p>Balance: <?php echo number_format($wallet['balance'], 8); ?></p>
                        <p>Address: <?php echo htmlspecialchars(substr($wallet['address'], 0, 10)); ?>...</p>
                        <button class="btn" onclick="redirectTo('trade.php?currency=<?php echo urlencode($wallet['currency']); ?>&action=deposit')">Deposit</button>
                        <button class="btn" onclick="redirectTo('trade.php?currency=<?php echo urlencode($wallet['currency']); ?>&action=withdraw')">Withdraw</button>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="transactions">
            <h2>Recent Transactions</h2>
            <table class="transaction-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Currency</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transactions as $tx): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($tx['created_at']); ?></td>
                            <td><?php echo htmlspecialchars($tx['type']); ?></td>
                            <td><?php echo htmlspecialchars($tx['currency']); ?></td>
                            <td><?php echo number_format($tx['amount'], 8); ?></td>
                            <td><?php echo htmlspecialchars($tx['status']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script>
        function redirectTo(page) {
            window.location.href = page;
        }

        function logout() {
            window.location.href = 'logout.php';
        }
    </script>
</body>
</html>
