<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../inc/functions.php';


// Egyetlen szó elemzése (nem-AMP AJAX)
if (isset($_GET['word'])) {
    $word = trim($_GET['word']);
    if ($word === '') {
        echo json_encode(['error' => 'Nincs szó megadva']);
        exit;
    }
    $info = analyze_word($word);
    $info['highlighted'] = in_array($info['text'], load_highlights(), true);
    echo json_encode($info, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

// Teljes fájl feldolgozása (AMP-list)
if (isset($_GET['fajl'])) {
    $file  = basename($_GET['fajl']);
    $lines = load_text($file);
    $highlights = load_highlights();

    $result = ['sorok' => []];
    foreach ($lines as $line) {
        $words = preg_split('/\s+/', $line, -1, PREG_SPLIT_NO_EMPTY);
        $sor   = ['szavak' => []];
        foreach ($words as $w) {
    $info = analyze_word($w);
    $highlighted = in_array($info['text'], $highlights, true);

    if ($highlighted || $info['forditas']) {
        $info['highlighted'] = $highlighted;
        $sor['szavak'][] = $info;
    } else {
        // egyszerű szöveg
        $sor['szavak'][] = ['text' => $info['text']];
    }
}

        $result['sorok'][] = $sor;
    }

    echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

// Ha semmi nem jött
echo json_encode(['error' => 'Nincs megfelelő paraméter']);
