<?php

namespace frontend;

use infrastructure\Database;

readonly class CommentFinder
{
    public function __construct(
        private Database $database
    )
    {
    }

    public function find($articleId): array
    {
        $query =
            "SELECT r.id, r.content, r.created_at, u.name as author_name
            FROM replies AS r
            LEFT JOIN users u on u.id = author_id
            WHERE article_id = ? and is_approved = true and is_deleted = false
            ORDER BY r.created_at DESC";

        return $this->database->fetch($query, $articleId);
    }
}