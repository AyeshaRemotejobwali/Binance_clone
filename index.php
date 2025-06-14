<?php
require 'db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Binance Clone - Homepage</title>
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
        }
        .market-table {
            width: 100%;
            border-collapse: collapse;
            background: #1a2332;
            border-radius: 8px;
            overflow: hidden;
        }
        .market-table th, .market-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #2a3444;
        }
        .market-table th {
            background: #f0b90b;
            color: #0e1726;
        }
        .market-table tr:hover {
            background: #2a3444;
        }
        .chart-container {
            margin-top: 20px;
            background: #1a2332;
            padding: 20px;
            border-radius: 8px;
        }
        .btn {
            background: #f0b90b;
            color: #0e1726;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }
        .btn:hover {
            background: #ffffff;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div>
            <a href="index.php">Binance Clone</a>
        </div>
        <div>
            <a href="#" onclick="redirectTo('login.php')">Login</a>
            <a href="#" onclick="redirectTo('signup.php')">Sign Up</a>
            <a href="#" onclick="redirectTo('dashboard.php')">Dashboard</a>
        </div>
    </div>
    <div class="container">
        <h1>Welcome to Binance Clone</h1>
        <table class="market-table" id="marketTable">
            <thead>
                <tr>
                    <th>Pair</th>
                    <th>Price (USD)</th>
                    <th>24h Change</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
        <div class="chart-container">
            <canvas id="priceChart"></canvas>
        </div>
    </div>
    <script>
        function redirectTo(page) {
            window.location.href = page;
        }

        // Mock API for real-time prices
        async function fetchMarketData() {
            // Simulate API response
            return [
                { pair: 'BTC/USDT', price: 65000.25, change: 2.5 },
                { pair: 'ETH/USDT', price: 3500.75, change: -1.2 },
                { pair: 'BNB/USDT', price: 600.15, change: 0.8 }
            ];
        }

        function updateMarketTable(data) {
            const tbody = document.querySelector('#marketTable tbody');
            tbody.innerHTML = '';
            data.forEach(item => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${item.pair}</td>
                    <td>$${item.price.toFixed(2)}</td>
                    <td style="color: ${item.change >= 0 ? '#00ff00' : '#ff0000'}">${item.change}%</td>
                    <td><button class="btn" onclick="redirectTo('trade.php?pair=${item.pair}')">Trade</button></td>
                `;
                tbody.appendChild(row);
            });
        }

        // Chart.js for price trends
        const ctx = document.getElementById('priceChart').getContext('2d');
        const priceChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['1h', '2h', '3h', '4h', '5h'],
                datasets: [{
                    label: 'BTC/USDT Price',
                    data: [64000, 64500, 65000, 64800, 65000],
                    borderColor: '#f0b90b',
                    fill: false
                }]
            },
            options: { scales: { y: { beginAtZero: false } } }
        });

        // Fetch and update market data every 10 seconds
        async function initMarket() {
            const data = await fetchMarketData();
            updateMarketTable(data);
        }
        initMarket();
        setInterval(initMarket, 10000);
    </script>
</body>
</html>
