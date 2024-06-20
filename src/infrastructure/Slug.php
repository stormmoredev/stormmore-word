<?php

namespace infrastructure;

use Transliterator;

class Slug
{
    public function article($id, $title): string
    {
        $slug = self::slugify($title);
        return $id . '-' . $slug;
    }

    public function getParameters(string $slug): array
    {
        return array_slice(explode('-', $slug), 0);
    }

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