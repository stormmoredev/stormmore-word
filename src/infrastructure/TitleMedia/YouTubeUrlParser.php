<?php

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
}