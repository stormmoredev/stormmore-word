<?php

namespace app\backend\article;

use app\authentication\StormUser;
use DateTime;

readonly class ArticleService
{
    function __construct(
        private StormUser  $user,
        private ArticleStorage $articleStore
    ) {}

    function save(object $article): void
    {
        if ($article->id)
        {
            $article->title;
            $article->content;
            $this->articleStore->update($article);
        }
        else
        {
            $article->author_id = $this->user->id;
            $article->updatedAt = new DateTime();
            $article->language = $this->user->language->primary;
            $article->id = $this->articleStore->insert($article);
        }
    }

    function setPublishStatus(int $id, bool $isPublished): void
    {
        $this->articleStore->setPublishStatus($id, $isPublished);
    }
}


