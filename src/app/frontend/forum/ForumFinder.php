<?php

namespace app\frontend\forum;

use infrastructure\Database;

readonly class ForumFinder
{
    public function __construct(
        private Database $database
    ) { }

    public function listThreads(?string $slug = null): array
    {
        $args = array();
        $query =
            "SELECT e.id, e.title, e.slug, e.replies_num, e.last_activity_at, e.created_at, e.updated_at, u.name as author_name
            FROM entries AS e
            LEFT OUTER JOIN public.users u on u.id = author_id
            LEFT OUTER JOIN categories c on c.id = category_id
            WHERE e.type = 2 and c.is_deleted = false AND e.is_deleted = false";
        if ($slug !== null) {
            $args[] = $slug;
            $query .= " AND c.slug = ?";
        }
        $query .=  " ORDER BY e.last_activity_at DESC";
        return $this->database->fetchArgs($query, $args);
    }

    public function listCategories(): array
    {
        $query = "SELECT * FROM categories WHERE type = 2 AND is_deleted = false ORDER BY sequence ASC";
        return $this->database->fetch($query);
    }

    public function getCategoryBySlug(string $slug): ?object
    {
        $query = "SELECT * FROM categories WHERE slug = ? AND type = 2 AND is_deleted = false";
        return $this->database->fetchOne($query, $slug);
    }

    public function getCategoryById(int $id): ?object
    {
        $query = "SELECT * FROM categories WHERE id = ? AND type = 2 AND is_deleted = false";
        return $this->database->fetchOne($query, $id);
    }

    public function getThreadById(int $id): ?object
    {
        $query = "SELECT * FROM entries WHERE id = ? AND type = 2 AND is_deleted = false";
        return $this->database->fetchOne($query, $id);
    }
    public function getThreadBySlug(string $slug): ?object
    {
        $query =
            "SELECT e.id, e.title, e.content, e.published_at, u.name as author_name
            FROM entries AS e
            LEFT OUTER JOIN public.users u on u.id = author_id
            WHERE e.slug = ?
            ORDER BY e.published_at DESC";

        return $this->database->fetchOne($query, $slug);
    }

    public function listReplies(string $slug): array
    {
        $query =
            "SELECT r.id, r.content, r.created_at, u.name as author_name, u.photo as author_photo
            FROM replies AS r
            LEFT JOIN users u on u.id = author_id
            LEFT JOIN entries e ON e.id = r.entry_id
            WHERE e.slug = ? and r.is_approved = true and r.is_deleted = false
            ORDER BY r.created_at ASC";

        return $this->database->fetch($query, $slug);
    }
}