<?php
session_start();
// Include the database connection and query functions
include('conn.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Handle POST request for sponsor selection
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['sponsor'])) {
    $selected_sponsor = $_POST['sponsor'];
    $user_id = $_SESSION['user_id'];
    
    // FUNCTIONAL FIX: Save the selected sponsor to the database
    if (update_user_sponsor($conn, $selected_sponsor, $user_id)) {
        $_SESSION['sponsor'] = $selected_sponsor; // Update session for immediate use
        
        echo "
        <script>
            alert('✅ Thank you for choosing $selected_sponsor as your sponsor! Redirecting to dashboard.');
            // Rename: Redirect to dashboard.php
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
    </style>
</head>
<body class="bg-gray-900 text-white font-sans text-center p-10 min-h-screen">

    <h2 class="text-4xl font-bold mb-8 text-white">Choose Your Sponsor</h2>

    <form method="POST" id="sponsorForm">
        <div class="flex justify-center items-center flex-wrap gap-6 max-w-4xl mx-auto">
            
            <div class="sponsor-card bg-redbull rounded-xl w-56 p-5 text-center transition duration-300 ease-in-out cursor-pointer hover:scale-105" onclick="chooseSponsor('Red Bull')">
                <img src="image/redbull.png" alt="Red Bull" class="w-full h-32 object-contain mb-4">
                <p class="text-xl font-bold">Red Bull</p>
            </div>

            <div class="sponsor-card bg-mercedes rounded-xl w-56 p-5 text-center transition duration-300 ease-in-out cursor-pointer hover:scale-105" onclick="chooseSponsor('Mercedes')">
                <img src="image/mercedes.png" alt="Mercedes" class="w-full h-32 object-contain mb-4">
                <p class="text-xl font-bold">Mercedes</p>
            </div>

            <div class="sponsor-card bg-ferrari rounded-xl w-56 p-5 text-center transition duration-300 ease-in-out cursor-pointer hover:scale-105" onclick="chooseSponsor('Ferrari')">
                <img src="image/ferrari.png" alt="Ferrari" class="w-full h-32 object-contain mb-4">
                <p class="text-xl font-bold">Ferrari</p>
            </div>

            <div class="sponsor-card bg-mclaren rounded-xl w-56 p-5 text-center transition duration-300 ease-in-out cursor-pointer hover:scale-105" onclick="chooseSponsor('McLaren')">
                <img src="image/mclaren.png" alt="McLaren" class="w-full h-32 object-contain mb-4">
                <p class="text-xl font-bold">McLaren</p>
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