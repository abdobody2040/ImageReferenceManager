<?php
// Events list page

// Get query parameters for filtering
$search = $_GET['search'] ?? '';
$type_id = $_GET['type'] ?? '';
$status = $_GET['status'] ?? '';
$format = $_GET['format'] ?? '';
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 12; // Number of events per page

// Build the query
$query = "SELECT e.*, et.name as event_type FROM event e 
          LEFT JOIN event_type et ON e.event_type_id = et.id";

$where_clauses = [];
$params = [];

// Add search condition
if (!empty($search)) {
    $where_clauses[] = "(e.name LIKE ? OR e.description LIKE ? OR e.requester_name LIKE ?)";
    $search_term = "%$search%";
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
}

// Add type filter
if (!empty($type_id)) {
    $where_clauses[] = "e.event_type_id = ?";
    $params[] = $type_id;
}

// Add status filter
if (!empty($status)) {
    $where_clauses[] = "e.status = ?";
    $params[] = $status;
}

// Add format filter
if ($format === 'online') {
    $where_clauses[] = "e.is_online = TRUE";
} elseif ($format === 'in-person') {
    $where_clauses[] = "e.is_online = FALSE";
}

// Add date range filters
if (!empty($date_from)) {
    $where_clauses[] = "e.start_datetime >= ?";
    $params[] = $date_from . ' 00:00:00';
}

if (!empty($date_to)) {
    $where_clauses[] = "e.start_datetime <= ?";
    $params[] = $date_to . ' 23:59:59';
}

// For medical reps, only show their own events and approved events
if (isMedicalRep()) {
    $where_clauses[] = "(e.user_id = ? OR e.status = 'approved')";
    $params[] = $_SESSION['user_id'];
}

// Combine where clauses
if (!empty($where_clauses)) {
    $query .= " WHERE " . implode(" AND ", $where_clauses);
}

// Add order by
$query .= " ORDER BY e.start_datetime DESC";

// Use pagination function to get results
$pagination = paginate($query, $params, $page, $per_page);
$events = $pagination['items'];

// Get event types for filter dropdown
$stmt = $pdo->query("SELECT id, name FROM event_type ORDER BY name");
$event_types = $stmt->fetchAll();

// Include view
include 'views/events/index.php';
?>