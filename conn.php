<?php

$host = "localhost";
$user = "root"; 
$pass = ""; 
$db = "f1academy"; 

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function check_user_by_email($conn, $email) {
    $stmt = $conn->prepare("SELECT id, fullname, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    return $user;
}

function insert_new_user($conn, $fullname, $email, $nationality, $age, $gender, $hashed_password) {
    $stmt = $conn->prepare("INSERT INTO users (fullname, email, nationality, age, gender, password) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssiss", $fullname, $email, $nationality, $age, $gender, $hashed_password);
    $success = $stmt->execute();
    $stmt->close();
    return $success;
}

function get_user_by_id($conn, $user_id) {
    $stmt = $conn->prepare("SELECT fullname, email, nationality, age, gender, team, sponsor, team_request, sponsor_request FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    return $user;
}

function update_user_full_profile_and_requests($conn, $fullname, $email, $nationality, $team_request, $sponsor_request, $user_id) {
    $stmt = $conn->prepare("UPDATE users SET fullname = ?, email = ?, nationality = ?, team_request = ?, sponsor_request = ? WHERE id = ?");
    $stmt->bind_param("sssssi", $fullname, $email, $nationality, $team_request, $sponsor_request, $user_id);
    $success = $stmt->execute();
    $stmt->close();
    return $success;
}

function update_user_team($conn, $team, $user_id) {
    $stmt = $conn->prepare("UPDATE users SET team = ? WHERE id = ?");
    $stmt->bind_param("si", $team, $user_id);
    $success = $stmt->execute();
    $stmt->close();
    return $success;
}

function update_user_sponsor($conn, $sponsor, $user_id) {
    $stmt = $conn->prepare("UPDATE users SET sponsor = ? WHERE id = ?");
    $stmt->bind_param("si", $sponsor, $user_id);
    $success = $stmt->execute();
    $stmt->close();
    return $success;
}

function get_pending_requests($conn) {
    $query = "SELECT id, fullname, email, team_request, sponsor_request FROM users WHERE team_request IS NOT NULL OR sponsor_request IS NOT NULL";
    $result = $conn->query($query);
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    return $data;
}

function approve_team_request($conn, $user_id, $team_name) {
    $stmt = $conn->prepare("UPDATE users SET team = ?, team_request = NULL WHERE id = ?");
    $stmt->bind_param("si", $team_name, $user_id);
    $success = $stmt->execute();
    $stmt->close();
    return $success;
}

function approve_sponsor_request($conn, $user_id, $sponsor_name) {
    $stmt = $conn->prepare("UPDATE users SET sponsor = ?, sponsor_request = NULL WHERE id = ?");
    $stmt->bind_param("si", $sponsor_name, $user_id);
    $success = $stmt->execute();
    $stmt->close();
    return $success;
}

function get_admin_summary_counts($conn) {
    $summary = [];
    $summary['total_users'] = $conn->query("SELECT COUNT(*) FROM users")->fetch_row()[0];
    $summary['total_teams'] = $conn->query("SELECT COUNT(*) FROM teams")->fetch_row()[0];
    $summary['total_sponsors'] = $conn->query("SELECT COUNT(*) FROM sponsors")->fetch_row()[0];
    $summary['total_drivers'] = $conn->query("SELECT COUNT(*) FROM drivers")->fetch_row()[0];
    $summary['total_races'] = $conn->query("SELECT COUNT(*) FROM races")->fetch_row()[0];
    return $summary;
}

function get_team_distribution_data($conn) {
    $query = "SELECT team, COUNT(id) as count FROM users WHERE team IS NOT NULL GROUP BY team";
    $result = $conn->query($query);
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    return $data;
}

function get_all_users($conn) {
    $query = "SELECT id, fullname, email, nationality, age, gender, team, sponsor, team_request, sponsor_request, role FROM users";
    $result = $conn->query($query);
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    return $data;
}

function delete_user_by_id($conn, $id) {
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $success = $stmt->execute();
    $stmt->close();
    return $success;
}

function get_all_teams($conn) {
    $query = "SELECT * FROM teams";
    $result = $conn->query($query);
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $row['driver_count'] = $conn->query("SELECT COUNT(*) FROM drivers WHERE team_name = '{$row['name']}'")->fetch_row()[0];
        $data[] = $row;
    }
    return $data;
}

function get_team_by_name($conn, $name) {
    $stmt = $conn->prepare("SELECT * FROM teams WHERE name = ?");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $result = $stmt->get_result();
    $team = $result->fetch_assoc();
    $stmt->close();
    return $team;
}

function insert_new_team($conn, $name, $base_country, $engine_supplier) {
    $stmt = $conn->prepare("INSERT INTO teams (name, base_country, engine_supplier) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $base_country, $engine_supplier);
    $success = $stmt->execute();
    $stmt->close();
    return $success;
}

function update_team($conn, $id, $name, $base_country, $engine_supplier, $logo_path, $car_image_path) {
    $stmt = $conn->prepare("UPDATE teams SET name = ?, base_country = ?, engine_supplier = ?, logo_path = ?, car_image_path = ? WHERE id = ?");
    $stmt->bind_param("sssssi", $name, $base_country, $engine_supplier, $logo_path, $car_image_path, $id);
    $success = $stmt->execute();
    $stmt->close();
    return $success;
}

function delete_team_by_id($conn, $id) {
    $stmt = $conn->prepare("DELETE FROM teams WHERE id = ?");
    $stmt->bind_param("i", $id);
    $success = $stmt->execute();
    $stmt->close();
    return $success;
}

function get_all_sponsors($conn) {
    $query = "SELECT * FROM sponsors";
    $result = $conn->query($query);
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    return $data;
}

function insert_new_sponsor($conn, $name, $sector, $contract_value, $logo_path, $details) {
    $stmt = $conn->prepare("INSERT INTO sponsors (name, sector, contract_value, logo_path, details) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiss", $name, $sector, $contract_value, $logo_path, $details);
    $success = $stmt->execute();
    $stmt->close();
    return $success;
}

function update_sponsor($conn, $id, $name, $sector, $contract_value, $logo_path, $details) {
    $stmt = $conn->prepare("UPDATE sponsors SET name = ?, sector = ?, contract_value = ?, logo_path = ?, details = ? WHERE id = ?");
    $stmt->bind_param("ssissi", $name, $sector, $contract_value, $logo_path, $details, $id);
    $success = $stmt->execute();
    $stmt->close();
    return $success;
}

function delete_sponsor_by_id($conn, $id) {
    $stmt = $conn->prepare("DELETE FROM sponsors WHERE id = ?");
    $stmt->bind_param("i", $id);
    $success = $stmt->execute();
    $stmt->close();
    return $success;
}

function get_all_drivers($conn) {
    $query = "SELECT * FROM drivers ORDER BY standing_position ASC";
    $result = $conn->query($query);
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    return $data;
}

function get_driver_by_id($conn, $id) {
    $stmt = $conn->prepare("SELECT * FROM drivers WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $driver = $result->fetch_assoc();
    $stmt->close();
    return $driver;
}

function insert_new_driver($conn, $fullname, $team_name, $sponsor_name, $standing_position, $points, $biography, $image_path) {
    $stmt = $conn->prepare("INSERT INTO drivers (fullname, team_name, sponsor_name, standing_position, points, biography, image_path) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssiiss", $fullname, $team_name, $sponsor_name, $standing_position, $points, $biography, $image_path);
    $success = $stmt->execute();
    $stmt->close();
    return $success;
}

function update_driver($conn, $id, $fullname, $team_name, $sponsor_name, $standing_position, $points, $biography, $image_path) {
    $stmt = $conn->prepare("UPDATE drivers SET fullname = ?, team_name = ?, sponsor_name = ?, standing_position = ?, points = ?, biography = ?, image_path = ? WHERE id = ?");
    $stmt->bind_param("sssiissi", $fullname, $team_name, $sponsor_name, $standing_position, $points, $biography, $image_path, $id);
    $success = $stmt->execute();
    $stmt->close();
    return $success;
}

function delete_driver_by_id($conn, $id) {
    $stmt = $conn->prepare("DELETE FROM drivers WHERE id = ?");
    $stmt->bind_param("i", $id);
    $success = $stmt->execute();
    $stmt->close();
    return $success;
}

function get_all_races($conn) {
    $query = "SELECT * FROM races ORDER BY round_number ASC";
    $result = $conn->query($query);
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    return $data;
}

function get_race_by_id($conn, $id) {
    $stmt = $conn->prepare("SELECT * FROM races WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $race = $result->fetch_assoc();
    $stmt->close();
    return $race;
}

function insert_new_race($conn, $name, $date, $details, $round_number) {
    $stmt = $conn->prepare("INSERT INTO races (name, date, details, round_number) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $name, $date, $details, $round_number);
    $success = $stmt->execute();
    $stmt->close();
    return $success;
}

function update_race($conn, $id, $name, $date, $details, $round_number, $is_completed) {
    $stmt = $conn->prepare("UPDATE races SET name = ?, date = ?, details = ?, round_number = ?, is_completed = ? WHERE id = ?");
    $stmt->bind_param("sssiii", $name, $date, $details, $round_number, $is_completed, $id);
    $success = $stmt->execute();
    $stmt->close();
    return $success;
}

function delete_race_by_id($conn, $id) {
    // Deleting a race will cascade and delete all related results due to FOREIGN KEY ON DELETE CASCADE (as per f1academy.sql)
    $stmt = $conn->prepare("DELETE FROM races WHERE id = ?");
    $stmt->bind_param("i", $id);
    $success = $stmt->execute();
    $stmt->close();
    return $success;
}

function get_race_results($conn, $race_id) {
    $stmt = $conn->prepare("
        SELECT 
            r.position, r.points, d.fullname, d.team_name, d.image_path, d.id AS driver_id
        FROM 
            results r
        JOIN 
            drivers d ON r.driver_id = d.id
        WHERE 
            r.race_id = ?
        ORDER BY 
            r.position ASC
    ");
    $stmt->bind_param("i", $race_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    $stmt->close();
    return $data;
}

function insert_or_update_race_result($conn, $race_id, $driver_id, $position, $points) {
    $stmt = $conn->prepare("
        INSERT INTO results (race_id, driver_id, position, points) 
        VALUES (?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
            position = VALUES(position), points = VALUES(points)
    ");
    $stmt->bind_param("iiii", $race_id, $driver_id, $position, $points);
    $success = $stmt->execute();
    $stmt->close();
    return $success;
}

function get_driver_standings_data($conn) {
    $query = "
        SELECT 
            d.id, d.fullname, d.team_name, d.standing_position, d.points, d.image_path
        FROM 
            drivers d
        ORDER BY 
            d.standing_position ASC, d.points DESC
    ";
    $result = $conn->query($query);
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    return $data;
}

function get_latest_race_result($conn) {
    $race_query = "SELECT id, name, round_number FROM races WHERE is_completed = TRUE ORDER BY round_number DESC LIMIT 1";
    $race_result = $conn->query($race_query);
    $latest_race = $race_result->fetch_assoc();

    if (!$latest_race) {
        return ['race' => null, 'results' => []];
    }

    $race_id = $latest_race['id'];
    $results = get_race_results($conn, $race_id);

    return ['race' => $latest_race, 'results' => $results];
}

function get_constructor_standings_data($conn) {
    $query = "
        SELECT 
            t.name AS team_name, t.logo_path, SUM(d.points) AS total_points
        FROM 
            teams t
        JOIN 
            drivers d ON t.name = d.team_name
        GROUP BY 
            t.name
        ORDER BY 
            total_points DESC
    ";
    $result = $conn->query($query);
    $data = [];
    $rank = 1;
    while ($row = $result->fetch_assoc()) {
        $row['standing_position'] = $rank++;
        $data[] = $row;
    }
    return $data;
}

?>