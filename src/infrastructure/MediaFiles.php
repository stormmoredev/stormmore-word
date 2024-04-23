<?php

namespace infrastructure;

use Imagick;
use STORM, Exception;

readonly class MediaFiles
{
    public function writeAsProfile($photo, $name)
    {
        $filePath = STORM::aliasPath("@profile/$name");

        $imagick = new Imagick($photo->tmp);
        $origWidth = $imagick->getImageWidth();
        $origHeight = $imagick->getImageHeight();
        list($newWidth, $newHeight) = $this->calculateSize($origWidth, $origHeight, 200, 200);
        $imagick->thumbnailImage($newWidth, $newHeight);
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