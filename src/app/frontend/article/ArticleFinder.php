<?php

namespace app\frontend\article;

use infrastructure\Database;
use infrastructure\settings\Settings;
use infrastructure\Slug;
use stdClass;

readonly class ArticleFinder
{
    public function __construct(
        private Database $database,
        private Slug     $slug,
        private Settings $settings
    ) { }

    public function find($language): array
    {
        $previewMaxChars = $this->settings->editorEntry->maxPreviewChars;
        $query =
            "SELECT e.id, e.title, substring(e.content, 0, $previewMaxChars) as content, e.published_at
            FROM entries AS e
            LEFT OUTER JOIN public.users u on u.id = author_id
            WHERE e.language = ? and e.type = 1
            ORDER BY e.published_at DESC";

        $articles = $this->database->fetch($query, $language);

        foreach ($articles as $article) {
            $pos = strpos($article->content, "</h3>");
            $article->content = substr($article->content, $pos + 5);
            $article->content = strip_tags($article->content);
            if (strlen($article->content) > $previewMaxChars) {
                $pos = strrpos($article->content, ' ');
                $article->content = substr($article->content, 0, $pos + 1);
            }
            $article->slug = $this->slug->article($article->id, $article->title);
        }

        return $articles;
    }

    public function getById(int $id): stdClass
    {
        $query =
            "SELECT a.id, a.title, a.content, a.published_at, u.name as author_name
            FROM entries AS a
            LEFT OUTER JOIN public.users u on u.id = author_id
            WHERE a.id = ?
            ORDER BY a.published_at DESC";

        return $this->database->fetchOne($query, $id);
    }
}