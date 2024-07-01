<?php

namespace infrastructure;

use Transliterator;

class Slug
{
    public static function slugify(string $title): string
    {
        $rules = ':: Any-Latin; :: Latin-ASCII; :: NFD; :: [:Nonspacing Mark:] Remove; :: Lower(); :: NFC;';
        $transliterate = Transliterator::createFromRules($rules, Transliterator::FORWARD);
        $slug = $transliterate->transliterate($title);
        $slug = preg_replace('/[^0-9a-zA-Z-_\s]/', "", $slug);
        $slug = trim($slug);
        return preg_replace('/\s+/', '-', $slug);
    }
}