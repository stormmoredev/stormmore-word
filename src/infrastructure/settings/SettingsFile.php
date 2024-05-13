<?php

namespace infrastructure\settings;

class SettingsFile
{
    public function save($settings): void
    {
        $env = App::getInstance()->configuration->environment;
        if (App::getInstance()->configuration->isProduction()) {
            $path = "@/settings.json";
        } else {
            $path = "@/settings.$env.json";
        }
        $path = resolve_path_alias($path);
        file_put_contents($path, json_encode($settings));
    }
}