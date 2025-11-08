<?php
session_start();
include("conn.php");

if (!isset($_SESSION['user_id'])) {
    echo "Not logged in!";
    exit;
}

if (isset($_POST['team'])) {
    $team = $_POST['team'];
    $user_id = $_SESSION['user_id'];

    if (update_user_team($conn, $team, $user_id)) {
        $_SESSION['team'] = $team;
        echo "Success";
    } else {
        echo "Error: " . $conn->error;
    }
} else {
    echo "No team selected!";
}
?>