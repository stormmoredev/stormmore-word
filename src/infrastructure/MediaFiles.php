<?php

namespace infrastructure;

use Imagick;
use STORM, Exception;

readonly class MediaFiles
{
    public function writeProfilePhoto($photo, $name): void
    {
        $filePath = STORM::aliasPath("@profile/$name");

        $imagick = new Imagick($photo->tmp);
        $imagick->cropThumbnailImage(200, 200);
        $imagick->writeImage($filePath);

        $photo->delete();
    }

    /**
     * @param int $w
     * @param int $h
     * @param int $maxW
     * @param int $maxH
     * @return int[]
     */
    private function calculateSize(int $w, int $h, int $maxW, int $maxH): array
    {
        $widthRatio = $maxW / $w;
        $heightRatio = $maxH / $h;
        $ratio = min($widthRatio, $heightRatio);
        $newWidth  =  intval($w  * $ratio);
        $newHeight =  intval($h * $ratio);

        return array($newWidth, $newHeight);
    }
}