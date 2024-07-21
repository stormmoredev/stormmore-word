<?php

namespace app\frontend\blog\domain;

use app\shared\domain\EntryRepository;
use DateTime;
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
        $query = "INSERT INTO entries (title, subtitle, titled_media, slug, content, language, author_id, type, published_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $this->database->insert($query,
            $post->title,
            $post->subtitle,
            $post->media,
            $post->slug,
            $post->content,
            $post->language,
            $post->author_id,
            1,
            new DateTime());
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
}