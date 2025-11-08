<?php
session_start();
// Include the database connection and query functions
include('conn.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Full list of sponsors for display and validation
$f1_sponsors = [
    'Red Bull', 'Mercedes', 'Ferrari', 'McLaren', 
    'Oracle', 'HP (Hewlett-Packard)', 'PETRONAS', 'Visa', 
    'Cash App', 'Stake', 'Kick', 'MoneyGram', 
    'BWT', 'Aramco', 'Mastercard', 'Shell', 
    'Ineos', 'Red Bull GmbH', 'Cognizant'
];

// Handle POST request for sponsor selection
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['sponsor'])) {
    $selected_sponsor = $_POST['sponsor'];
    $user_id = $_SESSION['user_id'];
    
    // Validate selected sponsor against the full list (basic security)
    if (!in_array($selected_sponsor, $f1_sponsors)) {
         echo "
        <script>
            alert('❌ Error: Invalid sponsor selected. Please try again.');
            window.location.href = 'select_sponsor.php'; 
        </script>";
        exit();
    }

    if (update_user_sponsor($conn, $selected_sponsor, $user_id)) {
        $_SESSION['sponsor'] = $selected_sponsor; // Update session for immediate use
        
        echo "
        <script>
            alert('✅ Thank you for choosing $selected_sponsor as your sponsor! Redirecting to dashboard.');
            window.location.href = 'dashboard.php'; 
        </script>";
        exit();
    } else {
         echo "
        <script>
            alert('❌ Error saving sponsor to database. Please try again.');
            window.location.href = 'select_sponsor.php'; 
        </script>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Choose Your Sponsor</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Custom colors based on original design */
        .bg-hotpink { background-color: hotpink; }
        .text-hotpink { color: hotpink; }
        .bg-redbull { background-color: #002f6c; }     /* Deep blue */
        .bg-mercedes { background-color: #00c0b5; }   /* Teal */
        .bg-ferrari { background-color: #da1212; }    /* Red */
        .bg-mclaren { background-color: #ff8000; }    /* Orange */

        /* General styling for cards */
        .sponsor-card {
            min-height: 250px;
        }
    </style>
</head>
<body class="bg-gray-900 text-white font-sans text-center p-10 min-h-screen">

    <h2 class="text-4xl font-bold mb-8 text-white">Choose Your Sponsor</h2>
    <p class="text-gray-400 mb-8">Select one of our major partners to represent.</p>

    <form method="POST" id="sponsorForm">
        <div class="flex justify-center items-center flex-wrap gap-6 max-w-6xl mx-auto">
            
            <div class="sponsor-card bg-redbull rounded-xl w-56 p-5 text-center transition duration-300 ease-in-out cursor-pointer hover:scale-105" onclick="chooseSponsor('Red Bull')">
                <img src="image/RedBull.png" alt="Red Bull" class="w-full h-24 object-contain mb-4">
                <p class="text-xl font-bold">Red Bull</p>
                <p class="text-xs mt-1">Energy Drink</p>
            </div>

            <div class="sponsor-card bg-mercedes rounded-xl w-56 p-5 text-center transition duration-300 ease-in-out cursor-pointer hover:scale-105" onclick="chooseSponsor('Mercedes')">
                <img src="image/Mercedes-Logo.png" alt="Mercedes" class="w-full h-24 object-contain mb-4">
                <p class="text-xl font-bold">Mercedes</p>
                <p class="text-xs mt-1">Automotive</p>
            </div>

            <div class="sponsor-card bg-ferrari rounded-xl w-56 p-5 text-center transition duration-300 ease-in-out cursor-pointer hover:scale-105" onclick="chooseSponsor('Ferrari')">
                <img src="image/Ferrari.png" alt="Ferrari" class="w-full h-24 object-contain mb-4">
                <p class="text-xl font-bold">Ferrari</p>
                <p class="text-xs mt-1">Automotive</p>
            </div>

            <div class="sponsor-card bg-mclaren rounded-xl w-56 p-5 text-center transition duration-300 ease-in-out cursor-pointer hover:scale-105" onclick="chooseSponsor('McLaren')">
                <img src="image/McLaren.png" alt="McLaren" class="w-full h-24 object-contain mb-4">
                <p class="text-xl font-bold">McLaren</p>
                <p class="text-xs mt-1">Technology</p>
            </div>

            <h3 class="w-full text-2xl font-bold text-gray-500 mt-10">Select from Full Partner List:</h3>

            <div class="flex flex-wrap justify-center w-full gap-4">
                <?php 
                // Only show remaining sponsors if they are not already in the main 4 cards
                $primary_sponsors = ['Red Bull', 'Mercedes', 'Ferrari', 'McLaren'];
                foreach ($f1_sponsors as $s): 
                    if (!in_array($s, $primary_sponsors)): ?>
                    <div class="bg-gray-800 border border-gray-700 p-4 rounded-lg text-center transition duration-200 cursor-pointer hover:bg-gray-700"
                        onclick="chooseSponsor('<?php echo htmlspecialchars($s); ?>')">
                        <p class="font-semibold text-white"><?php echo htmlspecialchars($s); ?></p>
                    </div>
                <?php endif;
                endforeach; ?>
            </div>
            
        </div>

        <input type="hidden" name="sponsor" id="selectedSponsor">
    </form>

    <script>
        function chooseSponsor(name) {
            document.getElementById('selectedSponsor').value = name;
            document.getElementById('sponsorForm').submit();
        }
    </script>

</body>
</html>