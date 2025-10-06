<?php
// 🧾 Egy sor hozzáadása a logfájlhoz
function log_hozzaad($uzenet) {
    $idopont = date('Y-m-d H:i:s');
    $sor = "[$idopont] $uzenet\n";
    file_put_contents('./log/nyelvtan.log', $sor, FILE_APPEND);
}
