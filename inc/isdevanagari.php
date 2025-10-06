<?php
function isDevanagari($text) {
    return preg_match('/\p{Devanagari}/u', $text);
}

function transliterateDevanagariold($text) {
    $map = [
        'अ'=>'a','आ'=>'ā','इ'=>'i','ई'=>'ī','उ'=>'u','ऊ'=>'ū',
        'ऋ'=>'ṛ','ॠ'=>'ṝ','ऌ'=>'ḷ','ॡ'=>'ḹ',
        'ए'=>'e','ऐ'=>'ai','ओ'=>'o','औ'=>'au',
        'क'=>'ka','ख'=>'kha','ग'=>'ga','घ'=>'gha','ङ'=>'ṅa',
        'च'=>'ca','छ'=>'cha','ज'=>'ja','झ'=>'jha','ञ'=>'ña',
        'ट'=>'ṭa','ठ'=>'ṭha','ड'=>'ḍa','ढ'=>'ḍha','ण'=>'ṇa',
        'त'=>'ta','थ'=>'tha','द'=>'da','ध'=>'dha','न'=>'na',
        'प'=>'pa','फ'=>'pha','ब'=>'ba','भ'=>'bha','म'=>'ma',
        'य'=>'ya','र'=>'ra','ल'=>'la','व'=>'va',
        'श'=>'śa','ष'=>'ṣa','स'=>'sa','ह'=>'ha',
        'ं'=>'ṃ','ः'=>'ḥ','ँ'=>'̃','ऽ'=>"’",'्'=>'',
        '०'=>'0','१'=>'1','२'=>'2','३'=>'3','४'=>'4',
        '५'=>'5','६'=>'6','७'=>'7','८'=>'8','९'=>'9'
    ];
    $out = '';
    $chars = preg_split('//u', $text, -1, PREG_SPLIT_NO_EMPTY);
    foreach ($chars as $ch) {
        $out .= $map[$ch] ?? $ch;
    }
    return preg_replace('/a(\s|$)/u', '$1', $out);
}

function transliterateDevanagari($text) {
  // 🔁 Ligatúrák cseréje először
  $ligatures = [
    'ज्ञ' => 'jñ', 'त्र' => 'tr', 'श्र' => 'śr'
  ];
  foreach ($ligatures as $dev => $lat) {
    $text = str_replace($dev, $lat, $text);
  }

  // 🔤 Mātrā jelek (magánhangzó toldalékok)
  $matras = [
    'ा'=>'ā','ि'=>'i','ी'=>'ī','ु'=>'u','ू'=>'ū',
    'े'=>'e','ै'=>'ai','ो'=>'o','ौ'=>'au',
    'ृ'=>'ṛ','ॄ'=>'ṝ','ॅ'=>'ê','ॉ'=>'ô'
  ];

  // 🔡 Alap mássalhangzók (implicit "a" hanggal)
  $consonants = [
    'क'=>'k','ख'=>'kh','ग'=>'g','घ'=>'gh','ङ'=>'ṅ',
    'च'=>'c','छ'=>'ch','ज'=>'j','झ'=>'jh','ञ'=>'ñ',
    'ट'=>'ṭ','ठ'=>'ṭh','ड'=>'ḍ','ढ'=>'ḍh','ण'=>'ṇ',
    'त'=>'t','थ'=>'th','द'=>'d','ध'=>'dh','न'=>'n',
    'प'=>'p','फ'=>'ph','ब'=>'b','भ'=>'bh','म'=>'m',
    'य'=>'y','र'=>'r','ल'=>'l','व'=>'v',
    'श'=>'ś','ष'=>'ṣ','स'=>'s','ह'=>'h'
  ];

  // 🔣 Egyéb karakterek
  $vowels = [
    'अ'=>'a','आ'=>'ā','इ'=>'i','ई'=>'ī','उ'=>'u','ऊ'=>'ū',
    'ऋ'=>'ṛ','ॠ'=>'ṝ','ऌ'=>'ḷ','ॡ'=>'ḹ',
    'ए'=>'e','ऐ'=>'ai','ओ'=>'o','औ'=>'au'
  ];
  $symbols = [
    'ं'=>'ṃ','ः'=>'ḥ','ँ'=>'̃','ऽ'=>"’",'्'=>'', // virāma törli az "a"-t
    '०'=>'0','१'=>'1','२'=>'2','३'=>'3','४'=>'4',
    '५'=>'5','६'=>'6','७'=>'7','८'=>'8','९'=>'9'
  ];

  $chars = preg_split('//u', $text, -1, PREG_SPLIT_NO_EMPTY);
  $out = '';
  $prevConsonant = null;

  foreach ($chars as $i => $ch) {
    if (isset($vowels[$ch])) {
      $out .= $vowels[$ch];
      $prevConsonant = null;
    } elseif (isset($consonants[$ch])) {
      $prevConsonant = $consonants[$ch];
      $out .= $prevConsonant . 'a'; // implicit "a", ha nincs virāma vagy mātrā
    } elseif (isset($matras[$ch])) {
      if ($prevConsonant !== null) {
        // töröljük az előző "a"-t, és helyette jön a mātrā
        $out = mb_substr($out, 0, mb_strlen($out) - 1);
        $out .= $matras[$ch];
        $prevConsonant = null;
      } else {
        $out .= $matras[$ch]; // ha nincs előző mássalhangzó
      }
    } elseif ($ch === '्') {
      // virāma → töröljük az előző "a"-t
      if ($prevConsonant !== null) {
        $out = mb_substr($out, 0, mb_strlen($out) - 1);
        $prevConsonant = null;
      }
    } elseif (isset($symbols[$ch])) {
      $out .= $symbols[$ch];
      $prevConsonant = null;
    } else {
      $out .= $ch; // ismeretlen karakter
      $prevConsonant = null;
    }
  }

  return $out;
}



