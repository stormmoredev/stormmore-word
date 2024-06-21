<?php

namespace infrastructure\FileSystem;

class CacheInformation
{
    public int $viewFilesNum;
    public float $viewFilesSize;

    public int $responseFilesNum = 0;

    public float $responseFilesSize;
}