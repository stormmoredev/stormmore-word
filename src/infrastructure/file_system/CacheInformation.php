<?php

namespace infrastructure\file_system;

class CacheInformation
{
    public int $viewFilesNum;
    public float $viewFilesSize;

    public int $responseFilesNum = 0;

    public float $responseFilesSize;
}