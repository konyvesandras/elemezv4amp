<?php
function karaktercsere_folio(string $szoveg): string {
    return karaktercsere_spec(karaktercsere_alap($szoveg));
}

function karaktercsere_alap(string $szoveg): string {
    $cserek = [
        '“' => '"', '”' => '"', '‘' => "'", '’' => "'",
        '–' => '-', '—' => ' — ', '…' => '...', ' ' => ' ',
        '„' => '"', '«' => '"', '»' => '"', '›' => "'", '‹' => "'",
        "\r\n" => "\n", "\r" => "\n", '  ' => ' ',
    ];
    return strtr($szoveg, $cserek);
}

function karaktercsere_spec(string $szoveg): string {
    $csere = [
        '¦'=>'ī','Ą'=>'ṛ','Ý'=>'Ā','±'=>'ṭ','Ł'=>'ṇ','ˇ'=>'ū','°'=>'ṁ',
        '¤'=>'ḥ','§'=>'ā','Ş'=>'ś','ń'=>'ñ','˘'=>'ṣ','Ż'=>'ṅ','¥¢'=>'ṅ',
        'Ľ'=>'Ś','¼'=>'Ś','ľ'=>'Ī','ª'=>'ś','¢'=>'ṣ','£'=>'ṇ','¨'=>'ḍ'
    ];
    return strtr($szoveg, $csere);
}
