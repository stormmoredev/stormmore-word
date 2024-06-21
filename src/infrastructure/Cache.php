<?php

namespace infrastructure;

use AppConfiguration;

readonly class Cache
{
    public function __construct(
        private AppConfiguration $configuration,
    ) { }

    public function removeAll(): void
    {
        $viewsDir = concatenate_paths($this->configuration->cacheDir, 'views', "*.*");
        $responsesDir = concatenate_paths($this->configuration->cacheDir, 'responses', "*.*");

        unlink(concatenate_paths($this->configuration->cacheDir, 'routes'));
        unlink(concatenate_paths($this->configuration->cacheDir, '/classes'));
        array_map('unlink', glob($viewsDir));
        array_map('unlink', glob($responsesDir));
    }
}