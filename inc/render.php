<?php
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/karaktercsere.php';



function render_text($lines) {
    $highlights = load_highlights();
	
    $html = '';
    foreach ($lines as $line) {
		$line=karaktercsere_folio($line);
        $words = preg_split('/\s+/', $line);
        $html .= "<p>";
        foreach ($words as $w) {
            $info = analyze_word($w);
            $highlighted = in_array($info['text'], $highlights, true);

            // A nem-AMP oldalon minden szó span, hogy kattintható legyen
            $classes = "word";
            if ($highlighted) $classes .= " highlighted";

            $title = htmlspecialchars($info['tooltip']);
            $html .= "<span class=\"$classes\" title=\"$title\" data-word=\"{$info['text']}\">";
            $html .= htmlspecialchars($info['text']);
            if (!empty($info['forditas'])) {
                $html .= " <span class=\"forditas\">" . htmlspecialchars($info['forditas']) . "</span>";
            }
            $html .= "</span> ";
        }
        $html .= "</p>\n";
    }
    return $html;
}
