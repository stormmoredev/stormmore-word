<?php

namespace infrastructure\file_system;

use AppConfiguration;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

readonly class Cache
{
    private string $viewCacheDir;
    private string $responseCacheDir;

    public function __construct(
        private AppConfiguration $configuration,
    )
    {
        $this->viewCacheDir = concatenate_paths(getcwd(), $this->configuration->cacheDir, 'views');
        $this->responseCacheDir = concatenate_paths(getcwd(), $this->configuration->cacheDir, 'responses');
    }

    public function removeAll(): void
    {
        $viewsDirPattern = concatenate_paths($this->viewCacheDir, "*.*");
        $responsesDirPattern = concatenate_paths($this->responseCacheDir, "*.*");

        unlink(concatenate_paths($this->configuration->cacheDir, 'routes'));
        unlink(concatenate_paths($this->configuration->cacheDir, '/classes'));
        array_map('unlink', glob($viewsDirPattern));
        array_map('unlink', glob($responsesDirPattern));
    }

    public function getCacheFilesInformation(): CacheInformation
    {
        $info = new CacheInformation();

        list($num, $size) = $this->directorySize($this->responseCacheDir);
        $info->responseFilesNum = $num;
        $info->responseFilesSize = round($size / (1024 * 1024), 2);

        list($num, $size) = $this->directorySize($this->viewCacheDir);
        $info->viewFilesNum = $num;
        $info->viewFilesSize = round($size / (1024 * 1024), 2);

        return $info;
    }

    private function directorySize($directory): array
    {
        if (!is_dir($directory)) {
            return [0,0];
        }

        $num = 0;
        $size = 0;
        foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory)) as $file){
            $size += $file->getSize();
            $num++;
        }
        return [$num, $size];
    }
}