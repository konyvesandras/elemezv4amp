<?php
// 📄 Bemeneti fájl elérési útja
$input_file = './data/elemzes.txt'; // Ez a szövegfájl, amit feldolgozunk
$output_file = './data/szokivonat.json'; // Ide mentjük a kivonatolt szavakat JSON formátumban

// 🧠 Karaktercsere a hibás vagy nem szabványos karakterekhez
function karaktercsere_folio($text) {
    // Egyes hibás karakterek cseréje (pl. konvertált PDF-ből származó karakterek)
    if (strpos($text, "î") > 0) {
        $text = str_replace('î', 'í', $text); // egyszerűsített magyar ékezet javítás
    }

    // Egyéni karakterek cseréje szabványos Unicode megfelelőkre
    $csere = [
        '¦' => 'ī', 'Ą' => 'ṛ', 'Ý' => 'Ā', '¥¢' => 'ṛṣ', '±' => 'ṭ',
        'Ľ' => 'Ś', '¼' => 'Ś', 'ľ' => 'Ī', 'Ş' => 'ś', 'ª' => 'ś',
        '˘' => 'ṣ', '¢' => 'ṣ', 'Ł' => 'ṇ', '£' => 'ṇ', 'ˇ' => 'ū',
        '°' => 'ṁ', 'ń' => 'ñ', 'Ż' => 'ṅ', '¨' => 'ḍ', '¤' => 'ḥ',
        '—' => ' — ', '§' => 'ā' // hosszú gondolatjel elválasztóként
    ];

    // Végigmegyünk a csere-táblán és lecseréljük a karaktereket
    foreach ($csere as $kulcs => $ertek) {
        $text = str_replace($kulcs, $ertek, $text);
    }

    return $text;
}

// 📦 Szöveg beolvasása és karaktercsere alkalmazása
$szoveg = file_get_contents($input_file); // Beolvassuk a teljes szöveget
$szoveg = karaktercsere_folio($szoveg);   // Lefuttatjuk a karaktercserét

// 🔍 Szöveg darabolása magyar fordítások mentén (pl. " — " elválasztóval)
$forditasok = []; // Ebbe gyűjtjük a szavakat
$blokkok = explode(' — ', $szoveg); // Feldaraboljuk a szöveget blokkokra

foreach ($blokkok as $resz) {
    // 🔧 Kisbetűsítés (csak latin betűkre hat)
    $resz = mb_strtolower($resz, 'UTF-8');

    // 🔧 Írásjelek eltávolítása, de megtartjuk minden betűt és ékezetet
    // \p{L} = minden betű, \p{M} = diakritikus jelek, \s = szóköz
    $resz = preg_replace('/[^\p{L}\p{M}\s]/u', ' ', $resz);

    // 🔧 Többszörös szóközök egységesítése
    $resz = preg_replace('/\s+/', ' ', $resz);

    // 🔍 Szavak kinyerése szóközök mentén
    $szavak = preg_split('/\s+/', trim($resz));

    // 🧠 Szavak hozzáadása a kivonathoz
    foreach ($szavak as $szo) {
        if (mb_strlen($szo, 'UTF-8') >= 1) { // Legalább 1 karakter hosszú legyen
            $forditasok[$szo] = new stdClass(); // Üres objektumot rendelünk hozzá
        }
    }
}

// 📝 JSON fájl mentése
file_put_contents($output_file, json_encode($forditasok, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

// ✅ Visszajelzés a parancssorban
echo "✅ Szókivonat mentve: " . count($forditasok) . " szó a '$output_file' fájlban.\n";
?>
