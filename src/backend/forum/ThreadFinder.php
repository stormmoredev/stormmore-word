<?php

namespace backend\forum;

use infrastructure\Database;

readonly class ThreadFinder
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
            WHERE a.type = 2
            ORDER BY updated_at DESC";
        return $this->database->fetch($query);
    }
}