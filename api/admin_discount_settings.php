<?php
/**
 * Admin Discount Settings API
 * Discount settings manage korar jonno
 */

session_start();
require_once __DIR__ . '/../auth/admin_auth.php';
require_once __DIR__ . '/../includes/admin_functions.php';

header('Content-Type: application/json');

// Admin logged in check kore
if (!isAdminLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

// GET - Discount settings fetch kore
if ($method === 'GET') {
    $settings = getAllDiscountSettings();
    $stats = getDiscountStats();
    
    // Settings ke key-value format e convert kore
    $settingsMap = [];
    foreach ($settings as $setting) {
        $settingsMap[$setting['setting_key']] = [
            'value' => $setting['setting_value'],
            'description' => $setting['description']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'settings' => $settingsMap,
        'stats' => $stats
    ]);
    exit;
}

// POST - Discount settings update kore
if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid data']);
        exit;
    }
    
    $adminId = $_SESSION['admin_id'] ?? null;
    
    // Allowed settings gulo check kore
    $allowedSettings = [
        'discount_enabled',
        'nth_order_small',
        'discount_small_percent',
        'nth_order_large',
        'discount_large_percent'
    ];
    
    $settingsToUpdate = [];
    foreach ($data as $key => $value) {
        if (in_array($key, $allowedSettings)) {
            // Validate values
            if ($key === 'discount_enabled') {
                $value = $value ? '1' : '0';
            } else {
                $value = intval($value);
                if ($value < 0) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => "Invalid value for $key"]);
                    exit;
                }
                // Percent 100 er beshi hote parbe na
                if (strpos($key, 'percent') !== false && $value > 100) {
                    $value = 100;
                }
            }
            $settingsToUpdate[$key] = $value;
        }
    }
    
    if (empty($settingsToUpdate)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'No valid settings provided']);
        exit;
    }
    
    if (updateMultipleDiscountSettings($settingsToUpdate, $adminId)) {
        echo json_encode([
            'success' => true,
            'message' => 'Discount settings updated successfully'
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to update settings']);
    }
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Method not allowed']);
?>
