<style>
.kiemelt {
    background-color: rgb(46, 139, 87);
    color: rgb(255, 255, 255);
}
.noun { background-color: #ffd700; }
.verb { background-color: #87ceeb; }
.adjective { background-color: #98fb98; }
.adverb { background-color: #dda0dd; }
.conjunction { background-color: #ff7f50; }
</style>
<?php
// 📂 Nyelvtani adatok betöltése nyelvtan.json-ból
$nyelvtanAdatok = json_decode(file_get_contents(__DIR__ . '/data/nyelvtan.json'), true) ?? [];
$nyelvtanSzofajok = [];

// 🧠 Szófajfordító szótár: angol → magyar
$szofajForditas = [
    'noun' => 'főnév',
    'verb' => 'ige',
    'adjective' => 'melléknév',
    'adverb' => 'határozószó',
    'pronoun' => 'névmás',
    'conjunction' => 'kötőszó',
    'particle' => 'partikula',
    'postposition' => 'névutó',
    'interjection' => 'indulatszó',
    'sublative' => 'alárendelő',
    'instrumental' => 'eszközhatározó',
    'ablative' => 'elöljárószó',
    'temporal' => 'időhatározó',
    'translative' => 'állapothatározó',
    'adessive' => 'helyhatározó',
    'allative' => 'irányhatározó',
    'elative' => 'kihatározó',
    'sandhi-marker' => 'szanszkrit-határjel'
];

// 🔁 Nyelvtan.json szófajainak normalizálása és fordítása
foreach ($nyelvtanAdatok as $szo => $info) {
    $kulcs = normalizal((string)$szo);
    if ($kulcs !== '' && isset($info['type'])) {
        $angolType = mb_strtolower((string)$info['type'], 'UTF-8');
        $magyarType = $szofajForditas[$angolType] ?? $angolType; // ha nincs fordítás, marad eredeti
        $nyelvtanSzofajok[$kulcs] = $angolType;
    }
}


// 📄 Szöveg betöltése az elemzéshez
$lines = file(__DIR__ . '/txt/elemzes.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
if ($lines === false) {
    http_response_code(500);
    echo '<p>Hiba: nem sikerült betölteni az elemzes.txt fájlt.</p>';
    exit;
}

$filteredLines = [];
foreach ($lines as $line) {
    
        $visibleLines[] = $line;
}

foreach ($visibleLines as $line) {
    // Szavak és szóközök szétválasztása
    $tokens = preg_split('/(\s+)/u', $line, -1, PREG_SPLIT_DELIM_CAPTURE);
    if ($tokens === false) continue;

    $out = '';
	
    foreach ($tokens as $token) {
		if (isset($nyelvtanSzofajok[$token]))
			echo ' <span class="'.$nyelvtanSzofajok[$token].'">'.$token.'</span> ';
			else ' '.$token;
			}
}			







// 🧹 Szó normalizálása (kisbetűs, írásjel és szóköz nélkül, ASCII közelítés)
function normalizal(string $szo): string {
    $szo = mb_strtolower(trim($szo), 'UTF-8');
    $szo = preg_replace('/[.,!?;:"()„”’‘\'`´\-–—]/u', '', $szo);
    $szo = preg_replace('/\s+/u', '', $szo);

	// Ha teljesen devanagari, hagyjuk eredetiben
	if (preg_match('/^\p{Devanagari}+$/u', $szo)) {
		return $szo;
	}

	$trans = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $szo);
	return $trans !== false ? $trans : $szo;

}
