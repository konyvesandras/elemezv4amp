<?php
require_once __DIR__ . '/inc/functions.php';

$file = 'elemzes.txt';
$dataDir = __DIR__ . '/data/';
$textPath = $dataDir . $file;
$stampPath = $dataDir . 'stamp.txt';

// Ellenőrizzük, hogy változott-e a szöveg
$currentStamp = file_exists($textPath) ? filemtime($textPath) : 0;
$lastStamp = file_exists($stampPath) ? (int)file_get_contents($stampPath) : 0;

if ($currentStamp === $lastStamp) {
    echo "Nincs változás. Előfeldolgozás kihagyva.\n";
    exit;
}

// Szöveg betöltése
$lines = load_text($file);
$words = get_unique_words($lines);

// Elemzések és fordítások előállítása
$analyze = [];
$translate = [];

foreach ($words as $word) {
    $info = analyze_word($word);
    $analyze[$word] = $info;
    $translate[$word] = $info['forditas'];
}

// JSON fájlok mentése
file_put_contents($dataDir . 'szokivonat.json', json_encode($words, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
file_put_contents($dataDir . 'analyze.json', json_encode($analyze, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
file_put_contents($dataDir . 'translate.json', json_encode($translate, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
file_put_contents($stampPath, $currentStamp);

echo "Előfeldolgozás kész: " . count($words) . " egyedi szó feldolgozva.\n";
