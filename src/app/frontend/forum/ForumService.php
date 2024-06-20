<?php

namespace app\frontend\forum;

use app\authentication\StormUser;
use infrastructure\settings\Settings;

readonly class ForumService
{
    public function __construct(
        private StormUser       $stormUser,
        private Settings        $settings,
        private ForumRepository $forumRepository)
    { }

    public function addThread(int $cid, string $title, string $content): int
    {
        return $this->forumRepository->addThread($cid, $title, $content, $this->stormUser->getId());
    }

    public function addPost(int $threadId, string $content): int
    {
        $this->forumRepository->incrementRepliesCounterAndRefreshIssueDate($threadId);
        return $this->forumRepository->addPost($threadId, $this->stormUser->getId(), $content);
    }
}