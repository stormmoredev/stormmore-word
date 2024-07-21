<?php

namespace app\shared\presentation;

use infrastructure\Database;
use Mapper;

readonly class ReplyFinder
{
    public function __construct(private Database $database)
    {
    }

    /**
     * @param string $slug
     * @return ReplyDto[]
     */
    public function find(string $slug): array
    {
        $query =
            "SELECT r.id, r.content, r.created_at, 
                u.name as name, u.photo as photo, u.slug as slug
            FROM replies AS r
            LEFT JOIN users u on u.id = author_id
            LEFT JOIN entries e on e.id = r.entry_id
            WHERE e.slug = ? and r.is_approved = true and r.is_deleted = false
            ORDER BY r.created_at DESC";

        $results = $this->database->fetch($query, $slug);

        $replies = [];
        foreach ($results as $result) {
            $author = new UserProfileDto();
            Mapper::map($result, $author, [
                'name',
                'photo',
                'slug']);

            $reply = new ReplyDto();
            $reply->author = $author;
            Mapper::map($result, $reply,  [
                'id',
                'content',
                'created_at'
            ]);
            $replies[] = $reply;
        }

        return $replies;
    }
}