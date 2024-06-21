<?php

namespace infrastructure\routing;

use infrastructure\Slug;

class Routing
{
    private static string $ForumCategoryId = '11';
    public function forumCategory($category): string
    {
        return url('/f/' . $category->slug);
    }

    public function forumThread($thread): string
    {
        return $this->forumThreadByTitleAndId($thread->title, $thread->id);
    }

    public function forumThreadByTitleAndId($title, $id): string
    {
        return url('/f/' . Slug::slugify($title) . ',' . $id);
    }

    public function parse($route): Route
    {
        $lastOccurrence = strrpos($route, ',');
        if ($lastOccurrence === false) {
            return new Route($route);
        }
        $slug = substr($route,0, $lastOccurrence);
        $id = substr($route, $lastOccurrence + 1);

        return new Route($slug, $id);
    }
}