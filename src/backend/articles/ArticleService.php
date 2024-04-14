<?php

namespace backend;

use authentication\StormUser;
use DateTime;

readonly class ArticleService
{
    function __construct(
        private StormUser  $user,
        private ArticleStore $articleStore
    ) {}

    function save(object $article)
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
            return $this->articleStore->insert($article);
        }
    }

    function setPublishStatus(int $id, bool $isPublished): void
    {
        $this->articleStore->setPublishStatus($id, $isPublished);
    }
}


