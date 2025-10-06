<?php
require_once __DIR__ . '/inc/render.php';
$lines = load_text('elemzes.txt');
?>
<!doctype html>
<html lang="hu">
<head>
  <meta charset="utf-8">
  <title>Szövegelemzés – interaktív</title>
  <link rel="stylesheet" href="assets/style.css">
  <script src="assets/script.js" defer></script>
</head>
<body class="dark-mode">
  <h1>Szövegelemzés – interaktív verzió</h1>

  <button id="theme-toggle">🌗 Dark / Light váltás</button>

  <div id="text">
    <?= render_text($lines) ?>
  </div>

  <div id="analysis-panel">
    <h2>Kijelölt szó elemzése</h2>
    <div id="analysis-content">Válassz ki egy szót!</div>
  </div>
</body>
</html>
