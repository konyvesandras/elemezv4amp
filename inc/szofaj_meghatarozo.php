<?php
function meghataroz_szofaj_es_tooltip(string $szo, array $szotar_hu, array $szotar_en, array $suffixek, array $tooltipek): array {
    $tisztitott = strtolower(trim($szo));
    $is_en = preg_match('/^[a-zA-Z\-]+$/', $tisztitott);

    $szofaj = '';
    $tooltip = '';

    if ($is_en) {
        $szofaj = $szotar_en[$tisztitott] ?? '';
        if ($szofaj === '') {
            foreach ($suffixek as $veg => $info) {
                if (($info['lang'] ?? '') === 'en' && substr($tisztitott, -strlen($veg)) === $veg) {
                    $szofaj = $info['type'] ?? '';
                    break;
                }
            }
        }
        $tooltip = $tooltipek['en'][$szofaj] ?? '';
    } else {
        $szofaj = $szotar_hu[$tisztitott] ?? '';
        if ($szofaj === '') {
            foreach ($suffixek as $veg => $info) {
                if (($info['lang'] ?? '') === 'hu' && substr($tisztitott, -strlen($veg)) === $veg) {
                    $szofaj = $info['type'] ?? '';
                    break;
                }
            }
        }
        $tooltip = $tooltipek['hu'][$szofaj] ?? '';
    }

    return [
        'szofaj' => $szofaj,
        'tooltip' => $tooltip
    ];
}
