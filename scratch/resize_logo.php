<?php
$sourcePath = 'D:\PROJECT TEMPLATE JUAL\SK SMART NEW APP\resources\image\kpr automation.png';
$publicPath = 'D:\PROJECT TEMPLATE JUAL\SK SMART NEW APP\public\\';

function resizeImage($source, $dest, $targetSize) {
    list($origWidth, $origHeight, $type) = getimagesize($source);
    
    // Create new image from source
    if ($type === IMAGETYPE_PNG) {
        $srcImage = imagecreatefrompng($source);
    } elseif ($type === IMAGETYPE_JPEG) {
        $srcImage = imagecreatefromjpeg($source);
    } else {
        die("Unsupported image type");
    }

    $destImage = imagecreatetruecolor($targetSize, $targetSize);
    
    // Preserve transparency for PNG
    imagealphablending($destImage, false);
    imagesavealpha($destImage, true);
    $transparent = imagecolorallocatealpha($destImage, 255, 255, 255, 127);
    imagefilledrectangle($destImage, 0, 0, $targetSize, $targetSize, $transparent);

    // Calculate crop/resize to fit squarely without stretching if possible, or just resample
    // We will just do a simple resample for now, assuming the logo is somewhat square or can be squashed/padded.
    // Actually, padding it to a square is much better so it doesn't squish.
    $ratio = min($targetSize / $origWidth, $targetSize / $origHeight);
    $newWidth = $origWidth * $ratio;
    $newHeight = $origHeight * $ratio;
    
    $posX = ($targetSize - $newWidth) / 2;
    $posY = ($targetSize - $newHeight) / 2;

    imagecopyresampled($destImage, $srcImage, $posX, $posY, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);

    imagepng($destImage, $dest);
    imagedestroy($srcImage);
    imagedestroy($destImage);
}

resizeImage($sourcePath, $publicPath . 'pwa-icon-192.png', 192);
resizeImage($sourcePath, $publicPath . 'pwa-icon-512.png', 512);

echo "Icons updated successfully with the provided logo.\n";
