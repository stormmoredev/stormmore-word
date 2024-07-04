<?php

namespace infrastructure\title_media;

use IValidator;
use ValidatorResult;
use infrastructure\title_media\YouTubeUrlParser;

readonly class TitledMediaValidator implements IValidator
{
    public function __construct(private YouTubeUrlParser $youtubeUrlParser)
    {
    }

    function validate(mixed $value, string $name, array $data, mixed $args): ValidatorResult
    {
        if (empty($value)) return new ValidatorResult();

        if (str_starts_with($value, 'https://www.youtube.com/')) {
            $headers = get_headers($value);

            if (substr($headers[0], 9, 3) === "404")
                return new ValidatorResult(false, "Video not found");
        }

        return new ValidatorResult(false);
    }
}