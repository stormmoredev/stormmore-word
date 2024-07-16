<?php

namespace infrastructure;

use Imagick;
use ImagickException;
use UnknownPathAliasException;

readonly class MediaFiles
{
    /**
     * @throws ImagickException
     * @throws UnknownPathAliasException
     */
    public function writePostTitledMedia($mediaUrl): string
    {
        $dir = resolve_path_alias('@media');
        $filename = gen_unique_file_name(64, 'webp' , $dir);
        $filepath = concatenate_paths($dir, $filename);
        file_put_contents($filepath, fopen($mediaUrl, 'r'));

        $imagick = new Imagick($filepath);
        $imagick->cropThumbnailImage(256, 128);
        $imagick->writeImage($filepath);

        return $filename;
    }

    /**
     * @throws ImagickException
     * @throws UnknownPathAliasException
     */
    public function writeProfilePhoto($photo, $name): void
    {
        $filePath = resolve_path_alias("@profile/$name");

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