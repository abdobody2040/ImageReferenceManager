<?php
// API endpoint for monthly events chart

// Set content type to JSON
header('Content-Type: application/json');

try {
    // Get current year and month
    $current_year = date('Y');
    $current_month = date('m');
    
    // Generate months for the past year
    $labels = [];
    $months = [];
    
    for ($i = 0; $i < 12; $i++) {
        $month = (int)$current_month - $i;
        $year = (int)$current_year;
        
        if ($month <= 0) {
            $month += 12;
            $year -= 1;
        }
        
        $date = new DateTime("$year-$month-01");
        $labels[] = $date->format('M Y');
        $months[] = [
            'year' => $year,
            'month' => $month,
            'start' => "$year-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-01",
            'end' => "$year-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-" . date('t', $date->getTimestamp())
        ];
    }
    
    // Reverse arrays to show oldest to newest
    $labels = array_reverse($labels);
    $months = array_reverse($months);
    
    // Query for events count by month
    $values = [];
    
    foreach ($months as $month) {
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as count
            FROM event
            WHERE start_datetime BETWEEN ? AND ?
            AND (status = 'approved' OR user_id = ? OR ? = TRUE)
        ");
        
        $stmt->execute([
            $month['start'] . ' 00:00:00',
            $month['end'] . ' 23:59:59',
            $_SESSION['user_id'],
            isAdmin()
        ]);
        
        $result = $stmt->fetch();
        $values[] = (int)$result['count'];
    }
    
    // Return as JSON
    echo json_encode([
        'labels' => $labels,
        'values' => $values
    ]);
    
} catch (Exception $e) {
    // Log error
    error_log('Error getting monthly data: ' . $e->getMessage());
    
    // Return error
    echo json_encode([
        'error' => 'Error: ' . $e->getMessage(),
        'labels' => [],
        'values' => []
    ]);
}
?>