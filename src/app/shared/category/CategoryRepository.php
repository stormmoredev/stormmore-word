<?php

namespace app\shared\category;

use infrastructure\Database;
use Request;

readonly class CategoryRepository
{
    public function __construct(
        private Database $database
    ) { }

    public function getBySlug(string $slug): ?object
    {
        $query = "SELECT * FROM categories WHERE slug = ?";
        return $this->database->fetchOne($query, $slug);
    }
}