<?php

namespace app\backend\forum;

use infrastructure\Category;
use infrastructure\Database;

readonly class ForumCategoryFinder
{
    public function __construct(
        private Database $database
    ) { }

    public function find(): array
    {
        $query = "SELECT * FROM categories AS c 
                    WHERE c.type = 2 AND is_deleted = false
                    ORDER BY sequence ASC";
        return $this->database->fetch($query);
    }
}