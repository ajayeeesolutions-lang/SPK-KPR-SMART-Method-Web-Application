<?php

function createIcon($size, $filename) {
    $img = imagecreatetruecolor($size, $size);
    // Warna background: #2563EB (Biru Bank)
    $bg = imagecolorallocate($img, 37, 99, 235);
    imagefill($img, 0, 0, $bg);
    
    // Warna Teks: Putih
    $text_color = imagecolorallocate($img, 255, 255, 255);
    
    // Tambah teks (simple, ga pakai font external biar gampang)
    $text = "KPR";
    $font_size = $size > 200 ? 5 : 4; 
    
    // Posisi teks di tengah
    $x = ($size / 2) - 15;
    $y = ($size / 2) - 10;
    
    imagestring($img, 5, ($size/2)-15, ($size/2)-10, "KPR", $text_color);
    imagestring($img, 5, ($size/2)-20, ($size/2)+5, "SMART", $text_color);
    
    imagepng($img, __DIR__ . '/../public/' . $filename);
    imagedestroy($img);
}

createIcon(192, 'pwa-icon-192.png');
createIcon(512, 'pwa-icon-512.png');

echo "Icons generated successfully.\n";
