<?php
session_start();
include("conn.php");

// Check for Admin Access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$message = "";
$team_to_edit = null;

// Handle Delete Action
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_team'])) {
    $team_id = $_POST['team_id'];
    if (delete_team_by_id($conn, $team_id)) {
        $message = "<div class='bg-green-500 text-white p-3 rounded-lg mb-4'>Team ID $team_id deleted successfully.</div>";
    } else {
        $message = "<div class='bg-red-500 text-white p-3 rounded-lg mb-4'>Error deleting Team ID $team_id. (Ensure no drivers are linked)</div>";
    }
}

// Handle Insert/Update Action
if ($_SERVER["REQUEST_METHOD"] == "POST" && (isset($_POST['add_team']) || isset($_POST['update_team']))) {
    $id = isset($_POST['team_id']) ? $_POST['team_id'] : null;
    $name = $_POST['name'];
    $base_country = $_POST['base_country'];
    $engine_supplier = $_POST['engine_supplier'];
    $logo_path = $_POST['logo_path'];
    $car_image_path = $_POST['car_image_path'];

    if (isset($_POST['add_team'])) {
        if (insert_new_team($conn, $name, $base_country, $engine_supplier)) {
            $message = "<div class='bg-green-500 text-white p-3 rounded-lg mb-4'>Team <strong>$name</strong> added successfully.</div>";
        } else {
            $message = "<div class='bg-red-500 text-white p-3 rounded-lg mb-4'>Error adding team.</div>";
        }
    } elseif (isset($_POST['update_team']) && $id) {
        if (update_team($conn, $id, $name, $base_country, $engine_supplier, $logo_path, $car_image_path)) {
            $message = "<div class='bg-green-500 text-white p-3 rounded-lg mb-4'>Team ID $id updated successfully.</div>";
        } else {
            $message = "<div class='bg-red-500 text-white p-3 rounded-lg mb-4'>Error updating team ID $id.</div>";
        }
    }
}

// Handle Edit Fetch
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['edit_id'])) {
    $stmt = $conn->prepare("SELECT * FROM teams WHERE id = ?");
    $stmt->bind_param("i", $_GET['edit_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $team_to_edit = $result->fetch_assoc();
    $stmt->close();
}

// Fetch all teams
$teams = get_all_teams($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Teams List (CRUD)</title>
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
            <a href="admin_dashboard.php" class="block text-lg font-bold text-white hover:text-hotpink"> Dashboard</a>
            <a href="admin_requests.php" class="block text-lg font-bold text-white hover:text-hotpink"> Requests</a>
            <a href="admin_users.php" class="block text-lg font-bold text-white hover:text-hotpink"> Users List</a>
            <a href="admin_drivers.php" class="block text-lg font-bold text-white hover:text-hotpink"> Drivers (CRUD)</a>
            <a href="admin_teams.php" class="block text-lg font-bold text-hotpink"> Teams (CRUD)</a>
            <a href="admin_sponsors.php" class="block text-lg font-bold text-white hover:text-hotpink"> Sponsors (CRUD)</a>
            <a href="admin_races.php" class="block text-lg font-bold text-white hover:text-hotpink"> Races & Results (CRUD)</a>
            <a href="logout.php" class="block text-lg font-bold text-white hover:text-red-500 pt-6"> Logout</a>
        </nav>
    </aside>

    <div class="flex-1 ml-64 p-10">
        <header class="mb-8 border-b border-gray-700 pb-4">
            <h2 class="text-4xl font-bold">F1 Teams Management</h2>
            <p class="text-gray-400">Add, update, and remove tracked F1 teams.</p>
        </header>

        <?php echo $message; ?>
        
        <div class="bg-gray-800 p-6 rounded-xl shadow-lg mb-8">
            <h3 class="text-2xl font-semibold mb-4 text-hotpink"><?php echo $team_to_edit ? 'Update Team ID: ' . $team_to_edit['id'] : 'Add New Team'; ?></h3>
            <form method="POST" action="admin_teams.php" class="space-y-4">
                <?php if ($team_to_edit): ?>
                    <input type="hidden" name="team_id" value="<?php echo htmlspecialchars($team_to_edit['id']); ?>">
                <?php endif; ?>

                <input type="text" name="name" placeholder="Team Name" required 
                       value="<?php echo htmlspecialchars($team_to_edit['name'] ?? ''); ?>"
                       class="w-full p-3 border border-gray-600 bg-gray-900 text-white rounded-md focus:outline-none focus:ring-2 focus:ring-hotpink">

                <div class="grid grid-cols-2 gap-4">
                    <input type="text" name="base_country" placeholder="Base Country" required 
                           value="<?php echo htmlspecialchars($team_to_edit['base_country'] ?? ''); ?>"
                           class="p-3 border border-gray-600 bg-gray-900 text-white rounded-md focus:outline-none focus:ring-2 focus:ring-hotpink">
                    <input type="text" name="engine_supplier" placeholder="Engine Supplier" required 
                           value="<?php echo htmlspecialchars($team_to_edit['engine_supplier'] ?? ''); ?>"
                           class="p-3 border border-gray-600 bg-gray-900 text-white rounded-md focus:outline-none focus:ring-2 focus:ring-hotpink">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <input type="text" name="logo_path" placeholder="Logo Path (e.g., image/McLaren.png)" 
                           value="<?php echo htmlspecialchars($team_to_edit['logo_path'] ?? ''); ?>"
                           class="p-3 border border-gray-600 bg-gray-900 text-white rounded-md focus:outline-none focus:ring-2 focus:ring-hotpink">
                    <input type="text" name="car_image_path" placeholder="Car Image Path (e.g., image/2025mclarencarright.avif)" 
                           value="<?php echo htmlspecialchars($team_to_edit['car_image_path'] ?? ''); ?>"
                           class="p-3 border border-gray-600 bg-gray-900 text-white rounded-md focus:outline-none focus:ring-2 focus:ring-hotpink">
                </div>

                <button type="submit" name="<?php echo $team_to_edit ? 'update_team' : 'add_team'; ?>" 
                        class="w-full p-3 bg-hotpink text-white font-bold border-none rounded-md cursor-pointer hover:bg-lightpink transition duration-300">
                    <?php echo $team_to_edit ? 'Update Team' : 'Add Team'; ?>
                </button>
            </form>
        </div>


        <div class="bg-gray-800 rounded-xl overflow-x-auto shadow-lg">
            <h3 class="text-xl font-semibold p-4 border-b border-gray-700">Current Teams List (<?php echo count($teams); ?>)</h3>
            <table class="min-w-full table-auto text-left">
                <thead>
                    <tr class="bg-gray-700 text-sm uppercase">
                        <th class="p-3">ID</th>
                        <th class="p-3">Name</th>
                        <th class="p-3">Base Country</th>
                        <th class="p-3">Engine Supplier</th>
                        <th class="p-3">Drivers</th>
                        <th class="p-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($teams)): ?>
                        <?php foreach ($teams as $team): ?>
                            <tr class="hover:bg-gray-700 text-sm">
                                <td><?php echo htmlspecialchars($team['id']); ?></td>
                                <td><?php echo htmlspecialchars($team['name']); ?></td>
                                <td><?php echo htmlspecialchars($team['base_country']); ?></td>
                                <td><?php echo htmlspecialchars($team['engine_supplier']); ?></td>
                                <td><?php echo htmlspecialchars($team['driver_count']); ?></td>
                                <td class="whitespace-nowrap flex space-x-2">
                                    <a href="admin_teams.php?edit_id=<?php echo $team['id']; ?>" class="text-xs bg-blue-600 hover:bg-blue-700 text-white py-1 px-3 rounded-lg font-bold transition duration-200">
                                        Edit
                                    </a>
                                    <form method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this team?');">
                                        <input type="hidden" name="team_id" value="<?php echo $team['id']; ?>">
                                        <button type="submit" name="delete_team" class="text-xs bg-red-600 hover:bg-red-700 text-white py-1 px-3 rounded-lg font-bold transition duration-200">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center py-4 text-gray-400">No teams found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>