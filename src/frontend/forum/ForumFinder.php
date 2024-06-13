<?php

namespace frontend\forum;

use infrastructure\Database;

readonly class ForumFinder
{
    public function __construct(
        private Database $database
    ) { }

    public function listThreads(): array
    {
        $query =
            "SELECT a.id, a.title, replies, issued_at, a.created_at, a.updated_at, u.name as author_name
            FROM entries AS a
            LEFT OUTER JOIN public.users u on u.id = author_id
            WHERE a.type = 2
            ORDER BY issued_at DESC";
        return $this->database->fetch($query);
    }

    public function getById(int $id): object
    {
        $query =
            "SELECT a.id, a.title, a.content, a.published_at, u.name as author_name
            FROM entries AS a
            LEFT OUTER JOIN public.users u on u.id = author_id
            WHERE a.id = ?
            ORDER BY a.published_at DESC";

        return $this->database->fetchOne($query, $id);
    }

    public function listReplies(int $threadId): array
    {
        $query =
            "SELECT r.id, r.content, r.created_at, u.name as author_name, u.photo as author_photo
            FROM replies AS r
            LEFT JOIN users u on u.id = author_id
            WHERE entry_id = ? and is_approved = true and is_deleted = false
            ORDER BY r.created_at ASC";

        return $this->database->fetch($query, $threadId);
    }
}