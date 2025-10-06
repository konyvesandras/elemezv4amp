<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../inc/functions.php';

$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input) || !isset($input['word'])) {
    echo json_encode(['error' => 'Hibás bemenet']);
    exit;
}

$word = trim($input['word']);
if ($word === '') {
    echo json_encode(['error' => 'Üres szó']);
    exit;
}

$highlights = load_highlights();

if (in_array($word, $highlights, true)) {
    // már benne van → töröljük
    $highlights = array_values(array_diff($highlights, [$word]));
    save_highlights($highlights);
    echo json_encode(['status' => 'removed', 'word' => $word]);
} else {
    // új kijelölés
    $highlights[] = $word;
    save_highlights($highlights);
    echo json_encode(['status' => 'added', 'word' => $word]);
}
