<?php
session_start();
include("conn.php");

// Check for Admin Access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$message = "";
$teams = get_all_teams($conn);
$sponsors = get_all_sponsors($conn);
$driver_to_edit = null;

// Handle Delete Action
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_driver'])) {
    $driver_id = $_POST['driver_id'];
    if (delete_driver_by_id($conn, $driver_id)) {
        $message = "<div class='bg-green-500 text-white p-3 rounded-lg mb-4'>Driver ID $driver_id deleted successfully.</div>";
    } else {
        $message = "<div class='bg-red-500 text-white p-3 rounded-lg mb-4'>Error deleting Driver ID $driver_id. (Ensure no race results are linked)</div>";
    }
}

// Handle Insert/Update Action
if ($_SERVER["REQUEST_METHOD"] == "POST" && (isset($_POST['add_driver']) || isset($_POST['update_driver']))) {
    $id = isset($_POST['driver_id']) ? $_POST['driver_id'] : null;
    $fullname = $_POST['fullname'];
    $team_name = $_POST['team_name'];
    $sponsor_name = $_POST['sponsor_name'] ?: NULL;
    $standing_position = (int)$_POST['standing_position'];
    $points = (int)$_POST['points'];
    $biography = $_POST['biography'];
    $image_path = $_POST['image_path'];

    if (isset($_POST['add_driver'])) {
        if (insert_new_driver($conn, $fullname, $team_name, $sponsor_name, $standing_position, $points, $biography, $image_path)) {
            $message = "<div class='bg-green-500 text-white p-3 rounded-lg mb-4'>Driver **$fullname** added successfully.</div>";
        } else {
            $message = "<div class='bg-red-500 text-white p-3 rounded-lg mb-4'>Error adding driver.</div>";
        }
    } elseif (isset($_POST['update_driver']) && $id) {
        if (update_driver($conn, $id, $fullname, $team_name, $sponsor_name, $standing_position, $points, $biography, $image_path)) {
            $message = "<div class='bg-green-500 text-white p-3 rounded-lg mb-4'>Driver ID $id updated successfully.</div>";
        } else {
            $message = "<div class='bg-red-500 text-white p-3 rounded-lg mb-4'>Error updating driver ID $id.</div>";
        }
    }
}

// Handle Edit Fetch
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['edit_id'])) {
    $driver_to_edit = get_driver_by_id($conn, $_GET['edit_id']);
}

// Fetch all drivers
$drivers = get_all_drivers($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Drivers CRUD</title>
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
            <a href="admin_requests.php" class="block text-lg font-bold text-white hover:text-hotpink">📧 Requests</a>
            <a href="admin_users.php" class="block text-lg font-bold text-white hover:text-hotpink">👤 Users List</a>
            <a href="admin_drivers.php" class="block text-lg font-bold text-hotpink">🧑‍💻 Drivers (CRUD)</a>
            <a href="admin_teams.php" class="block text-lg font-bold text-white hover:text-hotpink">🏎️ Teams (CRUD)</a>
            <a href="admin_sponsors.php" class="block text-lg font-bold text-white hover:text-hotpink">💰 Sponsors (CRUD)</a>
            <a href="admin_races.php" class="block text-lg font-bold text-white hover:text-hotpink">🗓️ Races & Results (CRUD)</a>
            <a href="logout.php" class="block text-lg font-bold text-white hover:text-red-500 pt-6">🚪 Logout</a>
        </nav>
    </aside>

    <div class="flex-1 ml-64 p-10">
        <header class="mb-8 border-b border-gray-700 pb-4">
            <h2 class="text-4xl font-bold">Driver Management</h2>
            <p class="text-gray-400">Add, update, and remove F1 Academy drivers and their data.</p>
        </header>

        <?php echo $message; ?>

        <div class="bg-gray-800 p-6 rounded-xl shadow-lg mb-8">
            <h3 class="text-2xl font-semibold mb-4 text-hotpink"><?php echo $driver_to_edit ? 'Update Driver ID: ' . $driver_to_edit['id'] : 'Add New Driver'; ?></h3>
            <form method="POST" action="admin_drivers.php" class="space-y-4">
                <?php if ($driver_to_edit): ?>
                    <input type="hidden" name="driver_id" value="<?php echo htmlspecialchars($driver_to_edit['id']); ?>">
                <?php endif; ?>

                <input type="text" name="fullname" placeholder="Full Name" required 
                       value="<?php echo htmlspecialchars($driver_to_edit['fullname'] ?? ''); ?>"
                       class="w-full p-3 border border-gray-600 bg-gray-900 text-white rounded-md focus:outline-none focus:ring-2 focus:ring-hotpink">
                
                <textarea name="biography" placeholder="Biography" rows="3" required 
                          class="w-full p-3 border border-gray-600 bg-gray-900 text-white rounded-md focus:outline-none focus:ring-2 focus:ring-hotpink"><?php echo htmlspecialchars($driver_to_edit['biography'] ?? ''); ?></textarea>

                <div class="grid grid-cols-3 gap-4">
                    <input type="number" name="standing_position" placeholder="Standing Position" required
                           value="<?php echo htmlspecialchars($driver_to_edit['standing_position'] ?? ''); ?>"
                           class="p-3 border border-gray-600 bg-gray-900 text-white rounded-md focus:outline-none focus:ring-2 focus:ring-hotpink">
                    <input type="number" name="points" placeholder="Points" required
                           value="<?php echo htmlspecialchars($driver_to_edit['points'] ?? ''); ?>"
                           class="p-3 border border-gray-600 bg-gray-900 text-white rounded-md focus:outline-none focus:ring-2 focus:ring-hotpink">
                    <input type="text" name="image_path" placeholder="Image Path (e.g., image/MV.png)"
                           value="<?php echo htmlspecialchars($driver_to_edit['image_path'] ?? ''); ?>"
                           class="p-3 border border-gray-600 bg-gray-900 text-white rounded-md focus:outline-none focus:ring-2 focus:ring-hotpink">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <select name="team_name" required class="p-3 border border-gray-600 bg-gray-900 text-white rounded-md focus:outline-none focus:ring-2 focus:ring-hotpink">
                        <option value="">-- Select Team --</option>
                        <?php foreach ($teams as $team): ?>
                            <option value="<?php echo htmlspecialchars($team['name']); ?>"
                                <?php echo ($driver_to_edit['team_name'] ?? '') == $team['name'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($team['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <select name="sponsor_name" class="p-3 border border-gray-600 bg-gray-900 text-white rounded-md focus:outline-none focus:ring-2 focus:ring-hotpink">
                        <option value="">-- Select Sponsor (Optional) --</option>
                        <?php foreach ($sponsors as $sponsor): ?>
                            <option value="<?php echo htmlspecialchars($sponsor['name']); ?>"
                                <?php echo ($driver_to_edit['sponsor_name'] ?? '') == $sponsor['name'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($sponsor['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" name="<?php echo $driver_to_edit ? 'update_driver' : 'add_driver'; ?>" 
                        class="w-full p-3 bg-hotpink text-white font-bold border-none rounded-md cursor-pointer hover:bg-lightpink transition duration-300">
                    <?php echo $driver_to_edit ? 'Update Driver' : 'Add Driver'; ?>
                </button>
            </form>
        </div>

        <div class="bg-gray-800 rounded-xl overflow-x-auto shadow-lg">
            <h3 class="text-xl font-semibold p-4 border-b border-gray-700">Current Drivers List</h3>
            <table class="min-w-full table-auto text-left">
                <thead>
                    <tr class="bg-gray-700 text-sm uppercase">
                        <th class="p-3">ID</th>
                        <th class="p-3">POS</th>
                        <th class="p-3">Name</th>
                        <th class="p-3">Team</th>
                        <th class="p-3">Points</th>
                        <th class="p-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($drivers)): ?>
                        <?php foreach ($drivers as $driver): ?>
                            <tr class="hover:bg-gray-700 text-sm">
                                <td><?php echo htmlspecialchars($driver['id']); ?></td>
                                <td><?php echo htmlspecialchars($driver['standing_position']); ?></td>
                                <td><?php echo htmlspecialchars($driver['fullname']); ?></td>
                                <td><?php echo htmlspecialchars($driver['team_name']); ?></td>
                                <td><?php echo htmlspecialchars($driver['points']); ?></td>
                                <td class="whitespace-nowrap flex space-x-2">
                                    <a href="admin_drivers.php?edit_id=<?php echo $driver['id']; ?>" class="text-xs bg-blue-600 hover:bg-blue-700 text-white py-1 px-3 rounded-lg font-bold transition duration-200">
                                        Edit
                                    </a>
                                    <form method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete driver: <?php echo $driver['fullname']; ?>?');">
                                        <input type="hidden" name="driver_id" value="<?php echo $driver['id']; ?>">
                                        <button type="submit" name="delete_driver" class="text-xs bg-red-600 hover:bg-red-700 text-white py-1 px-3 rounded-lg font-bold transition duration-200">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center py-4 text-gray-400">No drivers found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>