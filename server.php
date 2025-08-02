<?php
// Enhanced Game Library Server
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set headers for CORS and JSON
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');

// Configuration
define('DATABASE_FILE', __DIR__ . '/database.json');
define('LOG_FILE', __DIR__ . '/server.log');
define('MAX_GAMES', 1000);

// Logging function
function logMessage($message, $level = 'INFO') {
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[$timestamp] [$level] $message" . PHP_EOL;
    file_put_contents(LOG_FILE, $logEntry, FILE_APPEND | LOCK_EX);
}

// Response helper
function sendResponse($data, $status = 200) {
    http_response_code($status);
    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    exit;
}

// Error handler
function sendError($message, $status = 500) {
    logMessage("ERROR: $message", 'ERROR');
    sendResponse(['error' => $message, 'success' => false], $status);
}

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Load database
function loadDatabase() {
    if (!file_exists(DATABASE_FILE)) {
        logMessage("Database file not found, creating new one", 'WARNING');
        $initialData = [
            'games' => [],
            'metadata' => [
                'totalGames' => 0,
                'lastUpdated' => date('c'),
                'version' => '1.0'
            ]
        ];
        saveDatabase($initialData);
        return $initialData;
    }
    
    $content = file_get_contents(DATABASE_FILE);
    if ($content === false) {
        sendError("Cannot read database file");
    }
    
    $data = json_decode($content, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        logMessage("JSON decode error: " . json_last_error_msg(), 'ERROR');
        sendError("Database file is corrupted");
    }
    
    return $data;
}

// Save database
function saveDatabase($data) {
    $data['metadata']['lastUpdated'] = date('c');
    $data['metadata']['totalGames'] = count($data['games']);
    
    $jsonData = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    if ($jsonData === false) {
        sendError("Failed to encode data to JSON");
    }
    
    // Create backup
    if (file_exists(DATABASE_FILE)) {
        copy(DATABASE_FILE, DATABASE_FILE . '.backup');
    }
    
    if (file_put_contents(DATABASE_FILE, $jsonData, LOCK_EX) === false) {
        sendError("Failed to save database");
    }
    
    logMessage("Database saved successfully", 'INFO');
    return true;
}

// Validate game data
function validateGameData($gameData) {
    if (!isset($gameData['url']) || empty(trim($gameData['url']))) {
        return "URL is required";
    }
    
    if (!filter_var($gameData['url'], FILTER_VALIDATE_URL)) {
        return "Invalid URL format";
    }
    
    if (!isset($gameData['title']) || empty(trim($gameData['title']))) {
        return "Title is required";
    }
    
    if (strlen($gameData['title']) > 100) {
        return "Title too long (max 100 characters)";
    }
    
    return null;
}

// Get all games
function getGames() {
    logMessage("Getting all games", 'INFO');
    $database = loadDatabase();
    
    sendResponse([
        'success' => true,
        'games' => $database['games'],
        'metadata' => $database['metadata']
    ]);
}

// Add new game
function addGame() {
    $input = file_get_contents('php://input');
    if ($input === false) {
        sendError("Cannot read input data", 400);
    }
    
    $gameData = json_decode($input, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        sendError("Invalid JSON data", 400);
    }
    
    $validation = validateGameData($gameData);
    if ($validation !== null) {
        sendError($validation, 400);
    }
    
    $database = loadDatabase();
    
    // Check if we're at max capacity
    if (count($database['games']) >= MAX_GAMES) {
        sendError("Maximum number of games reached", 400);
    }
    
    // Check for duplicate URLs
    foreach ($database['games'] as $existingGame) {
        if ($existingGame['url'] === $gameData['url']) {
            sendError("Game with this URL already exists", 400);
        }
    }
    
    // Create new game
    $newGame = [
        'id' => 'game_' . time() . '_' . rand(1000, 9999),
        'title' => trim($gameData['title']),
        'url' => trim($gameData['url']),
        'icon' => isset($gameData['icon']) ? trim($gameData['icon']) : null,
        'addedAt' => date('c'),
        'addedBy' => 'User'
    ];
    
    $database['games'][] = $newGame;
    saveDatabase($database);
    
    logMessage("Added new game: " . $newGame['title'], 'INFO');
    
    sendResponse([
        'success' => true,
        'message' => 'Game added successfully',
        'game' => $newGame,
        'totalGames' => count($database['games'])
    ]);
}

// Delete game
function deleteGame() {
    $input = file_get_contents('php://input');
    if ($input === false) {
        sendError("Cannot read input data", 400);
    }
    
    $data = json_decode($input, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        sendError("Invalid JSON data", 400);
    }
    
    if (!isset($data['id']) || empty($data['id'])) {
        sendError("Game ID is required", 400);
    }
    
    $database = loadDatabase();
    $gameFound = false;
    $gameTitle = '';
    
    $database['games'] = array_filter($database['games'], function($game) use ($data, &$gameFound, &$gameTitle) {
        if ($game['id'] === $data['id']) {
            $gameFound = true;
            $gameTitle = $game['title'];
            return false;
        }
        return true;
    });
    
    if (!$gameFound) {
        sendError("Game not found", 404);
    }
    
    // Reindex array
    $database['games'] = array_values($database['games']);
    saveDatabase($database);
    
    logMessage("Deleted game: $gameTitle", 'INFO');
    
    sendResponse([
        'success' => true,
        'message' => 'Game deleted successfully',
        'totalGames' => count($database['games'])
    ]);
}

// Get server status
function getStatus() {
    $database = loadDatabase();
    
    sendResponse([
        'success' => true,
        'status' => 'online',
        'totalGames' => count($database['games']),
        'lastUpdated' => $database['metadata']['lastUpdated'],
        'version' => $database['metadata']['version'],
        'maxGames' => MAX_GAMES,
        'serverTime' => date('c')
    ]);
}

// Main router
try {
    $method = $_SERVER['REQUEST_METHOD'];
    $path = isset($_GET['action']) ? $_GET['action'] : '';
    
    logMessage("$method request to action: $path", 'INFO');
    
    switch ($method) {
        case 'GET':
            if ($path === 'status') {
                getStatus();
            } else {
                getGames();
            }
            break;
            
        case 'POST':
            addGame();
            break;
            
        case 'DELETE':
            deleteGame();
            break;
            
        default:
            sendError("Method not allowed", 405);
    }
} catch (Exception $e) {
    logMessage("Unexpected error: " . $e->getMessage(), 'ERROR');
    sendError("Internal server error: " . $e->getMessage());
}
?>