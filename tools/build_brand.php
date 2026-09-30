<?php
// Génère les déclinaisons du logo à partir de assets/img/brand/logo-source.png
$dir = __DIR__ . '/../assets/img/brand';
$src = imagecreatefrompng("$dir/logo-source.png");
function crop_square($src, $x, $y, $s, $size) {
    $dst = imagecreatetruecolor($size, $size);
    imagecopyresampled($dst, $src, 0, 0, $x, $y, $size, $size, $s, $s);
    return $dst;
}
// Emblème (anneau + flamme)
foreach ([640 => 'emblem-640', 256 => 'emblem-256', 96 => 'emblem-96'] as $size => $name) {
    $im = crop_square($src, 150, 32, 530, $size);
    imagepng($im, "$dir/$name.png", 9);
    if (function_exists('imagewebp')) imagewebp($im, "$dir/$name.webp", 88);
}
foreach ([32 => 'favicon-32', 180 => 'apple-touch-icon', 192 => 'icon-192', 512 => 'icon-512'] as $size => $name) {
    imagepng(crop_square($src, 150, 32, 530, $size), "$dir/$name.png", 9);
}
// Logo complet (emblème + SARGORA + slogan) en webp
$w = imagesx($src); $h = imagesy($src);
if (function_exists('imagewebp')) imagewebp($src, "$dir/logo-full.webp", 88);
$jpg = imagecreatetruecolor($w, $h); imagecopy($jpg, $src, 0, 0, 0, 0, $w, $h);
imagejpeg($jpg, "$dir/logo-full.jpg", 88);
// Image Open Graph 1200x630 : logo centré sur fond marine
$og = imagecreatetruecolor(1200, 630);
for ($i = 0; $i < 630; $i++) {
    $t = $i / 630;
    $c = imagecolorallocate($og, (int)(14 + 10*$t), (int)(20 + 8*$t), (int)(36 + 10*$t));
    imageline($og, 0, $i, 1200, $i, $c);
}
$th = 600; $tw = (int)round($w * $th / $h);
imagecopyresampled($og, $src, (int)((1200 - $tw) / 2), 15, 0, 0, $tw, $th, $w, $h);
imagejpeg($og, "$dir/og-image.jpg", 86);
echo "ok webp=" . (function_exists('imagewebp') ? 'yes' : 'no') . "\n";
