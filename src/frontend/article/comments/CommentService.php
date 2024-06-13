<?php

namespace frontend\article\comments;

use infrastructure\settings\Settings;
use stdClass;

readonly class CommentService
{
    public function __construct(
        private CommentStorage $commentStore,
        private Settings       $settings)
    {
    }

    public function add($userId, $articleId, $content): ?int
    {
        if (!$this->settings->comments->enabled) return null;
        if (strip_tags($content) > $this->settings->comments->maxChars) return null;

        $approval = !$this->settings->comments->approvalIsRequired;
        $comment = new stdClass();
        $comment->author_id = $userId;
        $comment->article_id = $articleId;
        $comment->content = $content;
        $comment->is_approved = $approval;

        return $this->commentStore->save($comment);
    }
}