<?php
/**
 * Database Connection Setup
 */
$host = "localhost";
$user = "root"; 
$pass = ""; 
$db = "f1academy"; 

// Create a new database connection
$conn = new mysqli($host, $user, $pass, $db);

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

/**
 * DATABASE QUERY FUNCTIONS
 * All functions performing direct database operations are defined here.
 */

/**
 * Checks if a user exists and retrieves their details by email.
 * Includes the 'role' field.
 * @param mysqli $conn The database connection object.
 * @param string $email The user's email address.
 * @return array|null The user's data array on success, or null if not found/error.
 */
function check_user_by_email($conn, $email) {
    // Prepare statement to select all user data based on email, including role
    $stmt = $conn->prepare("SELECT id, fullname, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    return $user;
}

/**
 * Inserts a new user into the database.
 */
function insert_new_user($conn, $fullname, $email, $nationality, $age, $gender, $hashed_password) {
    // Prepare statement for inserting new user data
    $stmt = $conn->prepare("INSERT INTO users (fullname, email, nationality, age, gender, password) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssiss", $fullname, $email, $nationality, $age, $gender, $hashed_password);
    $success = $stmt->execute();
    $stmt->close();
    return $success;
}

/**
 * Retrieves a user's details by their ID.
 */
function get_user_by_id($conn, $user_id) {
    // Prepare statement to select specific user data based on user ID
    $stmt = $conn->prepare("SELECT fullname, email, nationality, age, gender, team, sponsor FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    return $user;
}

/**
 * Updates the user's team and nationality in the database.
 */
function update_user_profile($conn, $team, $nationality, $user_id) {
    $stmt = $conn->prepare("UPDATE users SET team = ?, nationality = ? WHERE id = ?");
    $stmt->bind_param("ssi", $team, $nationality, $user_id);
    $success = $stmt->execute();
    $stmt->close();
    return $success;
}

/**
 * Updates only the user's team in the database.
 */
function update_user_team($conn, $team, $user_id) {
    $stmt = $conn->prepare("UPDATE users SET team = ? WHERE id = ?");
    $stmt->bind_param("si", $team, $user_id);
    $success = $stmt->execute();
    $stmt->close();
    return $success;
}

/**
 * Updates only the user's sponsor in the database.
 */
function update_user_sponsor($conn, $sponsor, $user_id) {
    $stmt = $conn->prepare("UPDATE users SET sponsor = ? WHERE id = ?");
    $stmt->bind_param("si", $sponsor, $user_id);
    $success = $stmt->execute();
    $stmt->close();
    return $success;
}

// --- ADMIN DASHBOARD FUNCTIONS ---

/**
 * Retrieves a count of total users, teams, and sponsors.
 * @param mysqli $conn The database connection object.
 * @return array Array with counts.
 */
function get_admin_summary_counts($conn) {
    $summary = [];
    $summary['total_users'] = $conn->query("SELECT COUNT(*) FROM users")->fetch_row()[0];
    $summary['total_teams'] = $conn->query("SELECT COUNT(*) FROM teams")->fetch_row()[0];
    $summary['total_sponsors'] = $conn->query("SELECT COUNT(*) FROM sponsors")->fetch_row()[0];
    return $summary;
}

/**
 * Retrieves team selection data for the analytics graph.
 * @param mysqli $conn The database connection object.
 * @return array Array of team names and user counts.
 */
function get_team_distribution_data($conn) {
    $query = "SELECT team, COUNT(id) as count FROM users WHERE team IS NOT NULL GROUP BY team";
    $result = $conn->query($query);
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    return $data;
}

/**
 * Retrieves all users for the user list page.
 */
function get_all_users($conn) {
    $query = "SELECT id, fullname, email, nationality, age, gender, team, sponsor, role FROM users";
    $result = $conn->query($query);
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    return $data;
}

/**
 * Retrieves all teams for the team list page.
 */
function get_all_teams($conn) {
    $query = "SELECT * FROM teams";
    $result = $conn->query($query);
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    return $data;
}

/**
 * Retrieves all sponsors for the sponsor list page.
 */
function get_all_sponsors($conn) {
    $query = "SELECT * FROM sponsors";
    $result = $conn->query($query);
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    return $data;
}

/**
 * Deletes a user by ID.
 */
function delete_user_by_id($conn, $id) {
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $success = $stmt->execute();
    $stmt->close();
    return $success;
}

/**
 * Deletes a team by ID.
 */
function delete_team_by_id($conn, $id) {
    $stmt = $conn->prepare("DELETE FROM teams WHERE id = ?");
    $stmt->bind_param("i", $id);
    $success = $stmt->execute();
    $stmt->close();
    return $success;
}

/**
 * Deletes a sponsor by ID.
 */
function delete_sponsor_by_id($conn, $id) {
    $stmt = $conn->prepare("DELETE FROM sponsors WHERE id = ?");
    $stmt->bind_param("i", $id);
    $success = $stmt->execute();
    $stmt->close();
    return $success;
}
?>
