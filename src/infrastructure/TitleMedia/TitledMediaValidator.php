<?php

namespace infrastructure\TitleMedia;

use IValidator;
use ValidatorResult;
use YouTubeUrlParser;

readonly class TitledMediaValidator implements IValidator
{
    public function __construct(private YouTubeUrlParser $youtubeUrlParser)
    {
    }

    function validate(mixed $value, string $name, array $data, mixed $args): ValidatorResult
    {
        if (empty($value)) return new ValidatorResult();

        if (str_starts_with($value, 'https://www.youtube.com/')) {
            $videoId = $this->youtubeUrlParser->getVideoId($value);
            return new ValidatorResult();
        }

        return new ValidatorResult();
    }
}