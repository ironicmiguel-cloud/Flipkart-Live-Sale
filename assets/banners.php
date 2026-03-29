<?php
header('Content-Type: application/json; charset=UTF-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

$type = $_GET['type'] ?? 'slider';
$root = dirname(__DIR__);
$exts = ['jpg','jpeg','png','webp','avif'];

function versionedPath(string $folder, string $basename, string $root, array $exts): string {
    foreach ($exts as $ext) {
        $f = "$root/$folder/$basename.$ext";
        if (file_exists($f)) {
            $ver = @filemtime($f) ?: time();
            return "$folder/$basename.$ext?v=$ver";
        }
    }
    return "$folder/$basename.jpg";
}

if ($type === 'sponsored') {
    echo json_encode(['src' => versionedPath('Images/Sponsored', 'banner', $root, $exts)]);
    exit;
}


if ($type === 'supercoin') {
    foreach ($exts as $ext) {
        $f = "$root/Images/SuperCoin/banner.$ext";
        if (file_exists($f)) {
            $ver = @filemtime($f) ?: time();
            echo json_encode(['src' => "Images/SuperCoin/banner.$ext?v=$ver"]);
            exit;
        }
    }
    echo json_encode(['src' => '']);
    exit;
}

if ($type === 'adbanner') {
    echo json_encode(['src' => versionedPath('banners/ads', 'banner', $root, $exts)]);
    exit;
}

if ($type === 'midbanner') {
    foreach (['nirvana_banner', 'mid_banner', 'banner'] as $name) {
        foreach ($exts as $ext) {
            $f = "$root/banners/$name.$ext";
            if (file_exists($f)) {
                $ver = @filemtime($f) ?: time();
                echo json_encode(['src' => "banners/$name.$ext?v=$ver"]);
                exit;
            }
        }
    }
    echo json_encode(['src' => 'banners/nirvana_banner.webp']);
    exit;
}

$baseFolder = "$root/Images/BannerSlider";
$banners = [];
for ($i = 1; $i <= 6; $i++) {
    foreach ($exts as $ext) {
        $file = "$baseFolder/$i.$ext";
        if (file_exists($file)) {
            $ver = @filemtime($file) ?: time();
            $banners[] = ['src' => "Images/BannerSlider/$i.$ext?v=$ver", 'slot' => $i];
            break;
        }
    }
}
if (empty($banners)) {
    for ($i = 1; $i <= 5; $i++) $banners[] = ['src' => "Images/BannerSlider/$i.jpg", 'slot' => $i];
}
echo json_encode($banners);
