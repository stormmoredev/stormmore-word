<?php

namespace infrastructure\routing;

class Route
{
    public function __construct(
        public string $slug,
        public ?int $id = null) { }

    public function isCategory(): bool
    {
        return $this->id === null;
    }

    public function isEntry(): bool
    {
        return $this->id !== null;
    }
}