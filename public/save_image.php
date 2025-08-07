<?php
/**
 * Save Image PHP Script
 * Handles saving canvas-generated images to the server.
 */

// Prevent direct access to this file if not through proper POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.0 403 Forbidden');
    echo json_encode(['success' => false, 'error' => 'Direct access not allowed']);
    exit;
}

// Set headers for JSON response
header('Content-Type: application/json');

// Check if all required parameters are provided
if (!isset($_POST['image_data']) || !isset($_POST['name']) || !isset($_POST['ornament'])) {
    echo json_encode(['success' => false, 'error' => 'Missing required parameters']);
    exit;
}

try {
    // Get POST data
    $imageData = $_POST['image_data'];
    $name = $_POST['name'];
    $ornament = intval($_POST['ornament']);
    
    // Validate the image data (must be a valid base64 string)
    if (strpos($imageData, 'data:image/png;base64,') !== 0) {
        throw new Exception('Invalid image data format');
    }
    
    // Remove the "data:image/png;base64," part
    $imageData = str_replace('data:image/png;base64,', '', $imageData);
    
    // Decode the base64 data
    $imageData = base64_decode($imageData);
    if ($imageData === false) {
        throw new Exception('Failed to decode image data');
    }
    
    // Sanitize the filename - remove special characters and spaces
    $sanitizedName = preg_replace('/[^a-zA-Z0-9_]/', '_', $name);
    
    // Create unique filename using name, ornament number, and timestamp
    $filename = $sanitizedName . '_' . $ornament . '_' . time() . '.png';
    
    // Define the directory where images will be stored
    $uploadDir = 'calligraphy_images/';
    
    // Create directory if it doesn't exist
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            throw new Exception('Failed to create upload directory');
        }
    }
    
    // Full path for the new file
    $filePath = $uploadDir . $filename;
    
    // Save the image file
    if (file_put_contents($filePath, $imageData) === false) {
        throw new Exception('Failed to save image');
    }
    
    // Get the full URL to the saved image
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];
    $baseUrl = $protocol . $host . rtrim(dirname($_SERVER['PHP_SELF']), '/') . '/';
    $imageUrl = $baseUrl . $filePath;
    
    // Optional: Log image creation
    $logFile = $uploadDir . 'image_log.txt';
    $logEntry = date('Y-m-d H:i:s') . ' - Created: ' . $filename . ' - IP: ' . $_SERVER['REMOTE_ADDR'] . PHP_EOL;
    file_put_contents($logFile, $logEntry, FILE_APPEND);
    
    // Return success response with image URL
    echo json_encode([
        'success' => true, 
        'filename' => $filename,
        'url' => $imageUrl
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?> 
