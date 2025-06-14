<?php
require 'db.php';
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href = 'login.php';</script>";
    exit;
}
$user_id = $_SESSION['user_id'];
$pair = isset($_GET['pair']) ? $_GET['pair'] : 'BTC/USDT';

// Handle order submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $type = $_POST['type'];
    $amount = $_POST['amount'];
    $price = isset($_POST['price']) ? $_POST['price'] : null;

    try {
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, type, pair, amount, price) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $type, $pair, $amount, $price]);
        echo "<script>alert('Order placed successfully!');</script>";
    } catch (PDOException $e) {
        echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trade - Binance Clone</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
        }
        .chart-container {
            background: #1a2332;
            padding: 20px;
            border-radius: 10px;
        }
        .trade-panel {
            background: #1a2332;
            padding: 20px;
            border-radius: 10px;
        }
        .trade-panel h2 {
            color: #f0b90b;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input, select {
            width: 100%;
            padding: 10px;
            border: 1px solid #2a3444;
            border-radius: 5px;
            background: #2a3444;
            color: #ffffff;
        }
        input:focus, select:focus {
            outline: none;
            border-color: #f0b90b;
        }
        .btn {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }
        .btn-buy {
            background: #00cc00;
            color: #ffffff;
        }
        .btn-sell {
            background: #cc0000;
            color: #ffffff;
        }
        .btn:hover {
            opacity: 0.9;
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
        <div class="chart-container">
            <h2><?php echo htmlspecialchars($pair); ?> Trading Chart</h2>
            <canvas id="tradeChart"></canvas>
        </div>
        <div class="trade-panel">
            <h2>Place Order</h2>
            <form method="POST">
                <div class="form-group">
                    <label for="type">Order Type</label>
                    <select id="type" name="type" required>
                        <option value="market">Market Order</option>
                        <option value="limit">Limit Order</option>
                        <option value="stop-loss">Stop-Loss Order</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="amount">Amount</label>
                    <input type="number" id="amount" name="amount" step="0.00000001" required>
                </div>
                <div class="form-group">
                    <label for="price">Price (USD)</label>
                    <input type="number" id="price" name="price" step="0.01">
                </div>
                <button type="submit" name="action" value="buy" class="btn btn-buy">Buy</button>
                <button type="submit" name="action" value="sell" class="btn btn-sell">Sell</button>
            </form>
        </div>
    </div>
    <script>
        function redirectTo(page) {
            window.location.href = page;
        }

        function logout() {
            window.location.href = 'logout.php';
        }

        // Trading Chart
        const ctx = document.getElementById('tradeChart').getContext('2d');
        const tradeChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['1m', '5m', '15m', '30m', '1h'],
                datasets: [{
                    label: '<?php echo htmlspecialchars($pair); ?>',
                    data: [65000, 65200, 65100, 65050, 65250],
                    borderColor: '#f0b90b',
                    fill: false
                }]
            },
            options: {
                scales: {
                    y: { beginAtZero: false }
                }
            }
        });

        // Mock price updates
        setInterval(() => {
            tradeChart.data.datasets[0].data.push(65000 + Math.random() * 500);
            tradeChart.data.labels.push('');
            if (tradeChart.data.labels.length > 10) {
                tradeChart.data.labels.shift();
                tradeChart.data.datasets[0].data.shift();
            }
            tradeChart.update();
        }, 5000);
    </script>
</body>
</html>
