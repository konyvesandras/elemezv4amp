<?php
require_once __DIR__ . '/includes/text.php';
require_once __DIR__ . '/includes/analyze.php';
require_once __DIR__ . '/includes/highlight.php';


/**
 * Betölti a szöveget egy fájlból, soronként megtisztítva.
 *
 * @param string $file A fájlnév (pl. 'szoveg.txt').
 * @return array A fájl sorai tömbként, üres sorok nélkül.
 */
function load_text($file) {
    $path = __DIR__ . '/../data/' . basename($file);
    if (!file_exists($path)) return [];
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    return array_map('trim', $lines);
}

/**
 * Elemzi a megadott szót és visszaadja annak jellemzőit.
 *
 * @param string $word A vizsgált szó.
 * @return array A szó elemzési eredményei (szöveg, tooltip, fordítás).
 */
function analyze_word($word) {
    return [
        'text'     => $word,
        'tooltip'  => 'Elemzés itt',
        'forditas' => translate_word($word)
    ];
}

/**
 * Lefordítja a megadott szót az előre definiált szótár alapján.
 *
 * @param string $word A lefordítandó szó.
 * @return string|null A fordítás vagy null, ha nincs találat.
 */
function translate_word($word) {
    $dict = ['Isten' => 'God', 'ember' => 'man'];
    return $dict[$word] ?? null;
}

/**
 * Betölti a kiemelt szavakat a JSON fájlból.
 *
 * @return array A kiemelt szavak tömbként.
 */
function load_highlights() {
    $path = __DIR__ . '/../data/kiemeltek.json';
    if (!file_exists($path)) return [];
    $json = file_get_contents($path);
    return json_decode($json, true) ?: [];
}

/**
 * Elmenti a kiemelt szavakat a JSON fájlba.
 *
 * @param array $words A kiemelendő szavak tömbje.
 * @return void
 */
function save_highlights($words) {
    $path = __DIR__ . '/../data/kiemeltek.json';
    file_put_contents(
        $path,
        json_encode(array_values(array_unique($words)), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
    );
}

function get_unique_words(array $lines) {
    $words = [];

    foreach ($lines as $line) {
        // Szavak kigyűjtése: szóközök mentén, írásjelek eltávolítása
        $tokens = preg_split('/\s+/', $line);
        foreach ($tokens as $token) {
            // Normalizálás: kisbetűs, írásjelek nélkül
            $clean = mb_strtolower(trim($token));
            $clean = preg_replace('/[^\p{L}]/u', '', $clean); // csak betűk maradnak
            if ($clean !== '') {
                $words[] = $clean;
            }
        }
    }

    // Egyedi szavak visszaadása
    return array_values(array_unique($words));
}
