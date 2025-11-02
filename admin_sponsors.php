<?php
session_start();
include("conn.php");

// Check for Admin Access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$f1_title_sponsors = [
    'Oracle',
    'HP (Hewlett-Packard)',
    'PETRONAS',
    'Visa',
    'Cash App',
    'Stake',
    'Kick',
    'MoneyGram',
    'BWT',
    'Aramco',
    'Cognizant',
    'Mastercard',
    'Shell',
    'Ineos',
    'Red Bull GmbH',
];

$f1_sectors = [
    'Cloud Technology',
    'Energy / Oil & Gas',
    'Financial Technology (Fintech)',
    'Computer Hardware',
    'Software / Data',
    'E-commerce',
    'Luxury / Watchmaking',
    'Logistics',
    'Beverage / Drink',
    'Automotive',
];


$message = "";
$sponsor_to_edit = null;

// Handle Delete Action
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_sponsor'])) {
    $sponsor_id = $_POST['sponsor_id'];
    if (delete_sponsor_by_id($conn, $sponsor_id)) {
        $message = "<div class='bg-green-500 text-white p-3 rounded-lg mb-4'>Sponsor ID $sponsor_id deleted successfully.</div>";
    } else {
        $message = "<div class='bg-red-500 text-white p-3 rounded-lg mb-4'>Error deleting Sponsor ID $sponsor_id. (Ensure no drivers are linked)</div>";
    }
}

// Handle Insert/Update Action
if ($_SERVER["REQUEST_METHOD"] == "POST" && (isset($_POST['add_sponsor']) || isset($_POST['update_sponsor']))) {
    $id = isset($_POST['sponsor_id']) ? $_POST['sponsor_id'] : null;
    $name = $_POST['name'];
    $sector = $_POST['sector'];
    $contract_value = (int)$_POST['contract_value'];
    $logo_path = $_POST['logo_path'];
    $details = $_POST['details'];

    if (isset($_POST['add_sponsor'])) {
        if (insert_new_sponsor($conn, $name, $sector, $contract_value, $logo_path, $details)) {
            // FIX: Removed ** from notification
            $message = "<div class='bg-green-500 text-white p-3 rounded-lg mb-4'>Sponsor <strong>$name</strong> added successfully.</div>";
        } else {
            $message = "<div class='bg-red-500 text-white p-3 rounded-lg mb-4'>Error adding sponsor.</div>";
        }
    } elseif (isset($_POST['update_sponsor']) && $id) {
        if (update_sponsor($conn, $id, $name, $sector, $contract_value, $logo_path, $details)) {
            $message = "<div class='bg-green-500 text-white p-3 rounded-lg mb-4'>Sponsor ID $id updated successfully.</div>";
        } else {
            $message = "<div class='bg-red-500 text-white p-3 rounded-lg mb-4'>Error updating sponsor ID $id.</div>";
        }
    }
}

// Handle Edit Fetch
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['edit_id'])) {
    $stmt = $conn->prepare("SELECT * FROM sponsors WHERE id = ?");
    $stmt->bind_param("i", $_GET['edit_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $sponsor_to_edit = $result->fetch_assoc();
    $stmt->close();
}

$sponsors = get_all_sponsors($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Sponsors List (CRUD)</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .bg-hotpink { background-color: hotpink; }
        .text-hotpink { color: hotpink; }
        .admin-nav a { transition: color 0.3s; }
        .admin-nav a:hover { color: hotpink; }
        th, td { padding: 12px; border-bottom: 1px solid #374151; }
        .admin-select option { background-color: #1f2937; color: white; }
    </style>
</head>
<body class="bg-gray-900 text-white font-sans flex">

    <aside class="w-64 bg-gray-800 h-screen fixed p-6">
        <h1 class="text-3xl font-extrabold mb-8 text-hotpink">Admin Panel</h1>
        <nav class="admin-nav space-y-4">
            <a href="admin_dashboard.php" class="block text-lg font-bold text-white hover:text-hotpink"> Dashboard</a>
            
            <a href="admin_requests.php" class="block text-lg font-bold text-white hover:text-hotpink">
                 Requests
            </a>
            
            <a href="admin_users.php" class="block text-lg font-bold text-white hover:text-hotpink"> Users List</a>
            <a href="admin_drivers.php" class="block text-lg font-bold text-white hover:text-hotpink"> Drivers (CRUD)</a>
            <a href="admin_teams.php" class="block text-lg font-bold text-white hover:text-hotpink"> Teams (CRUD)</a>
            <a href="admin_sponsors.php" class="block text-lg font-bold text-hotpink"> Sponsors (CRUD)</a>
            <a href="admin_races.php" class="block text-lg font-bold text-white hover:text-hotpink"> Races & Results (CRUD)</a>
            <a href="logout.php" class="block text-lg font-bold text-white hover:text-red-500 pt-6"> Logout</a>
        </nav>
    </aside>
    <div class="flex-1 ml-64 p-10">
        <header class="mb-8 border-b border-gray-700 pb-4">
            <h2 class="text-4xl font-bold">F1 Academy Sponsors Management</h2>
            <p class="text-gray-400">Add, update, and remove tracked sponsors using real-world data.</p>
        </header>

        <?php echo $message; ?>

        <div class="bg-gray-800 p-6 rounded-xl shadow-lg mb-8">
            <h3 class="text-2xl font-semibold mb-4 text-hotpink"><?php echo $sponsor_to_edit ? 'Update Sponsor ID: ' . $sponsor_to_edit['id'] : 'Add New Sponsor'; ?></h3>
            <form method="POST" action="admin_sponsors.php" class="space-y-4">
                <?php if ($sponsor_to_edit): ?>
                    <input type="hidden" name="sponsor_id" value="<?php echo htmlspecialchars($sponsor_to_edit['id']); ?>">
                <?php endif; ?>

                <select name="name" required 
                    class="w-full p-3 border border-gray-600 bg-gray-900 text-white rounded-md admin-select focus:outline-none focus:ring-2 focus:ring-hotpink">
                    <option value="" disabled <?php echo $sponsor_to_edit ? '' : 'selected'; ?>>Select Sponsor Name</option>
                    <?php foreach ($f1_title_sponsors as $sponsor_name): ?>
                        <option value="<?php echo htmlspecialchars($sponsor_name); ?>"
                            <?php echo (isset($sponsor_to_edit['name']) && $sponsor_to_edit['name'] === $sponsor_name) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($sponsor_name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <div class="grid grid-cols-2 gap-4">
                    <select name="sector" required 
                        class="p-3 border border-gray-600 bg-gray-900 text-white rounded-md admin-select focus:outline-none focus:ring-2 focus:ring-hotpink">
                        <option value="" disabled <?php echo $sponsor_to_edit ? '' : 'selected'; ?>>Select Sector</option>
                        <?php foreach ($f1_sectors as $sector): ?>
                            <option value="<?php echo htmlspecialchars($sector); ?>"
                                <?php echo (isset($sponsor_to_edit['sector']) && $sponsor_to_edit['sector'] === $sector) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($sector); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <input type="number" name="contract_value" placeholder="Contract Value (USD)" required 
                            value="<?php echo htmlspecialchars($sponsor_to_edit['contract_value'] ?? ''); ?>"
                            class="p-3 border border-gray-600 bg-gray-900 text-white rounded-md focus:outline-none focus:ring-2 focus:ring-hotpink">
                </div>

                <input type="text" name="logo_path" placeholder="Logo Path (e.g., image/Oracle.png)" 
                        value="<?php echo htmlspecialchars($sponsor_to_edit['logo_path'] ?? ''); ?>"
                        class="w-full p-3 border border-gray-600 bg-gray-900 text-white rounded-md focus:outline-none focus:ring-2 focus:ring-hotpink">

                <textarea name="details" placeholder="Sponsor Details/Description" rows="3" 
                          class="w-full p-3 border border-gray-600 bg-gray-900 text-white rounded-md focus:outline-none focus:ring-2 focus:ring-hotpink"><?php echo htmlspecialchars($sponsor_to_edit['details'] ?? ''); ?></textarea>

                <button type="submit" name="<?php echo $sponsor_to_edit ? 'update_sponsor' : 'add_sponsor'; ?>" 
                        class="w-full p-3 bg-hotpink text-white font-bold border-none rounded-md cursor-pointer hover:bg-lightpink transition duration-300">
                    <?php echo $sponsor_to_edit ? 'Update Sponsor' : 'Add Sponsor'; ?>
                </button>
            </form>
        </div>
        <div class="bg-gray-800 rounded-xl overflow-x-auto shadow-lg">
            <h3 class="text-xl font-semibold p-4 border-b border-gray-700">Current Sponsors List (<?php echo count($sponsors); ?>)</h3>
            <table class="min-w-full table-auto text-left">
                <thead>
                    <tr class="bg-gray-700 text-sm uppercase">
                        <th class="p-3">ID</th>
                        <th class="p-3">Name</th>
                        <th class="p-3">Sector</th>
                        <th class="p-3">Contract Value (USD)</th>
                        <th class="p-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($sponsors)): ?>
                        <?php foreach ($sponsors as $sponsor): ?>
                            <tr class="hover:bg-gray-700 text-sm">
                                <td><?php echo htmlspecialchars($sponsor['id']); ?></td>
                                <td><?php echo htmlspecialchars($sponsor['name']); ?></td>
                                <td><?php echo htmlspecialchars($sponsor['sector']); ?></td>
                                <td>$<?php echo number_format($sponsor['contract_value']); ?></td>
                                <td class="whitespace-nowrap flex space-x-2">
                                    <a href="admin_sponsors.php?edit_id=<?php echo $sponsor['id']; ?>" class="text-xs bg-blue-600 hover:bg-blue-700 text-white py-1 px-3 rounded-lg font-bold transition duration-200">
                                        Edit
                                    </a>
                                    <form method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete sponsor: <?php echo $sponsor['name']; ?>?');">
                                        <input type="hidden" name="sponsor_id" value="<?php echo $sponsor['id']; ?>">
                                        <button type="submit" name="delete_sponsor" class="text-xs bg-red-600 hover:bg-red-700 text-white py-1 px-3 rounded-lg font-bold transition duration-200">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center py-4 text-gray-400">No sponsors found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>