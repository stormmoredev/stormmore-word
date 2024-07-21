<?php

namespace app\frontend\blog\presentation;

use app\frontend\blog\presentation\Dto\PostDto;
use app\frontend\blog\presentation\Dto\PostItemDto;
use app\shared\presentation\UserProfileDto;
use infrastructure\Database;
use infrastructure\settings\Settings;
use Mapper;

readonly class PostFinder
{
    public function __construct(
        private Database $database,
        private Settings $settings
    ) { }

    /**
     * @return PostItemDto[]
     */
    public function findPosts(): array
    {
        $query =
            "SELECT e.slug, e.title, e.titled_media, e.subtitle, e.replies_num, e.votes_num, e.published_at,
                u.photo as photo, u.name as name, u.slug as u_slug
            FROM entries AS e
            LEFT JOIN users u on u.id = author_id
            WHERE e.type = 1
            ORDER BY e.last_activity_at DESC";

        $results = $this->database->fetch($query);

        $posts = [];
        foreach($results as $result) {
            $author = new UserProfileDto();
            Mapper::map($result, $author, [
                'name',
                'photo',
                'u_slug' => 'slug']);
            $post = new PostItemDto();
            Mapper::map($result, $post,
                ['slug', 'title', 'titled_media', 'subtitle', 'replies_num', 'votes_num', 'published_at']);
            $post->author = $author;
            $posts[] = $post;
        }

        return $posts;
    }

    public function getBySlug(string $slug): PostDto
    {
        $query =
            "SELECT e.id, e.title, e.subtitle, e.votes_num , e.replies_num, e.content, e.published_at, 
                u.name as name, u.photo as photo, u.entries_num, u.followers_num, u.slug as u_slug
            FROM entries AS e
            LEFT JOIN public.users u on u.id = author_id
            WHERE e.slug = ?
            ORDER BY e.published_at DESC";

        $result =  $this->database->fetchOne($query, $slug);

        $author = new UserProfileDto();
        Mapper::map($result, $author, [
            'name',
            'photo',
            'u_slug' => 'slug',
            'entries_num',
            'followers_num']);
        $post = new PostDto();
        Mapper::map($result, $post, [
            'id', 'title', 'subtitle', 'votes_num', 'replies_num', 'content', 'published_at'
        ]);
        $post->author = $author;

        return $post;
    }
}