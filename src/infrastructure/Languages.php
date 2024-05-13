<?php

namespace infrastructure;

class Languages
{
    private array $languages = [];

    private function getLanguages(): array
    {
        if (!count($this->languages)) {
            $jsonFile = resolve_path_alias('@/translations/languages.json');
            $languages = file_get_contents($jsonFile);
            $this->languages = (array) json_decode($languages);
        }

        return $this->languages;
    }

    public function getList(array $keys = null): array
    {
        $list = [];
        foreach($this->getLanguages() as $key => $language)
        {
            if ($keys != null) {
                if (in_array($key, $keys)) {
                    $list[$key] = $language->name . " (" . $language->nativeName . ")";
                }
            } else {
                $list[$key] = $language->name . " (" . $language->nativeName . ")";
            }
        }

        return $list;
    }
}