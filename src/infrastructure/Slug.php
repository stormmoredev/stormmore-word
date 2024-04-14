<?php

namespace infrastructure;

use Transliterator;

class Slug
{
    public function build(string $title, array $parameters): string
    {
        $rules = ':: Any-Latin; :: Latin-ASCII; :: NFD; :: [:Nonspacing Mark:] Remove; :: Lower(); :: NFC;';
        $transliterator = Transliterator::createFromRules($rules, Transliterator::FORWARD);
        $title = $transliterator->transliterate($title);
        $title = preg_replace('/[^0-9a-zA-Z-_\s]/', "", $title);
        $title = trim($title);
        $title = preg_replace('/\s+/', '-', $title);
        foreach($parameters as$val) {
            $title .= "," . $val;
        }

        return $title;
    }

    public function getParameters(string $slug): array
    {
        return array_slice(explode(',', $slug), 1);
    }
}