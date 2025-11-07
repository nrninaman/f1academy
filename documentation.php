<?php
// Note: This documentation page includes conn.php to dynamically fetch database schema info.
// The connection details are assumed to be correctly configured in conn.php.
include("conn.php");

// --- Data Fetching Functions for Documentation ---

function get_db_schema($conn) {
    $schema = [];
    $tables_result = $conn->query("SHOW TABLES");
    
    if ($tables_result) {
        while ($table_row = $tables_result->fetch_row()) {
            $table_name = $table_row[0];
            $columns_result = $conn->query("DESCRIBE `$table_name`");
            $columns = [];
            while ($col_row = $columns_result->fetch_assoc()) {
                $columns[] = $col_row;
            }
            $schema[$table_name] = $columns;
        }
    }
    return $schema;
}

function render_schema_table($schema) {
    $html = '';
    foreach ($schema as $table_name => $columns) {
        $html .= '<h3 class="text-2xl font-semibold mb-4 text-hotpink" id="table-' . strtolower($table_name) . '">' . htmlspecialchars(ucwords($table_name)) . ' Table</h3>';
        $html .= '<div class="overflow-x-auto mb-8">';
        $html .= '<table class="min-w-full table-auto text-left border border-gray-600">';
        $html .= '<thead><tr class="bg-gray-700 text-sm uppercase">';
        $html .= '<th class="p-3">Field</th><th class="p-3">Type</th><th class="p-3">Null</th><th class="p-3">Key</th><th class="p-3">Default</th></tr></thead>';
        $html .= '<tbody>';
        foreach ($columns as $column) {
            $html .= '<tr class="hover:bg-gray-700 text-sm">';
            $html .= '<td class="p-3"><strong>' . htmlspecialchars($column['Field']) . '</strong></td>';
            $html .= '<td class="p-3">' . htmlspecialchars($column['Type']) . '</td>';
            $html .= '<td class="p-3">' . htmlspecialchars($column['Null']) . '</td>';
            $html .= '<td class="p-3 text-hotpink">' . htmlspecialchars($column['Key']) . '</td>';
            $html .= '<td class="p-3">' . htmlspecialchars($column['Default'] ?? 'NULL') . '</td>';
            $html .= '</tr>';
        }
        $html .= '</tbody></table></div>';
    }
    return $html;
}

$db_schema = get_db_schema($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>F1 Academy Code Documentation</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* General Styles */
        .bg-hotpink { background-color: hotpink; }
        .text-hotpink { color: hotpink; }
        
        /* Sidebar Navigation */
        .admin-nav a { transition: color 0.3s; }
        .admin-nav a:hover { color: hotpink; }
        .sidebar a { display: block; padding: 8px 12px; border-radius: 4px; }
        .sidebar a:hover { background-color: #374151; }
        .sidebar a.sub-item { margin-left: 1rem; font-weight: bold; }

        /* Code Snippet - Carbon Style */
        .code-container {
            background: #282c34; /* Carbon base background */
            padding: 1rem 0; 
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.5);
            margin-top: 1rem;
            margin-bottom: 1rem;
            overflow-x: auto;
            position: relative;
        }

        /* Top Bar mimicking Carbon */
        .code-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 24px;
            background: #21252b;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
        }

        /* Traffic light buttons mimicking Carbon */
        .code-container::after {
            content: ' ';
            position: absolute;
            top: 8px;
            left: 12px;
            width: 12px;
            height: 12px;
            background: #ff5f56; 
            border-radius: 50%;
            box-shadow: 20px 0 0 #ffbd2e, 40px 0 0 #27c93f;
            z-index: 10;
        }

        .code-snippet {
            white-space: pre-wrap;
            font-family: 'Consolas', 'Monaco', monospace;
            font-size: 0.9rem;
            line-height: 1.4;
            padding: 16px; 
            padding-top: 24px; 
            margin-top: 0;
            position: relative;
            z-index: 5;
        }
        .code-snippet code {
            display: block;
            color: #ccc;
            padding: 2px 16px;
        }
        .code-snippet .php-tag { color: #81A2BE; font-weight: bold; }
        .code-snippet .keyword { color: #C792EA; font-weight: bold; }
        .code-snippet .string { color: #C3E88D; }
        .code-snippet .comment { color: #78909C; }
        .code-snippet .number { color: #F78C6C; }

        /* Removed Diff Highlighting CSS classes */
    </style>
</head>
<body class="bg-gray-900 text-white font-sans flex">

    <!-- Sidebar Navigation --><aside class="w-64 bg-gray-800 h-screen fixed p-6 overflow-y-auto">
        <h1 class="text-3xl font-extrabold mb-8 text-hotpink">Documentation</h1>
        <nav class="sidebar space-y-2">
            <a href="#architecture" class="text-lg font-bold text-white">1. System Architecture</a>
            <a href="#database-schema" class="text-lg font-bold text-white">2. Database Schema</a>
                <?php foreach (array_keys($db_schema) as $table): ?>
                    <a href="#table-<?php echo strtolower($table); ?>" class="sub-item text-sm text-gray-400 hover:text-hotpink"><?php echo htmlspecialchars(ucwords($table)); ?></a>
                <?php endforeach; ?>
            <a href="#comparison" class="text-lg font-bold text-white">3. Version Comparison</a>
            <a href="#c1-n+1-fix" class="sub-item text-sm text-gray-400 hover:text-hotpink">C1: N+1 Optimization</a>
            <a href="#c2-latest-results-fix" class="sub-item text-sm text-gray-400 hover:text-hotpink">C2: Latest Result Update</a>
            <a href="#c3-point-system" class="sub-item text-sm text-gray-400 hover:text-hotpink">C3: F1 Point System</a>
            <a href="#c4-admin-ui-fix" class="sub-item text-sm text-gray-400 hover:text-hotpink">C4: Admin UI Fixes</a>
            <a href="#future-optimization" class="text-lg font-bold text-white">4. Future Optimization</a>
        </nav>
    </aside>

    <!-- Main Content Area --><div class="flex-1 ml-64 p-10">
        <header class="mb-10 border-b border-gray-700 pb-4">
            <h2 class="text-4xl font-bold">F1 Academy Codebase Documentation</h2>
            <p class="text-gray-400">Comparing Latest vs. Previous Stable</p>
        </header>

        <!-- 1. System Architecture --><h2 class="text-3xl font-bold mb-6 text-hotpink" id="architecture">1. System Architecture</h2>
        <p class="mb-4">The F1 Academy system uses a **Monolithic PHP** architecture. This means one system handles everything: business logic, routing, and presentation. All database functions are kept separate inside the **`conn.php`** file. The frontend uses pure HTML and **Tailwind CSS CDN** for modern styling.</p>

        <hr class="border-gray-700 my-8">

        <!-- 2. Database Schema --><h2 class="text-3xl font-bold mb-6 text-hotpink" id="database-schema">2. Database Schema (Live Snapshot)</h2>
        <p class="mb-6 text-gray-300">This section shows the current structure of the **`f1academy`** database. The system uses auto-incrementing integers for primary keys. Foreign keys enforce relationships between tables, ensuring data integrity.</p>
        
        <?php echo render_schema_table($db_schema); ?>
        
        <hr class="border-gray-700 my-8">

        <!-- 3. Version Comparison --><h2 class="text-3xl font-bold mb-6 text-hotpink" id="comparison">3. Version Comparison & Key Repairs</h2>
        <p class="mb-6 text-gray-300">These changes fix critical bugs and optimize performance compared to the previous stable version.</p>

        <!-- Comparison 1: N+1 Query Optimization --><h3 class="text-xl font-bold mb-4 text-white" id="c1-n+1-fix">C1: Critical N+1 Query Optimization</h3>
        <p class="mb-4 text-gray-300">The old method slowed down the system greatly. It ran one database query to get all teams. Then, it ran another query for **each** team to count drivers. This generated too much traffic. We now fix this using a **single, optimized SQL query** with JOIN and GROUP BY.</p>
        
        <p class="font-bold text-sm mt-4">Problematic Code (<code>conn.php</code>, `get_all_teams` function):</p>
        <div class="code-container">
            <pre class="code-snippet">
<code><span class="php-tag">&lt;?php</span></code>
<code><span class="keyword">function</span> <span class="keyword">get_all_teams</span>(<span class="keyword">$conn</span>) {</code>
<code>    <span class="keyword">$query</span> <span class="keyword">=</span> <span class="string">"SELECT * FROM teams"</span>;</code>
<code>    <span class="keyword">$result</span> <span class="keyword">=</span> <span class="keyword">$conn</span>-&gt;query(<span class="keyword">$query</span>);</code>
<code>    <span class="keyword">$data</span> <span class="keyword">=</span> [];</code>
<code>    <span class="keyword">while</span> (<span class="keyword">$row</span> <span class="keyword">=</span> <span class="keyword">$result</span>-&gt;fetch_assoc()) {</code>
<code>        <span class="comment">// This runs a new query inside the loop for every team.</span></code>
<code>        <span class="keyword">$row</span>[<span class="string">'driver_count'</span>] <span class="keyword">=</span> <span class="keyword">$conn</span>-&gt;query(<span class="string">"SELECT COUNT(*) FROM drivers WHERE team_name = '{$row['name']}'"</span>)-&gt;fetch_row()[0];</code>
<code>        <span class="keyword">$data</span>[] <span class="keyword">=</span> <span class="keyword">$row</span>;</code>
<code>    }</code>
<code>    <span class="keyword">return</span> <span class="keyword">$data</span>;</code>
<code>}</code>
            </pre>
        </div>
        
        <p class="font-bold text-sm mt-4">Solution Code (<code>conn.php</code>, `get_all_teams` function):</p>
        <div class="code-container">
            <pre class="code-snippet">
<code><span class="php-tag">&lt;?php</span></code>
<code><span class="keyword">function</span> <span class="keyword">get_all_teams</span>(<span class="keyword">$conn</span>) {</code>
<code>    <span class="keyword">$query</span> <span class="keyword">=</span> <span class="string">"SELECT t.*, COUNT(d.id) as driver_count </span></code>
<code><span class="string">              FROM teams t </span></code>
<code><span class="string">              LEFT JOIN drivers d ON t.name = d.team_name </span></code>
<code><span class="string">              GROUP BY t.id</span></code>
<code><span class="string">              ORDER BY t.id ASC"</span>;</code>
<code>    <span class="comment">// The database calculates all counts in one efficient query.</span></code>
<code>    <span class="keyword">$result</span> <span class="keyword">=</span> <span class="keyword">$conn</span>-&gt;query(<span class="keyword">$query</span>);</code>
<code>    <span class="comment">// ... PHP processing loop remains outside for clarity ...</span></code>
<code>}</code>
            </pre>
        </div>

        <!-- Comparison 2: Latest Results Update Fix --><h3 class="text-xl font-bold mb-4 text-white mt-8" id="c2-latest-results-fix">C2: Latest Result Update Fix</h3>
        <p class="mb-4 text-gray-300">The latest results page did not update correctly after an admin changed race data. This happened because the system only checked the `round_number` or the simple `date`. Now, the system checks both the **`date`** and the internal race **`id`**. This guarantees the display always shows the latest completed race stably.</p>

        <p class="font-bold text-sm mt-4">Problematic Code (<code>conn.php</code>, `get_latest_race_result` query snippet):</p>
        <div class="code-container">
            <pre class="code-snippet">
<code><span class="php-tag">&lt;?php</span></code>
<code><span class="keyword">function</span> <span class="keyword">get_latest_race_result</span>(<span class="keyword">$conn</span>) {</code>
<code>    <span class="keyword">$race_query</span> <span class="keyword">=</span> <span class="string">"SELECT id, name, ... </span></code>
<code><span class="string">                   FROM races WHERE is_completed = TRUE </span></code>
<code><span class="string">                   ORDER BY date DESC LIMIT 1"</span>; <span class="comment">// Unstable sorting if dates are the same.</span></code>
<code>    <span class="comment">// ...</span></code>
            </pre>
        </div>

        <p class="font-bold text-sm mt-4">Solution Code (<code>conn.php</code>, `get_latest_race_result` query snippet):</p>
        <div class="code-container">
            <pre class="code-snippet">
<code><span class="php-tag">&lt;?php</span></code>
<code><span class="keyword">function</span> <span class="keyword">get_latest_race_result</span>(<span class="keyword">$conn</span>) {</code>
<code>    <span class="keyword">$race_query</span> <span class="keyword">=</span> <span class="string">"SELECT id, name, ... </span></code>
<code><span class="string">                   FROM races WHERE is_completed = TRUE </span></code>
<code><span class="string">                   ORDER BY date DESC, id DESC LIMIT 1"</span>; <span class="comment">// Stable sorting applied.</span></code>
<code>    <span class="comment">// ...</span></code>
            </pre>
        </div>

        <!-- Comparison 3: F1 Point System Integration --><h3 class="text-xl font-bold mb-4 text-white mt-8" id="c3-point-system">C3: F1 Point System Integration</h3>
        <p class="mb-4 text-gray-300">The administration interface no longer requires manual point entry. The system now uses the **official F1 point map** to calculate points automatically. When an admin enters a driver's position (1-10), the code injects the corresponding points into the database during the save operation. This makes result entry faster and prevents errors.</p>

        <p class="font-bold text-sm mt-4">Key Logic Change (<code>admin_races.php</code>, `save_results` POST handler):</p>
        <div class="code-container">
            <pre class="code-snippet">
<code><span class="php-tag">&lt;?php</span></code>
<code>/* In admin_races.php */</code>
<code><span class="keyword">$F1_RACE_POINTS</span> <span class="keyword">=</span> [<span class="number">1</span> <span class="keyword">=></span> <span class="number">25</span>, <span class="number">2</span> <span class="keyword">=></span> <span class="number">18</span>, <span class="number">3</span> <span class="keyword">=></span> <span class="number">15</span>, <span class="comment">... 10 => 1]</span>;</code>
<code><span class="keyword">if</span> (<span class="keyword">$race_id</span>) {</code>
<code>    <span class="keyword">foreach</span> (<span class="keyword">$driver_ids</span> <span class="keyword">as</span> <span class="keyword">$key</span> <span class="keyword">=></span> <span class="keyword">$driver_id</span>) {</code>
<code>        <span class="keyword">$position</span> <span class="keyword">=</span> (int)<span class="keyword">$positions</span>[<span class="keyword">$key</span>];</code>
<code>        <span class="keyword">$point</span> <span class="keyword">=</span> <span class="keyword">$F1_RACE_POINTS</span>[<span class="keyword">$position</span>] ?? <span class="number">0</span>; <span class="comment">// Automatic Calculation of points.</span></code>
<code>        <span class="keyword">if</span> (<span class="keyword">$position</span> <span class="keyword">></span> <span class="number">0</span>) {</code>
<code>            <span class="keyword">insert_or_update_race_result</span>(<span class="keyword">$conn</span>, <span class="keyword">$race_id</span>, <span class="keyword">$driver_id</span>, <span class="keyword">$position</span>, <span class="keyword">$point</span>);</code>
<code>        }</code>
<code>    }</code>
<code>}</code>
            </pre>
        </div>

        <!-- Comparison 4: Admin UI Consistency Fixes --><h3 class="text-xl font-bold mb-4 text-white mt-8" id="c4-admin-ui-fix">C4: Admin UI Consistency Fixes</h3>
        <p class="mb-4 text-gray-300">Layout issues were fixed to improve the user experience across all devices. We ensured consistent centering, corrected the administrative sidebar, and fixed vertical alignment problems in the race results table.</p>

        <hr class="border-gray-700 my-8">

        <!-- 4. Future Optimization --><h2 class="text-3xl font-bold mb-6 text-hotpink" id="future-optimization">4. Future Optimization Recommendations</h2>
        <p class="mb-4">The following mechanism can still be optimized for faster performance.</p>
        
        <h4 class="text-xl font-semibold mt-4 text-white">Redundant PHP Ranking in `get_driver_standings_data`</h4>
        <p class="text-gray-300">The system already calculates and **stores** the driver's rank in the database. However, the display function runs a redundant PHP loop to manually assign the rank again. Removing this extra PHP processing will reduce unnecessary work when loading the standings pages.</p>
        
        <p class="font-bold text-sm mt-4">Recommendation (Code to remove):</p>
        <div class="code-container">
            <pre class="code-snippet">
<code><span class="keyword">function</span> <span class="keyword">get_driver_standings_data</span>(<span class="keyword">$conn</span>) {</code>
<code>    <span class="comment">// ... SQL Query fetches d.standing_position ...</span></code>
<code>    <span class="keyword">$data</span> <span class="keyword">=</span> [];</code>
<code>    <span class="keyword">$rank</span> <span class="keyword">=</span> <span class="number">1</span>; <span class="comment">// &lt;--- Redundant Variable</span></code>
<code>    <span class="keyword">while</span> (<span class="keyword">$row</span> <span class="keyword">=</span> <span class="keyword">$result</span>-&gt;fetch_assoc()) {</code>
<code>        <span class="keyword">$row</span>[<span class="string">'standing_position'</span>] <span class="keyword">=</span> <span class="keyword">$rank</span>++; <span class="comment">// &lt;--- Redundant Re-ranking</span></code>
<code>        <span class="keyword">$data</span>[] <span class="keyword">=</span> <span class="keyword">$row</span>;</code>
<code>    }</code>
<code>    <span class="keyword">return</span> <span class="keyword">$data</span>;</code>
<code>}</code>
            </pre>
        </div>
    </div>
</body>
</html>