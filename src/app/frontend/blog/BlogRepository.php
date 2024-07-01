<?php

namespace app\frontend\blog;

use app\shared\EntryRepository;
use infrastructure\Database;

readonly class BlogRepository extends EntryRepository
{
    public function __construct(
        private Database $database)
    {
        parent::__construct($this->database);
    }

    public function insertPost(object $post): int
    {
        $query = "INSERT INTO entries (title, subtitle, slug, content, language, author_id, type) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
        $this->database->insert($query,
            $post->title,
            $post->subtitle,
            $post->slug,
            $post->content,
            $post->language,
            $post->author_id, 1);
        return $this->database->lastInsertedId();
    }

    public function insertComment(object $comment): int
    {
        $query = "INSERT INTO replies (author_id, entry_id, content, is_approved) VALUES(?, ?, ?, ?)";
        $this->database->insert($query,
            $comment->author_id,
            $comment->article_id,
            $comment->content,
            $comment->is_approved);

        return $this->database->lastInsertedId();
    }

    public function insertMediaTitle(int $entryId, string $url): int
    {
        $query = "INSERT INTO entry_title_media (entry_id, url) VALUES(?, ?)";
        return $this->database->insert($query, $entryId, $url);
    }
}