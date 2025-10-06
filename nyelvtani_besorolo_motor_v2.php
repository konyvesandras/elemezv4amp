<?php
ini_set("memory_limit", "-1");
ini_set('max_execution_time', 0);

// 🧾 Log modul (opcionális, ha van log.php)
@include 'log.php';

// 📦 Többnyelvű sablonok betöltése
$sablonok = array_merge(
    include 'sablonok/sablonok_hu.php',
    include 'sablonok/sablonok_en.php',
    include 'sablonok/sablonok_de.php',
    include 'sablonok/sablonok_sa.php'
);

// 🔗 Bemeneti szókivonat
$szotar_url = './data/szokivonat.json';
$nyelvtan = []; // Eredmény tárolása

// 🌐 Szókivonat betöltése
$szotar_json = @file_get_contents($szotar_url);
if ($szotar_json === false) {
    log_hozzaad("❌ Hiba: Nem sikerült betölteni a szókivonatot: $szotar_url");
    die("❌ Hiba: Nem sikerült betölteni a szókivonatot. - txt2json_szokivonat.php");
}
$szotar = json_decode($szotar_json, true);

// ✅ Log
log_hozzaad("Szókivonat betöltve: " . count($szotar) . " szó");
log_hozzaad("Sablonok betöltve: " . count($sablonok) . " darab");

// 🔍 Nyelvfelismerés írásrendszer és diakritika alapján
function nyelv_felismeres($szo) {
    if (preg_match('/[\x{0900}-\x{097F}]/u', $szo)) return 'sa'; // devanāgarī
    if (preg_match('/[āīūṛṅñṭḍṇśṣḥṁ]/u', $szo)) return 'hi';    // hindi/szanszkrit latin
    if (preg_match('/[äöüß]/u', $szo)) return 'de';              // német
    if (preg_match('/[áéíóöőúüű]/u', $szo)) return 'hu';         // magyar
    if (preg_match('/^[a-zA-Z]+$/u', $szo)) return 'en';         // angol
    return 'unknown';
}

// 📐 Hosszabb sablonok előnyben: kulcsok újrasorrendezése
uksort($sablonok, function($a, $b) {
    return strlen($b) - strlen($a); // hosszabb sablon előre
});

// 🔁 Szavak feldolgozása
foreach ($szotar as $szo => $ures) {
    $talalat = false;

    // 🔎 Sablonillesztés végződés alapján
    foreach ($sablonok as $veg => $adat) {
        if (mb_substr($szo, -mb_strlen($veg)) === $veg) {
            $besorolas = $adat;

            // 🧠 Nyelvfelismerés felülvizsgálata
            $nyelv = nyelv_felismeres($szo);
            if ($nyelv !== $adat['lang']) {
                $besorolas['lang'] = $nyelv;
                $besorolas['lang_override'] = true;
                log_hozzaad("⚠️ Felülírás: '$szo' sablon szerint " . $adat['lang'] . ", nyelvfelismerés szerint " . $nyelv);
            }

            $nyelvtan[$szo] = $besorolas;
            $talalat = true;
            log_hozzaad("Illeszkedés: '$szo' → " . json_encode($besorolas, JSON_UNESCAPED_UNICODE));
            break;
        }
    }

    // 🔎 Ha nincs sablonillesztés, csak nyelvfelismerés
    if (!$talalat) {
        $nyelv = nyelv_felismeres($szo);
        $nyelvtan[$szo] = ['lang' => $nyelv, 'source' => 'nyelv_felismeres'];
        log_hozzaad("Nyelvfelismerés: '$szo' → $nyelv");
    }
}

// 📝 Mentés
file_put_contents('./data/nyelvtan.json', json_encode($nyelvtan, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

// ✅ Log
log_hozzaad("Nyelvtan.json mentve: " . count($nyelvtan) . " szóhoz.");
echo "✅ Nyelvtani besorolás kész: " . count($nyelvtan) . " szó feldolgozva.\n";
?>
