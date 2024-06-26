<?php

namespace app\backend\article;

use DateTime;
use infrastructure\Database;

readonly class ArticleStorage
{
    public function __construct(
        private Database $database
    ) { }

    public function find(int $id): object
    {
        $query = "SELECT id, title, content, is_published from entries WHERE id = ?";
        return $this->database->fetchOne($query, $id);
    }

    public function insert(object $article): int
    {
        $query = "INSERT INTO entries (title, language, content, author_id, type) 
                VALUES(?, ?, ?, ?, ?)";
        $this->database->insert($query,$article->title, $article->language, $article->content, $article->author_id, 1);
        return $this->database->lastInsertedId();
    }

    public function update(object $article): void
    {
        $query =
            "UPDATE entries 
            SET title = ?, content = ?, updated_at = ?
            WHERE id = ?";
        $this->database->update($query, $article->title, $article->content, new DateTime(), $article->id);
    }

    public function setPublishStatus(int $id, bool $isPublished = false): void
    {
        $publishedAt = $isPublished ? new DateTime() : null;
        $query =
            "UPDATE entries 
            SET is_published = ?, published_at = ? 
            WHERE id = ?";
        $this->database->update($query, $isPublished, $publishedAt, $id);
    }
}