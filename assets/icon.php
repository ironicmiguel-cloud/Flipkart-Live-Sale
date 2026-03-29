<?php
$name = strtolower(preg_replace('/[^a-z0-9_-]/', '', $_GET['name'] ?? ''));
$base = __DIR__ . '/icons/' . $name;
$types = [
  'svg'  => 'image/svg+xml; charset=UTF-8',
  'png'  => 'image/png',
  'webp' => 'image/webp',
  'jpg'  => 'image/jpeg',
  'jpeg' => 'image/jpeg'
];
if ($name === '') {
  http_response_code(400);
  exit('Missing icon name');
}
foreach ($types as $ext => $ctype) {
  $file = $base . '.' . $ext;
  if (is_file($file)) {
    header('Content-Type: ' . $ctype);
    header('Cache-Control: public, max-age=31536000, immutable');
    header('Content-Length: ' . filesize($file));
    readfile($file);
    exit;
  }
}
http_response_code(404);
header('Content-Type: text/plain; charset=UTF-8');
echo 'Icon not found';
