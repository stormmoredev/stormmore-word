<?php

namespace app\frontend\forum;

use infrastructure\Database;

readonly class ForumRepository
{
    public function __construct(
        private Database $database
    ) { }

    public function addThread(int $category_id, string $title, string $slug, string $content, string $author_id): int
    {
        $query = "INSERT INTO entries (title, slug, language, content, author_id, type, category_id) 
                    VALUES(?, ?, ?, ?, ?, ?, ?)";
        return $this->database->insert($query, $title, $slug, 'en', $content, $author_id, 2, $category_id);
    }

    public function addPost(int $threadId, int $author_id, string $content): int
    {
        $query = "INSERT INTO replies (author_id, entry_id, content, is_approved) VALUES(?, ?, ?, ?)";
        $this->database->insert($query,
            $author_id,
            $threadId,
            $content,
            true);

        return $this->database->lastInsertedId();
    }

    public function updateRepliesCounterAndLastActivityTime(int $threadId): void
    {
        $query = "UPDATE entries 
                    SET replies = replies + 1, last_activity_at = now()
                    WHERE id = ?";
        $this->database->update($query, $threadId);
    }
}