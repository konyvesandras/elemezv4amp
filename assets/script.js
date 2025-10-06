document.addEventListener('DOMContentLoaded', () => {
  // === Téma visszaállítása ===
  const toggleBtn = document.getElementById('theme-toggle');
  const savedTheme = localStorage.getItem('theme');
  if (savedTheme) {
    document.body.classList.remove('dark-mode', 'light-mode');
    document.body.classList.add(savedTheme);
  }

  // === Téma váltás + mentés ===
  toggleBtn.addEventListener('click', () => {
    document.body.classList.toggle('dark-mode');
    document.body.classList.toggle('light-mode');
    const current = document.body.classList.contains('dark-mode') ? 'dark-mode' : 'light-mode';
    localStorage.setItem('theme', current);
  });

  // === Szavak kattintás kezelése ===
  document.querySelectorAll('.word').forEach(el => {
    el.addEventListener('click', () => {
      const word = el.dataset.word;

      // 1. Elemzés lekérése az API-ból
      fetch(`api/szoveg.php?word=${encodeURIComponent(word)}`)
        .then(res => res.json())
        .then(data => {
          const panel = document.getElementById('analysis-content');
          if (data.error) {
            panel.textContent = data.error;
            return;
          }
          panel.textContent = `Szó: ${data.text} | Szófaj: ${data.szofaj} | Fordítás: ${data.forditas ?? 'nincs'}`;
        })
        .catch(err => {
          console.error(err);
          document.getElementById('analysis-content').textContent = 'Hiba a lekérdezésben';
        });

      // 2. Kiemelés toggle mentése az API-n keresztül
      fetch('api/highlight.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ word })
      })
      .then(res => res.json())
      .then(data => {
        if (data.status === 'added') {
          el.classList.add('highlighted');
        } else if (data.status === 'removed') {
          el.classList.remove('highlighted');
        }
      })
      .catch(err => console.error('Kiemelés mentési hiba:', err));
    });
  });
});
