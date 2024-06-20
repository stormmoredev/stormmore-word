<?php

namespace app\frontend\forum;

use infrastructure\Database;

readonly class ForumFinder
{
    public function __construct(
        private Database $database
    ) { }

    public function listThreads(?int $cid = null): array
    {
        $args = array();
        $query =
            "SELECT a.id, a.title, replies, issued_at, a.created_at, a.updated_at, u.name as author_name
            FROM entries AS a
            LEFT OUTER JOIN public.users u on u.id = author_id
            WHERE type = 2";
        if ($cid !== null) {
            $args[] = $cid;
            $query .= " AND a.category_id = ?";
        }
        $query .=  " ORDER BY issued_at DESC";
        return $this->database->fetchArgs($query, $args);
    }

    public function listCategories(): array
    {
        $query = "SELECT * FROM categories WHERE type = 2 AND is_deleted = false ORDER BY sequence ASC";
        return $this->database->fetch($query);
    }

    public function getCategoryById($id): ?object
    {
        $query = "SELECT * FROM categories WHERE id = ? AND type = 2 AND is_deleted = false";
        return $this->database->fetchOne($query, $id);
    }

    public function getThreadById(int $id): object
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