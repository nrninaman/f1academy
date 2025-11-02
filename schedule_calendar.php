<?php
session_start();
include("conn.php");
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

// --- Calendar Logic ---

// Get current month/year from URL parameters, default to current month/year
$current_month = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('m');
$current_year = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');

// Ensure month is within 1-12 range
if ($current_month < 1 || $current_month > 12) {
    $current_month = (int)date('m');
}

// Calculate previous/next month and year for navigation
$prev_month = $current_month - 1;
$prev_year = $current_year;
if ($prev_month < 1) {
    $prev_month = 12;
    $prev_year--;
}

$next_month = $current_month + 1;
$next_year = $current_year;
if ($next_month > 12) {
    $next_month = 1;
    $next_year++;
}

// Get the timestamp for the first day of the selected month
$first_day_of_month = mktime(0, 0, 0, $current_month, 1, $current_year);
// Get the number of days in the month
$number_of_days = (int)date('t', $first_day_of_month);
// Get the textual month name
$month_name = date('F Y', $first_day_of_month);
// Get the day of the week for the first day (0=Sunday, 6=Saturday)
$day_of_week = (int)date('w', $first_day_of_month); // w returns 0 (Sun) to 6 (Sat)

// Fetch all races
$races = get_all_races($conn); 

// Map race data including flag (based on Grand Prix name)
$race_flags = [
    'Australian Grand Prix' => '🇦🇺',
    'Miami Grand Prix' => '🇺🇸',
    'Monaco Grand Prix' => '🇲🇨',
    'British Grand Prix' => '🇬🇧',
    'Austrian Grand Prix' => '🇦🇹',
    'Emilia-Romagna Grand Prix' => '🇮🇹',
    'Chinese Grand Prix' => '🇨🇳',
    'Japanese Grand Prix' => '🇯🇵',
    'Bahrain Grand Prix' => '🇧🇭',
    'Saudi Arabian Grand Prix' => '🇸🇦',
    // Add more races and flags as needed
];

// Map races to an array keyed by "Y-m-d" for easy lookup
$races_by_date = [];
foreach ($races as $race) {
    $date_key = date('Y-m-d', strtotime($race['date']));
    $race['flag'] = $race_flags[$race['name']] ?? '🏁';
    if (!isset($races_by_date[$date_key])) {
        $races_by_date[$date_key] = [];
    }
    $races_by_date[$date_key][] = $race;
}

// --- Calendar HTML Generation ---
$calendar_html = '';

// Print blank leading days (for days before the 1st)
for ($i = 0; $i < $day_of_week; $i++) {
    $calendar_html .= '<div class="calendar-day bg-gray-800/50"></div>';
}

// Print days of the month
for ($day = 1; $day <= $number_of_days; $day++) {
    $date_key = $current_year . '-' . str_pad($current_month, 2, '0', STR_PAD_LEFT) . '-' . str_pad($day, 2, '0', STR_PAD_LEFT);
    $events = $races_by_date[$date_key] ?? [];
    $is_current_day = ($date_key == date('Y-m-d'));
    
    $day_class = 'p-2 text-center border border-gray-700 h-28 flex flex-col justify-between overflow-hidden cursor-default transition duration-200';
    $day_class .= $is_current_day ? ' bg-hotpink text-white font-bold' : ' bg-gray-800 hover:bg-gray-700';

    $calendar_html .= "<div class=\"{$day_class}\">";
    $calendar_html .= "<span class='text-lg font-bold " . ($is_current_day ? 'text-white' : 'text-hotpink') . "'>$day</span>";

    if (!empty($events)) {
        foreach ($events as $event) {
            $status_class = $event['is_completed'] ? 'bg-green-600/70' : 'bg-blue-600/70';
            $calendar_html .= "<div class='mt-1 text-xs font-medium {$status_class} p-1 rounded leading-none whitespace-nowrap overflow-hidden text-ellipsis' title='{$event['name']} - {$event['details']}'>{$event['flag']} <strong>{$event['name']}</strong></div>";
        }
    }
    
    $calendar_html .= '</div>';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>2025 F1 Academy Calendar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .bg-hotpink { background-color: hotpink; }
        .text-hotpink { color: hotpink; }
        .calendar-grid { 
            display: grid; 
            grid-template-columns: repeat(7, 1fr); 
            gap: 0;
            border-top: 1px solid #4b5563; 
            border-left: 1px solid #4b5563; /* New left border for entire grid */
        }
        .calendar-grid > div {
            border-left: none !important; /* Remove individual left borders to leave only the grid line */
            border-top: none !important;
            border-bottom: 1px solid #4b5563;
            border-right: 1px solid #4b5563;
        }
        .day-label {
            padding: 10px;
            background-color: #374151;
            font-weight: bold;
            text-align: center;
        }
    </style>
</head>
<body class="bg-gray-900 text-white font-sans min-h-screen p-6 md:p-10">
    
    <div class="max-w-5xl mx-auto">
        <header class="text-center mb-10">
            <h1 class="text-4xl font-bold text-hotpink">2025 F1 Academy Calendar</h1>
            <p class="text-gray-400">All planned race events in a monthly view. </p> 
        </header>

        <div class="bg-gray-800 rounded-xl shadow-2xl p-6">
            
            <div class="flex justify-between items-center mb-6">
                <a href="schedule_calendar.php?month=<?php echo $prev_month; ?>&year=<?php echo $prev_year; ?>" 
                   class="p-2 bg-gray-700 hover:bg-gray-600 rounded-full transition duration-200">
                   &larr; Prev
                </a>
                <h2 class="text-3xl font-extrabold text-white"><?php echo $month_name; ?></h2>
                <a href="schedule_calendar.php?month=<?php echo $next_month; ?>&year=<?php echo $next_year; ?>" 
                   class="p-2 bg-gray-700 hover:bg-gray-600 rounded-full transition duration-200">
                   Next &rarr;
                </a>
            </div>

            <div class="calendar-grid">
                <div class="day-label">Sun</div>
                <div class="day-label">Mon</div>
                <div class="day-label">Tue</div>
                <div class="day-label">Wed</div>
                <div class="day-label">Thu</div>
                <div class="day-label">Fri</div>
                <div class="day-label">Sat</div>
                
                <?php echo $calendar_html; ?>

            </div>
        </div>
    </div>
    <div class="text-center mt-10">
        <a href="dashboard.php" class="text-hotpink hover:text-white transition duration-300 text-lg font-medium">← Back to Dashboard</a>
    </div>
</body>
</html>