<?php

namespace frontend;

use stdClass;
use infrastructure\Database;
use infrastructure\settings\Settings;
use infrastructure\Slug;

readonly class ArticlesFinder
{
    public function __construct(
        private Database $database,
        private Slug     $slug,
        private Settings $settings
    )
    {
    }

    public function find($language): array
    {
        $previewMaxChars = $this->settings->editorEntry->maxPreviewChars;
        $query =
            "SELECT a.id, a.title, substring(a.content, 0, $previewMaxChars) as content, a.published_at
            FROM articles AS a
            LEFT OUTER JOIN public.users u on u.id = author_id
            WHERE a.language = ?
            ORDER BY a.published_at DESC";

        $articles = $this->database->fetch($query, $language);

        foreach ($articles as $article) {
            $pos = strpos($article->content, "</h3>");
            $article->content = substr($article->content, $pos + 5);
            $article->content = strip_tags($article->content);
            $pos = strrpos($article->content, '.');
            $article->content = substr($article->content, 0, $pos + 1);
            $article->slug = $this->slug->build($article->title, [$article->id]);
        }

        return $articles;
    }

    public function findOne(int $id): stdClass
    {
        $query =
            "SELECT a.id, a.title, a.content, a.published_at, u.name as author_name
            FROM articles AS a
            LEFT OUTER JOIN public.users u on u.id = author_id
            WHERE a.id = ?
            ORDER BY a.published_at DESC";

        return $this->database->fetchOne($query, $id);
    }
}