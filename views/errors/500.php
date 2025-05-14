<?php
$app_name = getSetting('app_name', 'PharmaEvents') ?? 'PharmaEvents';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error - <?php echo $app_name; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        body {
            background-color: #f8f9fc;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }
        
        .error-container {
            max-width: 800px;
            width: 100%;
        }
        
        .error-code {
            font-size: 6rem;
            font-weight: bold;
            color: #4e73df;
            opacity: 0.8;
        }
        
        .error-details {
            background-color: #f8f9fa;
            border-left: 4px solid #4e73df;
            padding: 10px;
            margin-top: 20px;
            overflow-x: auto;
        }
        
        .error-trace {
            font-family: monospace;
            font-size: 0.9rem;
            white-space: pre-wrap;
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="card shadow">
            <div class="card-body">
                <div class="text-center mb-4">
                    <div class="error-code">500</div>
                    <h1 class="h3 mb-3"><?php echo htmlspecialchars($title); ?></h1>
                    <p class="lead text-gray-800"><?php echo htmlspecialchars($message); ?></p>
                    <p class="mb-4">We apologize for the inconvenience. Please try again later or contact the administrator if the problem persists.</p>
                    
                    <div class="mb-4">
                        <a href="/" class="btn btn-primary"><i class="fas fa-home me-2"></i>Go to Home</a>
                        <a href="javascript:history.back()" class="btn btn-secondary ms-2"><i class="fas fa-arrow-left me-2"></i>Go Back</a>
                    </div>
                </div>
                
                <?php if (isset($show_details) && $show_details && !empty($details)): ?>
                <div class="error-details">
                    <h5 class="mb-2">Error Details</h5>
                    <?php if (isset($details['file'])): ?>
                    <div><strong>File:</strong> <?php echo htmlspecialchars($details['file']); ?></div>
                    <?php endif; ?>
                    
                    <?php if (isset($details['line'])): ?>
                    <div><strong>Line:</strong> <?php echo htmlspecialchars($details['line']); ?></div>
                    <?php endif; ?>
                    
                    <?php if (isset($details['trace'])): ?>
                    <div class="mt-3">
                        <strong>Stack Trace:</strong>
                        <div class="error-trace"><?php echo htmlspecialchars($details['trace']); ?></div>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>