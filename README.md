# Generator de albume cu fotografii

Aplicație web realizată în PHP pentru organizarea fotografiilor în albume.

## Funcționalități

- Crearea unui album cu nume și dată.
- Încărcarea mai multor fotografii într-un album.
- Acceptarea imaginilor JPG, PNG, GIF și WEBP.
- Afișarea albumelor și fotografiilor într-o galerie.
- Salvarea albumelor pe calculator în fișierul `albume.json`.

## Tehnologii

- PHP
- HTML
- CSS

## Pornirea aplicației

1. Deschide folderul proiectului în terminal.
2. Pornește serverul PHP:

   ```bash
   php -S localhost:9000

   ## Fișiere

- `index.php` — codul aplicației.
- `.gitignore` — exclude fotografiile și datele locale din GitHub.
- `uploads/` și `albume.json` — create automat la folosirea aplicației.