<?php

namespace infrastructure\settings;

use STORM;
use stdClass;

class SettingsFile
{
    public function save($settings): void
    {
        $env = STORM::$instance->configuration->environment;
        if (STORM::$instance->configuration->isProduction()) {
            $path = "@/settings.json";
        } else {
            $path = "@/settings.$env.json";
        }
        $path = STORM::aliasPath($path);
        file_put_contents($path, json_encode($settings));
    }
}