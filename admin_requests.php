<?php
session_start();
include("conn.php");

// Check for Admin Access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$message = "";

// Handle Approve Team Action
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['approve_team'])) {
    $user_id = $_POST['user_id'];
    $team_name = $_POST['team_name'];
    if (approve_team_request($conn, $user_id, $team_name)) {
        $message = "<div class='bg-green-500 text-white p-3 rounded-lg mb-4'>Team request for User ID $user_id (Team: $team_name) approved.</div>";
    } else {
        $message = "<div class='bg-red-500 text-white p-3 rounded-lg mb-4'>Error approving team request for User ID $user_id.</div>";
    }
}

// Handle Approve Sponsor Action
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['approve_sponsor'])) {
    $user_id = $_POST['user_id'];
    $sponsor_name = $_POST['sponsor_name'];
    if (approve_sponsor_request($conn, $user_id, $sponsor_name)) {
        $message = "<div class='bg-green-500 text-white p-3 rounded-lg mb-4'>Sponsor request for User ID $user_id (Sponsor: $sponsor_name) approved.</div>";
    } else {
        $message = "<div class='bg-red-500 text-white p-3 rounded-lg mb-4'>Error approving sponsor request for User ID $user_id.</div>";
    }
}

// Fetch all pending requests
$requests = get_pending_requests($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - User Requests</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .bg-hotpink { background-color: hotpink; }
        .text-hotpink { color: hotpink; }
        .admin-nav a { transition: color 0.3s; }
        .admin-nav a:hover { color: hotpink; }
        th, td { padding: 12px; border-bottom: 1px solid #374151; }
    </style>
</head>
<body class="bg-gray-900 text-white font-sans flex">

    <aside class="w-64 bg-gray-800 h-screen fixed p-6">
        <h1 class="text-3xl font-extrabold mb-8 text-hotpink">Admin Panel</h1>
        <nav class="admin-nav space-y-4">
            <a href="admin_dashboard.php" class="block text-lg font-bold text-white hover:text-hotpink">📊 Dashboard</a>
            <a href="admin_requests.php" class="block text-lg font-bold text-hotpink">📧 Requests</a>
            <a href="admin_users.php" class="block text-lg font-bold text-white hover:text-hotpink">👤 Users List</a>
            <a href="admin_drivers.php" class="block text-lg font-bold text-white hover:text-hotpink">🧑‍💻 Drivers (CRUD)</a>
            <a href="admin_teams.php" class="block text-lg font-bold text-white hover:text-hotpink">🏎️ Teams (CRUD)</a>
            <a href="admin_sponsors.php" class="block text-lg font-bold text-white hover:text-hotpink">💰 Sponsors (CRUD)</a>
            <a href="admin_races.php" class="block text-lg font-bold text-white hover:text-hotpink">🗓️ Races & Results (CRUD)</a>
            <a href="logout.php" class="block text-lg font-bold text-white hover:text-red-500 pt-6">🚪 Logout</a>
        </nav>
    </aside>

    <div class="flex-1 ml-64 p-10">
        <header class="mb-8 border-b border-gray-700 pb-4">
            <h2 class="text-4xl font-bold">User Approval Requests (<?php echo count($requests); ?>)</h2>
            <p class="text-gray-400">Approve pending team and sponsor selections from users.</p>
        </header>

        <?php echo $message; ?>

        <div class="bg-gray-800 rounded-xl overflow-x-auto shadow-lg">
            <table class="min-w-full table-auto text-left">
                <thead>
                    <tr class="bg-gray-700 text-sm uppercase">
                        <th class="p-3">User ID</th>
                        <th class="p-3">Name</th>
                        <th class="p-3">Email</th>
                        <th class="p-3">Team Request</th>
                        <th class="p-3">Sponsor Request</th>
                        <th class="p-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($requests)): ?>
                        <?php foreach ($requests as $request): ?>
                            <tr class="hover:bg-gray-700 text-sm">
                                <td><?php echo htmlspecialchars($request['id']); ?></td>
                                <td><?php echo htmlspecialchars($request['fullname']); ?></td>
                                <td><?php echo htmlspecialchars($request['email']); ?></td>
                                <td>
                                    <?php if ($request['team_request']): ?>
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold bg-yellow-800 text-yellow-100">
                                            <?php echo htmlspecialchars($request['team_request']); ?>
                                        </span>
                                    <?php else: ?>
                                        N/A
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($request['sponsor_request']): ?>
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold bg-yellow-800 text-yellow-100">
                                            <?php echo htmlspecialchars($request['sponsor_request']); ?>
                                        </span>
                                    <?php else: ?>
                                        N/A
                                    <?php endif; ?>
                                </td>
                                <td class="whitespace-nowrap flex space-x-2">
                                    <?php if ($request['team_request']): ?>
                                        <form method="POST" class="inline-block" onsubmit="return confirm('Approve team **<?php echo $request['team_request']; ?>** for this user?');">
                                            <input type="hidden" name="user_id" value="<?php echo $request['id']; ?>">
                                            <input type="hidden" name="team_name" value="<?php echo $request['team_request']; ?>">
                                            <button type="submit" name="approve_team" class="text-xs bg-blue-600 hover:bg-blue-700 text-white py-1 px-3 rounded-lg font-bold transition duration-200">
                                                Approve Team
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    <?php if ($request['sponsor_request']): ?>
                                        <form method="POST" class="inline-block" onsubmit="return confirm('Approve sponsor **<?php echo $request['sponsor_request']; ?>** for this user?');">
                                            <input type="hidden" name="user_id" value="<?php echo $request['id']; ?>">
                                            <input type="hidden" name="sponsor_name" value="<?php echo $request['sponsor_request']; ?>">
                                            <button type="submit" name="approve_sponsor" class="text-xs bg-blue-600 hover:bg-blue-700 text-white py-1 px-3 rounded-lg font-bold transition duration-200">
                                                Approve Sponsor
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    <?php if (!$request['team_request'] && !$request['sponsor_request']): ?>
                                        <span class="text-gray-500">No Pending Action</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center py-4 text-gray-400">No pending user requests found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>