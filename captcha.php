<?php
// 1️⃣ Start session (one-time)
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// 2️⃣ Define grid parameters
$cols       = 5;
$rows       = 4;
$totalTiles = $cols * $rows;
$tilesDir   = 'captchas/tiles/';
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Captcha loading…</title>
  <style>
    /* Reset & Base */
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family:sans-serif; }

    /* Splash container */
    .splash-container {
      position: fixed; top:0; left:0; right:0; bottom:0;
      display: flex; flex-direction: column;
      justify-content: center; align-items: center;
      background:white; z-index:9999;
    }
    .splash-logo { max-width:120px; margin-bottom:10px; }
    .splash-title { font-size:1.5rem; margin-bottom:8px; }
    .splash-text  { margin-bottom:12px; }
    .loader { display:flex; gap:6px; }
    .loader div {
      width:12px; height:12px; background:#333; opacity:0;
      animation: blink 1.2s infinite ease-in-out;
    }
    .loader div:nth-child(1){animation-delay:0s;}
    .loader div:nth-child(2){animation-delay:0.4s;}
    .loader div:nth-child(3){animation-delay:0.8s;}
    @keyframes blink {0%,100%{opacity:0;}50%{opacity:1;}}

    /* Captcha styles (coming later) */
    .captcha-grid {
      display:grid;
      grid-template-columns:repeat(5,1fr);
      gap:4px; width:100%; max-width:100%;
    }
    .captcha-grid label {
      position:relative; width:100%; aspect-ratio:1/1;
    }
    .captcha-grid img {
      width:100%; height:100%; object-fit:cover;
    }
    .captcha-grid input[type="checkbox"] {
      position:absolute; top:5px; left:5px; z-index:1;
    }
    .error-msg {
      color:red; font-weight:bold; margin-bottom:10px;
    }
  </style>
</head>
<body>

  <!-- 2️⃣ Splash screen in pure HTML -->
  <div class="splash-container" id="splash">
    <img src="data:image/png;base64,[Base64 code]" alt="Forum-Logo" class="splash-logo">
    <div class="splash-title">My forum</div>
    <div class="splash-text">Please wait…</div>
    <div class="loader">
      <div></div><div></div><div></div>
    </div>
  </div>

<?php
  // 3️⃣ Send everything up to this point to the browser
  flush();

  // 4️⃣ Generate Captcha tiles (may take some time)
  if (!isset($_SESSION['captcha_wrong'])) {
      $images     = glob('captchas/originals/*.jpg');
      $falseParts = glob('captchas/false_parts/*.jpg');
      $original   = $images[array_rand($images)];
      $img        = imagecreatefromjpeg($original);
      $img_w      = imagesx($img);
      $img_h      = imagesy($img);

      $tile_w = intval($img_w / $cols);
      $tile_h = intval($img_h / $rows);
      @mkdir($tilesDir, 0777, true);

      $allIndexes   = range(0, $totalTiles - 1);
      shuffle($allIndexes);
      $wrongIndexes = array_slice($allIndexes, 0, 4);
      $_SESSION['captcha_wrong'] = $wrongIndexes;

      for ($i = 0; $i < $totalTiles; $i++) {
          $x    = ($i % $cols) * $tile_w;
          $y    = floor($i / $cols) * $tile_h;
          $tile = imagecreatetruecolor($tile_w, $tile_h);

          if (in_array($i, $wrongIndexes, true)) {
              $falseImg = imagecreatefromjpeg($falseParts[array_rand($falseParts)]);
              imagecopyresampled($tile, $falseImg, 0, 0, 0, 0,
                $tile_w, $tile_h, imagesx($falseImg), imagesy($falseImg));
              imagedestroy($falseImg);
          } else {
              imagecopy($tile, $img, 0, 0, $x, $y, $tile_w, $tile_h);
          }

          imagejpeg($tile, "$tilesDir/tile_$i.jpg");
          imagedestroy($tile);
      }
      imagedestroy($img);
  }

  // 5️⃣ Hide splash
  echo '<style>#splash{display:none !important;}</style>';
?>

  <!-- 6️⃣ Captcha form -->
  <?php if (!empty($_SESSION['wrong_solved'])): ?>
    <div class="error-msg">Falsch gelöst. Versuche es erneut.</div>
    <?php unset($_SESSION['wrong_solved']); ?>
  <?php endif; ?>

  <form method="post" action="verify.php">
    <p>Please select <strong>four parts of the picture</strong> that <strong>do not match the picture</strong>:</p>
    <div class="captcha-grid">
      <?php for ($i = 0; $i < $totalTiles; $i++): ?>
        <label>
          <img src="captchas/tiles/tile_<?= $i ?>.jpg" alt="Tile <?= $i ?>">
          <input type="checkbox" name="selected[]" value="<?= $i ?>">
        </label>
      <?php endfor; ?>
    </div>
    <button type="submit" style="margin-top:10px;">Absenden</button>
  </form>

</body>
</html>