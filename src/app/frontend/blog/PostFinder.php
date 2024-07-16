<?php

namespace app\frontend\blog;

use infrastructure\Database;
use infrastructure\settings\Settings;
use stdClass;

readonly class PostFinder
{
    public function __construct(
        private Database $database,
        private Settings $settings
    ) { }

    public function findPosts($language): array
    {
        $previewMaxChars = $this->settings->editorEntry->maxPreviewChars;
        $query =
            "SELECT e.id, e.title, e.subtitle, e.titled_media, e.slug, substring(e.content, 0, $previewMaxChars) as content,
                e.last_activity_at, u.photo as profile, u.name as username, e.created_at, e.replies_num, e.votes_num
            FROM entries AS e
            LEFT JOIN users u on u.id = author_id
            WHERE e.language = ? and e.type = 1
            ORDER BY e.last_activity_at DESC";

        return $this->database->fetch($query, $language);
    }

    public function getBySlug(string $slug): stdClass
    {
        $query =
            "SELECT e.id, e.title, e.slug, e.votes_num , e.titled_media, e.content, e.published_at, u.name as author_name
            FROM entries AS e
            LEFT JOIN public.users u on u.id = author_id
            WHERE e.slug = ?
            ORDER BY e.published_at DESC";

        return $this->database->fetchOne($query, $slug);
    }

    public function findComments(string $postSlug): array
    {
        $query =
            "SELECT r.id, r.content, r.created_at, u.name as author_name, u.photo as author_photo
            FROM replies AS r
            LEFT JOIN users u on u.id = author_id
            LEFT JOIN entries e on e.id = r.entry_id
            WHERE e.slug = ? and r.is_approved = true and r.is_deleted = false
            ORDER BY r.created_at DESC";

        return $this->database->fetch($query, $postSlug);
    }
}