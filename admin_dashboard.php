<?php
session_start();
include("conn.php");

// Check for Admin Access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch Summary Data
$summary = get_admin_summary_counts($conn);

// Fetch Graph Data
$distribution_data = get_team_distribution_data($conn);

// Prepare data for Chart.js
$team_labels = json_encode(array_column($distribution_data, 'team'));
$team_counts = json_encode(array_column($distribution_data, 'count'));

// Color utility for Chart.js
$base_colors = ['#dc2626', '#10b981', '#f97316', '#3b82f6', '#f472b6', '#ef4444', '#f59e0b', '#84cc16', '#6366f1'];
$background_colors = json_encode(array_slice($base_colors, 0, count($distribution_data)));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | F1 Academy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
    <style>
        .bg-hotpink { background-color: hotpink; }
        .text-hotpink { color: hotpink; }
        .admin-nav a { transition: color 0.3s; }
        .admin-nav a:hover { color: hotpink; }
    </style>
</head>
<body class="bg-gray-900 text-white font-sans flex">

    <aside class="w-64 bg-gray-800 h-screen fixed p-6">
        <h1 class="text-3xl font-extrabold mb-8 text-hotpink">Admin Panel</h1>
        <nav class="admin-nav space-y-4">
            <a href="admin_dashboard.php" class="block text-lg font-bold text-hotpink">📊 Dashboard</a>
            <a href="admin_requests.php" class="block text-lg font-bold text-white hover:text-hotpink">📧 Requests</a>
            <a href="admin_users.php" class="block text-lg font-bold text-white hover:text-hotpink">👤 Users List</a>
            <a href="admin_drivers.php" class="block text-lg font-bold text-white hover:text-hotpink">🧑‍💻 Drivers (CRUD)</a>
            <a href="admin_teams.php" class="block text-lg font-bold text-white hover:text-hotpink">🏎️ Teams (CRUD)</a>
            <a href="admin_sponsors.php" class="block text-lg font-bold text-white hover:text-hotpink">💰 Sponsors (CRUD)</a>
            <a href="admin_races.php" class="block text-lg font-bold text-white hover:text-hotpink">🗓️ Races & Results (CRUD)</a>
            <a href="logout.php" class="block text-lg font-bold text-white hover:text-red-500 pt-6">🚪 Logout</a>
        </nav>
    </aside>

    <div class="flex-1 ml-64 p-10">
        <header class="mb-10 border-b border-gray-700 pb-4">
            <h2 class="text-4xl font-bold">Academy Overview</h2>
            <p class="text-gray-400">Welcome, Admin User.</p>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-12">
            <div class="bg-gray-800 p-6 rounded-xl shadow-lg border-b-4 border-hotpink">
                <p class="text-sm uppercase text-gray-400 font-semibold">Total Registered Users</p>
                <p class="text-3xl font-extrabold mt-2"><?php echo $summary['total_users']; ?></p>
            </div>
            <div class="bg-gray-800 p-6 rounded-xl shadow-lg border-b-4 border-blue-500">
                <p class="text-sm uppercase text-gray-400 font-semibold">Total F1 Teams</p>
                <p class="text-3xl font-extrabold mt-2"><?php echo $summary['total_teams']; ?></p>
            </div>
            <div class="bg-gray-800 p-6 rounded-xl shadow-lg border-b-4 border-green-500">
                <p class="text-sm uppercase text-gray-400 font-semibold">Total Sponsors</p>
                <p class="text-3xl font-extrabold mt-2"><?php echo $summary['total_sponsors']; ?></p>
            </div>
            <div class="bg-gray-800 p-6 rounded-xl shadow-lg border-b-4 border-yellow-500">
                <p class="text-sm uppercase text-gray-400 font-semibold">Total Drivers</p>
                <p class="text-3xl font-extrabold mt-2"><?php echo $summary['total_drivers']; ?></p>
            </div>
            <div class="bg-gray-800 p-6 rounded-xl shadow-lg border-b-4 border-red-500">
                <p class="text-sm uppercase text-gray-400 font-semibold">Total Races</p>
                <p class="text-3xl font-extrabold mt-2"><?php echo $summary['total_races']; ?></p>
            </div>
        </div>

        <div class="bg-gray-800 p-6 rounded-xl shadow-lg">
            <h3 class="text-2xl font-semibold mb-4 border-b border-gray-700 pb-2">User Team Distribution</h3>
            <div class="w-full h-96">
                <canvas id="teamChart"></canvas>
            </div>
        </div>
    </div>

    <script>
        const ctx = document.getElementById('teamChart').getContext('2d');
        const teamChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo $team_labels; ?>,
                datasets: [{
                    label: 'Users per Team Selection',
                    data: <?php echo $team_counts; ?>,
                    backgroundColor: <?php echo $background_colors; ?>,
                    borderColor: '#ffffff',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: 'white',
                            stepSize: 1
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        }
                    },
                    x: {
                        ticks: {
                            color: 'white'
                        },
                        grid: {
                            display: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    </script>

</body>
</html>