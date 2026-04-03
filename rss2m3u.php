<?php
// File location: /var/www/rss2m3u.php

// --- Config ---
$FEEDS_FILE  = '/var/www/util/rss2m3u/rssfeeds.txt';
$SCRIPT      = '/var/www/util/rss2m3u/rss2m3u.sh';
$LOGFILE     = '/var/www/util/rss2m3u/log.txt';

// --- Actions ---
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'save') {
        $names = $_POST['name'] ?? [];
        $urls  = $_POST['url']  ?? [];
        $lines = [];
        foreach ($names as $i => $name) {
            $name = trim($name);
            $url  = trim($urls[$i] ?? '');
            if ($name !== '' && $url !== '') $lines[] = $name . ';' . $url;
        }
        if (file_put_contents($FEEDS_FILE, implode("\n", $lines) . "\n") !== false) {
            $message = 'Saved.';
            $messageType = 'ok';
        } else {
            $message = 'ERROR: Could not write to ' . $FEEDS_FILE;
            $messageType = 'err';
        }
    }
    if ($_POST['action'] === 'run') {
        shell_exec('bash ' . escapeshellarg($SCRIPT) . ' 2>&1');
        $message = 'Script executed — see log below.';
        $messageType = 'ok';
    }
}

$feeds = [];
if (file_exists($FEEDS_FILE)) {
    foreach (file($FEEDS_FILE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $parts = explode(';', $line, 2);
        if (count($parts) === 2) $feeds[] = ['name' => trim($parts[0]), 'url' => trim($parts[1])];
    }
}

$logLines = '';
if (file_exists($LOGFILE)) {
    $logLines = implode("\n", array_slice(file($LOGFILE, FILE_IGNORE_NEW_LINES), -40));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>rss2m3u</title>
<style>
  body { max-width:800px; margin:0 auto; padding:20px; font-family:monospace; font-size:13px; background:#111; color:#ccc; }
  h1 { font-size:15px; margin-bottom:4px; color:#fff; }
  h2 { font-size:12px; text-transform:uppercase; letter-spacing:.1em; color:#666; border-bottom:1px solid #222; padding-bottom:6px; margin:24px 0 12px; }
  input[type="text"] { background:#1a1a1a; border:1px solid #333; color:#ccc; font-family:monospace; font-size:12px; padding:5px 8px; border-radius:3px; }
  input[type="text"]:focus { outline:none; border-color:#00c896; }
  .row { display:grid; grid-template-columns:160px 1fr auto; gap:6px; margin-bottom:6px; }
  button { font-family:monospace; font-size:12px; padding:5px 12px; border:1px solid #333; border-radius:3px; cursor:pointer; background:#1a1a1a; color:#ccc; }
  button:hover { border-color:#00c896; color:#00c896; }
  .msg-ok  { color:#00c896; margin-bottom:12px; }
  .msg-err { color:#e05252; margin-bottom:12px; }
  .log { background:#0a0a0a; border:1px solid #222; padding:10px; font-size:11px; line-height:1.7; max-height:300px; overflow-y:auto; white-space:pre-wrap; color:#555; margin-top:8px; }
  .l-ok { color:#00c896; } .l-err { color:#e05252; } .l-sep { color:#444; }
  small { color:#555; }
</style>
</head>
<body>

<h1>rss2m3u</h1>
<small>RSS feed manager for moOde audio player</small>

<?php if ($message): ?>
  <p class="msg-<?= htmlspecialchars($messageType) ?>"><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<h2>RSS Feeds <small style="font-size:10px;"><?= $FEEDS_FILE ?></small></h2>

<form method="post">
  <input type="hidden" name="action" value="save">
  <div id="feedList">
<?php foreach ($feeds as $i => $feed): ?>
    <div class="row">
      <input type="text" name="name[]" value="<?= htmlspecialchars($feed['name']) ?>" placeholder="Name" required>
      <input type="text" name="url[]"  value="<?= htmlspecialchars($feed['url'])  ?>" placeholder="https://…" required style="width:100%">
      <button type="button" onclick="removeRow(this)">✕</button>
    </div>
<?php endforeach; ?>
  </div>
  <div style="margin-top:8px;display:flex;gap:8px;">
    <button type="button" onclick="addRow()">+ Add feed</button>
    <button type="submit">Save</button>
  </div>
</form>

<h2>Run Script <small style="font-size:10px;"><?= $SCRIPT ?></small></h2>
<p style="color:#555;margin-bottom:8px;font-size:12px;">
  Converts RSS feeds to .m3u playlists and triggers mpc update.<br>
  ⓘ Cover images must be added manually via moOde under Playlists.
</p>
<form method="post">
  <input type="hidden" name="action" value="run">
  <button type="submit">▶ Run now</button>
</form>

<h2>Log <small style="font-size:10px;"><?= $LOGFILE ?></small></h2>
<div class="log" id="logBox"><?php
  if ($logLines) {
      foreach (explode("\n", $logLines) as $line) {
          $esc = htmlspecialchars($line);
          if (str_starts_with($line, '--- Run')) echo '<span class="l-sep">' . $esc . '</span>' . "\n";
          elseif (str_contains($line, 'ERROR') || str_contains($line, 'Failed')) echo '<span class="l-err">' . $esc . '</span>' . "\n";
          elseif (str_contains($line, 'Success') || str_contains($line, 'finished')) echo '<span class="l-ok">' . $esc . '</span>' . "\n";
          else echo $esc . "\n";
      }
  } else echo '(no log yet)';
?></div>

<script>
let rowIndex = <?= count($feeds) ?>;
function addRow() {
  const list = document.getElementById('feedList');
  const div = document.createElement('div');
  div.className = 'row';
  div.innerHTML = `
    <input type="text" name="name[]" placeholder="Name" required>
    <input type="text" name="url[]" placeholder="https://…" required style="width:100%">
    <button type="button" onclick="removeRow(this)">✕</button>`;
  list.appendChild(div);
  div.querySelector('input').focus();
  rowIndex++;
}
function removeRow(btn) { btn.closest('.row').remove(); }
const lb = document.getElementById('logBox');
if (lb) lb.scrollTop = lb.scrollHeight;
</script>
</body>
</html>
