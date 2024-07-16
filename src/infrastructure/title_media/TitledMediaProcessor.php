<?php

namespace infrastructure\title_media;

use infrastructure\MediaFiles;

class TitledMediaProcessor
{
    public function __construct(private MediaFiles $mediaFiles)
    {
    }

    public function getVideoId(string $url): ?string
    {
        $query = parse_url($url, PHP_URL_QUERY);
        parse_str($query, $params);
        if (array_key_exists("v", $params)) {
            return $params["v"];
        }
        return null;
    }

    /**
     * @throws \UnknownPathAliasException
     */
    public function process(?string $mediaUrl): ?string
    {
        if (empty($mediaUrl)) return null;

        if (str_starts_with($mediaUrl, 'https://www.youtube.com/')) {
            return $this->convertToEmbedUrl($mediaUrl);
        }
        else {
            return $this->mediaFiles->writePostTitledMedia($mediaUrl);
        }
    }

    public function convertToEmbedUrl(?string $url): ?string
    {
        $videoId = $this->getVideoId($url);
        return "https://www.youtube.com/embed/$videoId";
    }
}