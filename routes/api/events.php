<?php
// API endpoint for filtering and sorting events

// Set content type to JSON
header('Content-Type: application/json');

try {
    // Get query parameters
    $search = $_GET['search'] ?? '';
    $type_id = $_GET['type_id'] ?? '';
    $status = $_GET['status'] ?? '';
    $format = $_GET['format'] ?? '';
    $date_from = $_GET['date_from'] ?? '';
    $date_to = $_GET['date_to'] ?? '';
    $category_id = $_GET['category_id'] ?? '';
    $sort_by = $_GET['sort_by'] ?? 'start_datetime';
    $sort_order = $_GET['sort_order'] ?? 'desc';
    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
    $per_page = isset($_GET['per_page']) && is_numeric($_GET['per_page']) ? (int)$_GET['per_page'] : 10;
    
    // Validate and sanitize sort parameters
    $allowed_sort_fields = ['name', 'requester_name', 'start_datetime', 'created_at', 'status'];
    if (!in_array($sort_by, $allowed_sort_fields)) {
        $sort_by = 'start_datetime';
    }
    
    $sort_order = strtolower($sort_order) === 'asc' ? 'ASC' : 'DESC';
    
    // Build the query
    $query = "
        SELECT e.*, et.name as event_type_name, 
               v.name as venue_name, v.governorate as venue_governorate
        FROM event e
        LEFT JOIN event_type et ON e.event_type_id = et.id
        LEFT JOIN venue v ON e.venue_id = v.id
    ";
    
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
    
    // Add category filter
    if (!empty($category_id)) {
        $query .= " JOIN event_category_junction ecj ON e.id = ecj.event_id";
        $where_clauses[] = "ecj.category_id = ?";
        $params[] = $category_id;
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
    $query .= " ORDER BY e." . $sort_by . " " . $sort_order;
    
    // Use pagination function to get results
    $pagination = paginate($query, $params, $page, $per_page);
    
    // Get total categories count for filter dropdown
    $stmt = $pdo->query("SELECT COUNT(*) FROM event_category");
    $total_categories = $stmt->fetchColumn();
    
    // Get total event types count for filter dropdown
    $stmt = $pdo->query("SELECT COUNT(*) FROM event_type");
    $total_event_types = $stmt->fetchColumn();
    
    // Format response
    $response = [
        'events' => $pagination['items'],
        'pagination' => [
            'total' => $pagination['total'],
            'per_page' => $pagination['per_page'],
            'current_page' => $pagination['current_page'],
            'total_pages' => $pagination['total_pages'],
            'has_next' => $pagination['has_next'],
            'has_prev' => $pagination['has_prev']
        ],
        'filters' => [
            'categories_count' => $total_categories,
            'event_types_count' => $total_event_types
        ]
    ];
    
    // Return as JSON
    echo json_encode($response);
    
} catch (PDOException $e) {
    // Log error
    error_log('Error getting events: ' . $e->getMessage());
    
    // Return error
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    // Log error
    error_log('Error: ' . $e->getMessage());
    
    // Return error
    http_response_code(500);
    echo json_encode(['error' => 'Error: ' . $e->getMessage()]);
}
?>