<?php
session_start();
include("conn.php");

// Check for Admin Access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// --- F1 2024+ Point System ---
// Array where the key is the position (1-10) and the value is the points awarded.
$F1_RACE_POINTS = [
    1 => 25,
    2 => 18,
    3 => 15,
    4 => 12,
    5 => 10,
    6 => 8,
    7 => 6,
    8 => 4,
    9 => 2,
    10 => 1
    // Positions 11+ get 0 points
];

$message = "";
$race_to_edit = null;
$drivers = get_all_drivers($conn); // Get all drivers for results form
$race_results = [];

// Handle Delete Race Action (Logic Unchanged)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_race'])) {
    $race_id = $_POST['race_id'];
    if (delete_race_by_id($conn, $race_id)) {
        $message = "<div class='bg-green-500 text-white p-3 rounded-lg mb-4'>Race ID $race_id and all related results deleted successfully.</div>";
        recalculate_overall_driver_standings($conn);
    } else {
        $message = "<div class='bg-red-500 text-white p-3 rounded-lg mb-4'>Error deleting Race ID $race_id.</div>";
    }
}

// Handle Insert/Update Race Action (Logic Unchanged)
if ($_SERVER["REQUEST_METHOD"] == "POST" && (isset($_POST['add_race']) || isset($_POST['update_race']))) {
    $id = isset($_POST['race_id']) ? $_POST['race_id'] : null;
    $name = $_POST['name'];
    $date = $_POST['date'];
    $details = $_POST['details'];
    $is_completed = isset($_POST['is_completed']) ? 1 : 0;

    $round_number = (int)($id ? $_POST['round_number'] : $conn->query("SELECT MAX(round_number) FROM races")->fetch_row()[0] + 1);

    if (isset($_POST['add_race'])) {
        if (insert_new_race($conn, $name, $date, $details, $round_number)) {
            $message = "<div class='bg-green-500 text-white p-3 rounded-lg mb-4'>Race <strong>$name</strong> added successfully as Round $round_number.</div>";
        } else {
            $message = "<div class='bg-red-500 text-white p-3 rounded-lg mb-4'>Error adding race (Check DB constraints).</div>";
        }
    } elseif (isset($_POST['update_race']) && $id) {
        if (update_race($conn, $id, $name, $date, $details, $round_number, $is_completed)) {
            $message = "<div class='bg-green-500 text-white p-3 rounded-lg mb-4'>Race ID $id updated successfully.</div>";
        } else {
            $message = "<div class='bg-red-500 text-white p-3 rounded-lg mb-4'>Error updating race ID $id.</div>";
        }
        recalculate_overall_driver_standings($conn);
    }
}

// Handle Insert/Update Race Results Action (Point Calculation Implemented)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_results'])) {
    $race_id = (int)$_POST['results_race_id'];
    $positions = $_POST['position'] ?? [];
    $driver_ids = $_POST['driver_id'] ?? [];
    $success_count = 0;

    if ($race_id) {
        foreach ($driver_ids as $key => $driver_id) {
            $position = (int)$positions[$key];
            
            // --- POINT CALCULATION LOGIC ---
            // If position is 1-10, get points from array; otherwise, points are 0.
            $point = $F1_RACE_POINTS[$position] ?? 0;

            if ($position > 0) {
                // Now insert/update result with the calculated points
                if (insert_or_update_race_result($conn, $race_id, $driver_id, $position, $point)) {
                    $success_count++;
                }
            }
        }
        // Recalculate standings after results are saved
        recalculate_overall_driver_standings($conn);
        $message = "<div class='bg-green-500 text-white p-3 rounded-lg mb-4'>$success_count race results saved/updated for Race ID $race_id. Overall standings updated. (Points Calculated Automatically)</div>";
    }
}

// Handle Edit Fetch (Race & Results)
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['edit_id'])) {
    $race_to_edit = get_race_by_id($conn, $_GET['edit_id']);
    if ($race_to_edit) {
        $race_results = get_race_results($conn, $race_to_edit['id']);
        $results_map = [];
        foreach ($race_results as $result) {
            // Note: We map the position only, as points are now derived from position.
            $results_map[$result['driver_id']] = ['position' => $result['position'], 'points' => $result['points']];
        }
        $race_results = $results_map;
    }
}

// Fetch all races
$races = get_all_races($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Races & Results (CRUD)</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .bg-hotpink { background-color: hotpink; }
        .text-hotpink { color: hotpink; }
        .admin-nav a { transition: color 0.3s; }
        .admin-nav a:hover { color: hotpink; }
        th, td { padding: 12px; border-bottom: 1px solid #374151; }
        .form-input { padding: 10px; border-radius: 6px; border: 1px solid #4b5563; background-color: #1f2937; color: white; width: 100%; box-sizing: border-box; }
        .points-display { background-color: #374151; color: #FF69B4; font-weight: bold; padding: 10px; border-radius: 6px; border: 1px solid #4b5563; text-align: center; }
        
        /* FIX: Ensure consistent vertical alignment in table cells */
        .results-table th, .results-table td {
            vertical-align: middle; /* Align content vertically in the middle */
        }
        .results-table .driver-cell {
            display: flex;
            align-items: center; /* Vertically center image and text */
            gap: 12px; /* Space between image and text */
        }
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
            <a href="admin_teams.php" class="block text-lg font-bold text-white hover:text-hotpink"> Teams (CRUD)</a>
            <a href="admin_sponsors.php" class="block text-lg font-bold text-white hover:text-hotpink"> Sponsors (CRUD)</a>
            <a href="admin_races.php" class="block text-lg font-bold text-hotpink"> Races & Results (CRUD)</a>
            <a href="logout.php" class="block text-lg font-bold text-white hover:text-red-500 pt-6"> Logout</a>
        </nav>
    </aside>

    <div class="flex-1 ml-64 p-10">
        <header class="mb-8 border-b border-gray-700 pb-4">
            <h2 class="text-4xl font-bold">Races & Results Management</h2>
            <p class="text-gray-400">Manage F1 Academy race events and record race results.</p>
        </header>

        <?php echo $message; ?>

        <div class="bg-gray-800 p-6 rounded-xl shadow-lg mb-8">
            <h3 class="text-2xl font-semibold mb-4 text-hotpink"><?php echo $race_to_edit ? 'Update Race ID: ' . $race_to_edit['id'] : 'Add New Race'; ?></h3>
            <form method="POST" action="admin_races.php" class="space-y-4">
                <?php if ($race_to_edit): ?>
                    <input type="hidden" name="race_id" value="<?php echo htmlspecialchars($race_to_edit['id']); ?>">
                    <input type="hidden" name="round_number" value="<?php echo htmlspecialchars($race_to_edit['round_number']); ?>">
                <?php endif; ?>

                <input type="text" name="name" placeholder="Race Name (e.g., British Grand Prix)" required 
                       value="<?php echo htmlspecialchars($race_to_edit['name'] ?? ''); ?>"
                       class="form-input">

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label for="date" class="text-sm text-gray-400 block text-left">Date</label>
                        <input type="date" id="date" name="date" required 
                            value="<?php echo htmlspecialchars($race_to_edit['date'] ?? ''); ?>"
                            class="form-input">
                    </div>
                    
                    <div class="space-y-1 pt-6 flex items-center justify-center">
                        <input type="checkbox" id="is_completed" name="is_completed" value="1"
                            <?php echo ($race_to_edit['is_completed'] ?? 0) ? 'checked' : ''; ?>
                            class="h-5 w-5 text-hotpink border-gray-600 rounded focus:ring-hotpink">
                        <label for="is_completed" class="ml-2 text-sm text-white">Race Completed</label>
                    </div>
                    
                </div>
                
                <textarea name="details" placeholder="Race Details (e.g., Race held at Silverstone)" rows="3" 
                          class="form-input"><?php echo htmlspecialchars($race_to_edit['details'] ?? ''); ?></textarea>

                <button type="submit" name="<?php echo $race_to_edit ? 'update_race' : 'add_race'; ?>" 
                        class="w-full p-3 bg-hotpink text-white font-bold border-none rounded-md cursor-pointer hover:bg-lightpink transition duration-300">
                    <?php echo $race_to_edit ? 'Update Race' : 'Add Race'; ?>
                </button>
            </form>
        </div>

        <?php if ($race_to_edit): ?>
        <div class="bg-gray-800 p-6 rounded-xl shadow-lg mb-8">
            <h3 class="text-2xl font-semibold mb-4 text-hotpink">Edit Results for: <?php echo htmlspecialchars($race_to_edit['name']); ?></h3>
            <p class="text-sm text-gray-400 mb-4">Points are calculated automatically based on F1 rules (P1=25, P2=18, ..., P10=1).</p>
            <form method="POST" action="admin_races.php" class="space-y-4">
                <input type="hidden" name="results_race_id" value="<?php echo htmlspecialchars($race_to_edit['id']); ?>">
                
                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto text-left results-table">
                        <thead>
                            <tr class="bg-gray-700 text-sm uppercase">
                                <th class="p-3 w-1/3">Driver</th>
                                <th class="p-3 w-1/4">Position (1-<?php echo count($drivers); ?>)</th>
                                <th class="p-3 w-1/4">Calculated Points</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($drivers as $driver): 
                                $current_result = $race_results[$driver['id']] ?? ['position' => '', 'points' => 0];
                                
                                // Calculate points in the frontend for display only
                                $display_points = $F1_RACE_POINTS[(int)$current_result['position']] ?? 0;
                            ?>
                                <tr class="hover:bg-gray-700 text-sm">
                                    <td class="p-3 font-semibold">
                                        <div class="driver-cell">
                                            <img src="<?php echo htmlspecialchars($driver['image_path']); ?>" alt="Driver" class="w-6 h-6 rounded-full object-cover">
                                            <span><?php echo htmlspecialchars($driver['fullname']); ?> (<?php echo htmlspecialchars($driver['team_name']); ?>)</span>
                                            <input type="hidden" name="driver_id[]" value="<?php echo htmlspecialchars($driver['id']); ?>">
                                        </div>
                                    </td>
                                    <td class="p-3">
                                        <input type="number" name="position[]" placeholder="Pos (1-<?php echo count($drivers); ?>)" 
                                               value="<?php echo htmlspecialchars($current_result['position']); ?>"
                                               min="1"
                                               class="form-input w-20 text-center p-2 result-position">
                                    </td>
                                    <td class="p-3">
                                        <!-- Display Points (Read-only field) -->
                                        <div class="points-display result-points-display" data-current-points="<?php echo $current_result['points']; ?>">
                                            <?php echo $current_result['points'] > 0 ? htmlspecialchars($current_result['points']) : '0'; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <button type="submit" name="save_results" class="w-full p-3 bg-blue-600 text-white font-bold border-none rounded-md cursor-pointer hover:bg-blue-700 transition duration-300">
                    💾 Save Race Results
                </button>
            </form>
        </div>
        
        <script>
            // F1 Points Map (Used for client-side display)
            const F1_POINTS_MAP = <?php echo json_encode($F1_RACE_POINTS); ?>;

            function updateCalculatedPoints(inputElement) {
                const position = parseInt(inputElement.value);
                const pointsDisplay = inputElement.closest('tr').querySelector('.result-points-display');
                let calculatedPoints = 0;

                if (position > 0 && F1_POINTS_MAP.hasOwnProperty(position)) {
                    calculatedPoints = F1_POINTS_MAP[position];
                }
                
                // Update the visible points display element
                pointsDisplay.textContent = calculatedPoints;
            }
            
            // Re-apply event listeners and initial update
            document.querySelectorAll('.result-position').forEach(input => {
                // Ensure event listener is attached (important when navigating in single-page context)
                input.oninput = () => updateCalculatedPoints(input);
                
                // Initial call to set points display correctly based on loaded positions
                updateCalculatedPoints(input);
            });
            
        </script>
        
        <?php endif; ?>

        <div class="bg-gray-800 rounded-xl overflow-x-auto shadow-lg">
            <h3 class="text-xl font-semibold p-4 border-b border-gray-700">Current Races (<?php echo count($races); ?>)</h3>
            <table class="min-w-full table-auto text-left">
                <thead>
                    <tr class="bg-gray-700 text-sm uppercase">
                        <th class="p-3">ID</th>
                        <th class="p-3">Name</th>
                        <th class="p-3">Date</th>
                        <th class="p-3">Status</th>
                        <th class="p-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($races)): ?>
                        <?php foreach ($races as $race): ?>
                            <tr class="hover:bg-gray-700 text-sm">
                                <td><?php echo htmlspecialchars($race['id']); ?></td>
                                <td><?php echo htmlspecialchars($race['name']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($race['date'])); ?></td>
                                <td><span class="px-2 py-1 rounded-full text-xs font-semibold <?php echo $race['is_completed'] ? 'bg-green-600' : 'bg-blue-600'; ?>"><?php echo $race['is_completed'] ? 'Completed' : 'Upcoming'; ?></span></td>
                                <td class="whitespace-nowrap flex space-x-2">
                                    <a href="admin_races.php?edit_id=<?php echo $race['id']; ?>" class="text-xs bg-blue-600 hover:bg-blue-700 text-white py-1 px-3 rounded-lg font-bold transition duration-200">
                                        Edit Race/Results
                                    </a>
                                    <form method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete race: <?php echo $race['name']; ?>? This will delete all results!');">
                                        <input type="hidden" name="race_id" value="<?php echo $race['id']; ?>">
                                        <button type="submit" name="delete_race" class="text-xs bg-red-600 hover:bg-red-700 text-white py-1 px-3 rounded-lg font-bold transition duration-200">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center py-4 text-gray-400">No races found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>
