<?php

namespace app\backend\article;

use infrastructure\Database;

class ArticleFinder
{
    public function __construct(
        private Database $database
    ) { }

    public function find(): array
    {
        $query =
            "SELECT a.id, a.title, a.language, published_at, a.created_at, a.updated_at, u.name as author_name
            FROM entries AS a
            LEFT OUTER JOIN public.users u on u.id = author_id
            WHERE a.type = 1";
        return $this->database->fetch($query);
    }
}