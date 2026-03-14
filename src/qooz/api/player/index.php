<?php
require_once __DIR__ . '/../init.php';

$method = $_SERVER['REQUEST_METHOD'];

// Join game
if ($method === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'join') {
        $pin = $_POST['pin'] ?? '';
        $nama = $_POST['nama'] ?? '';
        
        if (!$pin || !$nama) {
            response(['error' => 'Data tidak lengkap'], 400);
        }
        
        // Find session
        $result = conn()->query("SELECT * FROM game_sessions WHERE pin = '$pin' AND status != 'finished'");
        $session = $result->fetch_assoc();
        
        if (!$session) {
            response(['error' => 'Game tidak ditemukan'], 404);
        }
        
        if ($session['status'] === 'finished') {
            response(['error' => 'Game sudah selesai'], 400);
        }
        
        $id = generateUUID();
        
        $stmt = conn()->prepare("INSERT INTO players (id, session_id, nama_siswa, skor_total) VALUES (?, ?, ?, 0)");
        $stmt->bind_param('sss', $id, $session['id'], $nama);
        
        if ($stmt->execute()) {
            response([
                'success' => true,
                'player' => [
                    'id' => $id,
                    'session_id' => $session['id'],
                    'nama_siswa' => $nama,
                    'skor_total' => 0
                ]
            ]);
        } else {
            response(['error' => 'Gagal join game'], 500);
        }
    }
    
    // Submit answer
    if ($action === 'answer') {
        $playerId = $_POST['player_id'] ?? '';
        $questionId = $_POST['question_id'] ?? '';
        $sessionId = $_POST['session_id'] ?? '';
        $jawaban = $_POST['jawaban'] ?? 0;
        $waktuMs = $_POST['waktu_ms'] ?? 0;
        
        error_log("answer action: player_id=$playerId, question_id=$questionId, session_id=$sessionId, jawaban=$jawaban, waktuMs=$waktuMs");
        
        if (!$playerId || !$questionId || !$sessionId) {
            response(['error' => 'Data tidak lengkap'], 400);
        }
        
        // Check if already answered
        $check = conn()->query("SELECT id FROM answers WHERE player_id = '$playerId' AND question_id = '$questionId'");
        if ($check->num_rows > 0) {
            response(['error' => 'Sudah menjawab'], 400);
        }
        
        $id = generateUUID();
        
        $stmt = conn()->prepare("INSERT INTO answers (id, player_id, question_id, session_id, jawaban_dipilih, waktu_respon_ms) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('sssiis', $id, $playerId, $questionId, $sessionId, $jawaban, $waktuMs);
        
        if ($stmt->execute()) {
            error_log("Answer inserted successfully: id=$id, jawaban=$jawaban");
            response(['success' => true]);
        } else {
            error_log("Answer insert failed: " . $stmt->error);
            response(['error' => 'Gagal submit jawaban'], 500);
        }
    }
    
    // Get my score
    if ($action === 'score') {
        $playerId = $_POST['player_id'] ?? '';
        
        $result = conn()->query("SELECT * FROM players WHERE id = '$playerId'");
        $player = $result->fetch_assoc();
        
        if (!$player) {
            response(['error' => 'Player tidak ditemukan'], 404);
        }
        
        // Get rank
        $rankResult = conn()->query("SELECT id FROM players WHERE session_id = '{$player['session_id']}' ORDER BY skor_total DESC");
        $rank = 1;
        while ($row = $rankResult->fetch_assoc()) {
            if ($row['id'] === $playerId) break;
            $rank++;
        }
        
        $totalResult = conn()->query("SELECT COUNT(*) as total FROM players WHERE session_id = '{$player['session_id']}'");
        $total = $totalResult->fetch_assoc()['total'];
        
        response([
            'player' => $player,
            'rank' => $rank,
            'total' => $total
        ]);
    }
}

// Get player state (for polling)
if ($method === 'GET') {
    $action = $_GET['action'] ?? '';
    
    if ($action === 'state') {
        $playerId = $_GET['player_id'] ?? '';
        
        if (!$playerId) {
            response(['error' => 'Player ID required'], 400);
        }
        
        $result = conn()->query("SELECT * FROM players WHERE id = '$playerId'");
        $player = $result->fetch_assoc();
        
        if (!$player) {
            response(['error' => 'Player not found'], 404);
        }
        
        // Get session
        $sResult = conn()->query("SELECT * FROM game_sessions WHERE id = '{$player['session_id']}'");
        $session = $sResult->fetch_assoc();
        
        // Get current question
        $question = null;
        if ($session['current_question_id']) {
            $qResult = conn()->query("SELECT * FROM questions WHERE id = '{$session['current_question_id']}'");
            $question = $qResult->fetch_assoc();
        }
        
        // Check if already answered
        $answered = false;
        if ($question) {
            $aResult = conn()->query("SELECT id FROM answers WHERE player_id = '$playerId' AND question_id = '{$question['id']}'");
            $answered = $aResult->num_rows > 0;
        }
        
        // Get my answer if exists
        $myAnswer = null;
        if ($question) {
            $maResult = conn()->query("SELECT * FROM answers WHERE player_id = '$playerId' AND question_id = '{$question['id']}'");
            $myAnswer = $maResult->fetch_assoc();
        }
        
        response([
            'player' => $player,
            'session' => $session,
            'question' => $question,
            'answered' => $answered,
            'my_answer' => $myAnswer,
            'timestamp' => time()
        ]);
    }
}

response(['error' => 'Method tidak valid'], 405);
