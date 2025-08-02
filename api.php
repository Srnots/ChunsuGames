<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

$gamesFile = 'games.json';

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

function readGames() {
    global $gamesFile;
    if (!file_exists($gamesFile)) {
        return [
            'games' => [],
            'lastUpdated' => date('c'),
            'totalGames' => 0
        ];
    }
    $content = file_get_contents($gamesFile);
    return json_decode($content, true);
}

function writeGames($data) {
    global $gamesFile;
    $data['lastUpdated'] = date('c');
    $data['totalGames'] = count($data['games']);
    return file_put_contents($gamesFile, json_encode($data, JSON_PRETTY_PRINT));
}

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        // Get all games
        $games = readGames();
        echo json_encode($games);
        break;
        
    case 'POST':
        // Add a new game
        $input = json_decode(file_get_contents('php://input'), true);
        $games = readGames();
        
        $newGame = [
            'id' => time() . rand(1000, 9999),
            'url' => $input['url'],
            'title' => $input['title'],
            'icon' => $input['icon'] ?? null,
            'addedAt' => date('c')
        ];
        
        $games['games'][] = $newGame;
        
        if (writeGames($games)) {
            echo json_encode(['success' => true, 'game' => $newGame]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to save game']);
        }
        break;
        
    case 'DELETE':
        // Delete a game
        $input = json_decode(file_get_contents('php://input'), true);
        $games = readGames();
        
        $gameId = $input['id'];
        $games['games'] = array_filter($games['games'], function($game) use ($gameId) {
            return $game['id'] != $gameId;
        });
        
        // Reindex array
        $games['games'] = array_values($games['games']);
        
        if (writeGames($games)) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to delete game']);
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}
?>