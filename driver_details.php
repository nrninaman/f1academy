<?php
session_start();
include("conn.php");
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

$driver_id = $_GET['id'] ?? null;
$driver = null;
if ($driver_id) {
    $driver = get_driver_by_id($conn, $driver_id);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Driver Details</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>.bg-hotpink { background-color: hotpink; }.text-hotpink { color: hotpink; }</style>
</head>
<body class="bg-gray-900 text-white font-sans min-h-screen p-10">
    <header class="text-center mb-10">
        <h1 class="text-4xl font-bold text-hotpink">Driver Details</h1>
    </header>

    <?php if ($driver): ?>
        <div class="max-w-2xl mx-auto bg-gray-800 p-8 rounded-xl shadow-lg flex flex-col md:flex-row gap-8 items-center">
            <div class="md:w-1/3">
                <img src="<?php echo htmlspecialchars($driver['image_path']); ?>" alt="<?php echo htmlspecialchars($driver['fullname']); ?>" class="w-48 h-48 object-cover rounded-full border-4 border-hotpink mx-auto">
            </div>
            <div class="md:w-2/3 text-center md:text-left">
                <h2 class="text-4xl font-extrabold mb-2"><?php echo htmlspecialchars($driver['fullname']); ?></h2>
                <p class="text-xl text-gray-400 mb-4">Position: <span class="text-hotpink">#<?php echo htmlspecialchars($driver['standing_position']); ?></span> | Points: <span class="text-hotpink"><?php echo htmlspecialchars($driver['points']); ?></span></p>
                
                <p class="text-lg font-semibold mt-4"><strong class="text-gray-300">Team:</strong> <span class="text-white"><?php echo htmlspecialchars($driver['team_name']); ?></span></p>
                <p class="text-lg font-semibold"><strong class="text-gray-300">Sponsor:</strong> <span class="text-white"><?php echo htmlspecialchars($driver['sponsor_name'] ?? 'N/A'); ?></span></p>

                <p class="text-md mt-6 border-t border-gray-700 pt-4">
                    <strong class="text-gray-300">Biography:</strong> <br>
                    <?php echo nl2br(htmlspecialchars($driver['biography'] ?? 'No biography available.')); ?>
                </p>
            </div>
        </div>
    <?php else: ?>
        <p class="text-center text-red-400 text-xl">Driver not found.</p>
    <?php endif; ?>

    <div class="text-center mt-10">
        <a href="dashboard.php" class="text-hotpink hover:text-white transition duration-300">← Back to Dashboard</a>
    </div>
</body>
</html>