<?php
$folderAlbume = __DIR__ . '/uploads';
$fisierDate = __DIR__ . '/albume.json';

if (!is_dir($folderAlbume)) {
    mkdir($folderAlbume, 0777, true);
}

$albume = file_exists($fisierDate)
    ? json_decode(file_get_contents($fisierDate), true)
    : [];

if (!is_array($albume)) {
    $albume = [];
}

$mesaj = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titlu = trim($_POST['titlu'] ?? '');
    $poze = $_FILES['poze'] ?? null;

    if ($titlu === '') {
        $mesaj = 'Scrie numele albumului.';
    } elseif (!$poze || empty($poze['name'][0])) {
        $mesaj = 'Alege cel puțin o fotografie.';
    } else {
        $idAlbum = date('YmdHis') . '-' . bin2hex(random_bytes(3));
        $caleAlbum = $folderAlbume . '/' . $idAlbum;
        mkdir($caleAlbum, 0777, true);

        $fotografii = [];
        $tipuriPermise = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/gif'  => 'gif',
            'image/webp' => 'webp'
        ];

        $verificare = finfo_open(FILEINFO_MIME_TYPE);

        foreach ($poze['tmp_name'] as $index => $caleTemporara) {
            if ($poze['error'][$index] !== UPLOAD_ERR_OK) {
                continue;
            }

            if ($poze['size'][$index] > 5 * 1024 * 1024) {
                continue;
            }

            $tip = finfo_file($verificare, $caleTemporara);

            if (!isset($tipuriPermise[$tip])) {
                continue;
            }

            $numePoza = bin2hex(random_bytes(8)) . '.' . $tipuriPermise[$tip];
            $destinatie = $caleAlbum . '/' . $numePoza;

            if (move_uploaded_file($caleTemporara, $destinatie)) {
                $fotografii[] = 'uploads/' . $idAlbum . '/' . $numePoza;
            }
        }

        finfo_close($verificare);

        if (count($fotografii) > 0) {
            $albume[] = [
                'titlu' => $titlu,
                'data' => date('d.m.Y H:i'),
                'fotografii' => $fotografii
            ];

            file_put_contents(
                $fisierDate,
                json_encode($albume, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
            );

            $mesaj = 'Albumul a fost creat!';
        } else {
            $mesaj = 'Nu s-au putut încărca fotografiile. Folosește imagini JPG, PNG, GIF sau WEBP de maximum 5 MB fiecare.';
        }
    }
}

function sigur($text) {
    return htmlspecialchars((string)$text, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Albume foto</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f3f5f9;
            color: #202433;
        }

        header {
            padding: 36px 20px;
            text-align: center;
            color: white;
            background: linear-gradient(135deg, #4f46e5, #9333ea);
        }

        header h1 {
            margin: 0 0 10px;
        }

        main {
            width: min(1000px, 92%);
            margin: 30px auto;
        }

        .formular, .album {
            padding: 22px;
            margin-bottom: 24px;
            background: white;
            border-radius: 14px;
            box-shadow: 0 5px 18px #18213b12;
        }

        label {
            display: block;
            margin: 14px 0 7px;
            font-weight: bold;
        }

        input[type="text"], input[type="file"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccd1dc;
            border-radius: 8px;
        }

        button {
            margin-top: 16px;
            padding: 12px 18px;
            border: 0;
            border-radius: 8px;
            background: #4f46e5;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background: #3730a3;
        }

        .mesaj {
            padding: 12px;
            border-radius: 8px;
            background: #eef2ff;
        }

        .galerie {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 12px;
            margin-top: 16px;
        }

        .galerie img {
            width: 100%;
            height: 170px;
            object-fit: cover;
            border-radius: 10px;
        }

        .data {
            color: #687084;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <header>
        <h1>Generator de albume foto</h1>
        <p>Adaugă fotografiile tale și organizează-le în albume.</p>
    </header>

    <main>
        <section class="formular">
            <h2>Creează un album</h2>

            <?php if ($mesaj !== ''): ?>
                <p class="mesaj"><?= sigur($mesaj) ?></p>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <label for="titlu">Numele albumului</label>
                <input
                    type="text"
                    id="titlu"
                    name="titlu"
                    placeholder="Exemplu: Vacanța la mare"
                    required
                >

                <label for="poze">Alege fotografiile</label>
                <input
                    type="file"
                    id="poze"
                    name="poze[]"
                    accept="image/jpeg,image/png,image/gif,image/webp"
                    multiple
                    required
                >

                <button type="submit">Creează albumul</button>
            </form>
        </section>

        <h2>Albumele mele</h2>

        <?php if (count($albume) === 0): ?>
            <p>Nu ai creat încă niciun album.</p>
        <?php else: ?>
            <?php foreach (array_reverse($albume) as $album): ?>
                <section class="album">
                    <h3><?= sigur($album['titlu']) ?></h3>
                    <p class="data">Creat la: <?= sigur($album['data']) ?></p>

                    <div class="galerie">
                        <?php foreach ($album['fotografii'] as $fotografie): ?>
                            <img
                                src="<?= sigur($fotografie) ?>"
                                alt="Fotografie din album"
                            >
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endforeach; ?>
        <?php endif; ?>
    </main>
</body>
</html>