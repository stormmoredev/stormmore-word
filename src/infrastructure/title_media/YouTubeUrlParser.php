<?php

namespace infrastructure\title_media;

class YouTubeUrlParser
{
    public function getVideoId(string $url): ?string
    {
        $query = parse_url($url, PHP_URL_QUERY);
        parse_str($query, $params);
        if (array_key_exists("v", $params)) {
            return $params["v"];
        }
        return null;
    }

    public function convertToEmbedUrl(?string $url): ?string
    {
        if (empty($url)) return "";

        $videoId = $this->getVideoId($url);
        return "https://www.youtube.com/embed/$videoId";
    }
}