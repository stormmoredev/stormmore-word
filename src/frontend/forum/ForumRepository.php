<?php

namespace frontend\forum;

use infrastructure\Database;

readonly class ForumRepository
{
    public function __construct(
        private Database $database
    ) { }

    public function addThread(string $title, string $content, string $lang, string $author_id): int
    {
        $query = "INSERT INTO entries (title, language, content, author_id, type) VALUES(?, ?, ?, ?, ?)";
        return $this->database->insert($query, $title, 'en', $content, $author_id, 2);
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

    public function incrementRepliesCounterAndRefreshIssueDate(int $threadId): void
    {
        $query = "UPDATE entries 
                    SET replies = replies + 1, issued_at = now()
                    WHERE id = ?";
        $this->database->update($query, $threadId);
    }
}