<?php

namespace frontend\comments;

use infrastructure\Database;
use stdClass;

class CommentStorage
{
    public function __construct(
        private Database $database
    )
    {
    }

    public function save(stdClass $comment): int
    {
        $query = "INSERT INTO replies (author_id, article_id, content, is_approved) VALUES(?, ?, ?, ?)";
        $this->database->insert($query,
            $comment->author_id,
            $comment->article_id,
            $comment->content,
            $comment->is_approved);

        return $this->database->lastInsertedId();
    }
}