<?php

namespace infrastructure\title_media;

use IValidator;
use ValidatorResult;

readonly class TitledMediaValidator implements IValidator
{
    public function __construct(private TitledMediaProcessor $youtubeUrlParser)
    {
    }

    function validate(mixed $value, string $name, array $data, mixed $args): ValidatorResult
    {
        $url = $value;
        if (empty($url)) return new ValidatorResult();

        if (filter_var($url, FILTER_VALIDATE_URL) === FALSE) {
            return new ValidatorResult(false, _("Media url is not a valid URL"));
        }

        if (str_starts_with($url, 'https://www.youtube.com/')) {
            $id = $this->youtubeUrlParser->getVideoId($url);
            $oembed = 'https://www.youtube.com/oembed?format=json&url=https://www.youtube.com/watch?v=' . $id;
            $headers = get_headers($oembed);
            if (substr($headers[0], 9, 3) !== "200")
                return new ValidatorResult(false, _("YouTube video doesn't exist"));
        }
        else
        {
            $headers = get_headers($url);
            if (substr($headers[0], 9, 3) !== "200")
                return new ValidatorResult(false, _("Url is not a valid image URL"));

            $type = exif_imagetype($url);
            if ($type === false)
                return new ValidatorResult(false, _("Url is not a valid image URL"));
            if ($type != IMAGETYPE_JPEG && $type != IMAGETYPE_PNG && $type != IMAGETYPE_WEBP)
                return new ValidatorResult(false, "JPEG, PNG and WEBP files are supported");
        }

        return new ValidatorResult();
    }
}